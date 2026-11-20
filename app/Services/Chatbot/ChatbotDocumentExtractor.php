<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;
use SimpleXMLElement;

class ChatbotDocumentExtractor
{
    private const MAX_FILE_BYTES = 314572800; // 300 MB
    private const MAX_PDF_PAGES = 400;
    private const MAX_OCR_PAGES = 10;
    private const PROCESS_TIMEOUT = 600;

    /**
     * Extract text from a document stored on an approved local Laravel disk.
     */
    public function extract(string $disk, string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($extension, ['pdf', 'docx', 'xlsx', 'pptx', 'txt'], true)) {
            return $this->failure('unsupported', 'This document format is not supported.');
        }

        try {
            $absolutePath = $this->resolveLocalFile($disk, $path);

            if (filesize($absolutePath) > self::MAX_FILE_BYTES) {
                return $this->failure('too_large', 'Document exceeds the 300 MB extraction limit.');
            }

            return match ($extension) {
                'pdf' => $this->extractPdf($absolutePath),
                'docx' => $this->extractDocx($absolutePath),
                'xlsx' => $this->extractXlsx($absolutePath),
                'pptx' => $this->extractPptx($absolutePath),
                'txt' => $this->extractPlainText($absolutePath),
            };
        } catch (Throwable $e) {
            report($e);

            return $this->failure('failed', 'Document extraction failed. Check the server log.');
        }
    }

    private function resolveLocalFile(string $disk, string $path): string
    {
        if (!in_array($disk, ['local', 'public'], true)) {
            throw new \RuntimeException('Unapproved storage disk.');
        }

        if ($path === '' || str_contains($path, "\0")) {
            throw new \RuntimeException('Invalid document path.');
        }

        $storage = Storage::disk($disk);
        $root = realpath($storage->path(''));
        $file = realpath($storage->path($path));

        if (!$root || !$file || !is_file($file) || !is_readable($file)) {
            throw new \RuntimeException('Document is not available.');
        }

        $prefix = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!str_starts_with($file, $prefix)) {
            throw new \RuntimeException('Document path is outside storage.');
        }

        return $file;
    }

    private function extractPlainText(string $file): array
    {
        $text = file_get_contents($file);

        if ($text === false) {
            return $this->failure('read_failed', 'Text file could not be read.');
        }

        $text = $this->cleanText($text);

        if ($text === '') {
            return $this->failure('empty_text', 'No readable text was found.');
        }

        return [
            'status' => 'extracted',
            'method' => 'plain_text',
            'text' => $text,
            'pages' => 1,
        ];
    }

    private function extractPdf(string $file): array
    {
        $info = $this->run(['pdfinfo', $file]);
        preg_match('/^Pages:\s+(\d+)/mi', $info, $matches);

        $pages = isset($matches[1]) ? (int) $matches[1] : 0;

        if ($pages < 1) {
            return $this->failure('invalid_document', 'PDF page count could not be read.');
        }

        if ($pages > self::MAX_PDF_PAGES) {
            return $this->failure('page_limit', 'PDF exceeds the 400-page extraction limit.');
        }

        $text = trim($this->run(['pdftotext', '-enc', 'UTF-8', '-layout', $file, '-']));

        if (mb_strlen($text) >= 80) {
            return [
                'status' => 'extracted',
                'method' => 'pdftotext',
                'text' => $this->cleanText($text),
                'pages' => $pages,
            ];
        }

        $ocrPages = min($pages, self::MAX_OCR_PAGES);
        $directory = sys_get_temp_dir() . '/dor-chatbot-' . bin2hex(random_bytes(8));

        if (!mkdir($directory, 0700)) {
            throw new \RuntimeException('Unable to create OCR temporary directory.');
        }

        try {
            $prefix = $directory . '/page';

            $this->run([
                'pdftoppm',
                '-f', '1',
                '-l', (string) $ocrPages,
                '-r', '130',
                '-png',
                $file,
                $prefix,
            ]);

            $images = glob($prefix . '-*.png') ?: [];
            sort($images, SORT_NATURAL);

            $parts = [];

            foreach ($images as $image) {
                $parts[] = $this->run([
                    'tesseract',
                    $image,
                    'stdout',
                    '-l',
                    'eng+nep',
                    '--psm',
                    '3',
                ]);
            }

            $ocrText = $this->cleanText(implode("\n\n", $parts));

            if ($ocrText === '') {
                return $this->failure('empty_text', 'No readable text was found in this PDF.');
            }

            return [
                'status' => 'extracted',
                'method' => 'tesseract_eng_nep',
                'text' => $ocrText,
                'pages' => $pages,
                'ocr_pages' => count($images),
                'partial' => $pages > $ocrPages,
            ];
        } finally {
            foreach (glob($directory . '/*') ?: [] as $temporaryFile) {
                if (is_file($temporaryFile)) {
                    @unlink($temporaryFile);
                }
            }

            @rmdir($directory);
        }
    }

    private function extractDocx(string $file): array
    {
        $zip = $this->openZip($file);

        try {
            $xml = $this->readZipXml($zip, 'word/document.xml');
            $xml->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

            $parts = [];

            foreach ($xml->xpath('//w:p') ?: [] as $paragraph) {
                $paragraph->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
                $words = [];

                foreach ($paragraph->xpath('.//w:t') ?: [] as $node) {
                    $words[] = (string) $node;
                }

                $parts[] = implode('', $words);
            }

            $text = $this->cleanText(implode("\n", $parts));

            return $text === ''
                ? $this->failure('empty_text', 'No readable text found in DOCX.')
                : ['status' => 'extracted', 'method' => 'php_zip_xml', 'text' => $text];
        } finally {
            $zip->close();
        }
    }

    private function extractXlsx(string $file): array
    {
        $zip = $this->openZip($file);

        try {
            $shared = [];

            if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                $xml = $this->readZipXml($zip, 'xl/sharedStrings.xml');
                $xml->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

                foreach ($xml->xpath('//a:si') ?: [] as $item) {
                    $item->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    $values = [];

                    foreach ($item->xpath('.//a:t') ?: [] as $node) {
                        $values[] = (string) $node;
                    }

                    $shared[] = implode('', $values);
                }
            }

            $parts = [];
            $count = 0;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);

                if (!preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                    continue;
                }

                $sheet = $this->readZipXml($zip, $name);
                $sheet->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

                foreach ($sheet->xpath('//a:row') ?: [] as $row) {
                    $row->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    $cells = [];

                    foreach ($row->xpath('./a:c') ?: [] as $cell) {
                        $type = (string) $cell['t'];
                        $value = (string) ($cell->v ?? '');

                        if ($type === 's' && isset($shared[(int) $value])) {
                            $value = $shared[(int) $value];
                        } elseif ($type === 'inlineStr') {
                            $cell->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                            $inline = [];

                            foreach ($cell->xpath('.//a:t') ?: [] as $node) {
                                $inline[] = (string) $node;
                            }

                            $value = implode('', $inline);
                        }

                        $cells[] = $value;
                    }

                    $parts[] = implode(' | ', $cells);

                    if (++$count >= 10000) {
                        break 2;
                    }
                }
            }

            $text = $this->cleanText(implode("\n", $parts));

            return $text === ''
                ? $this->failure('empty_text', 'No readable cell values found in XLSX.')
                : ['status' => 'extracted', 'method' => 'php_zip_xml', 'text' => $text];
        } finally {
            $zip->close();
        }
    }

    private function extractPptx(string $file): array
    {
        $zip = $this->openZip($file);

        try {
            $slides = [];

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);

                if (!preg_match('#^ppt/slides/slide\d+\.xml$#', $name)) {
                    continue;
                }

                $slides[$name] = $name;
            }

            uksort($slides, 'strnatcmp');

            $parts = [];

            foreach ($slides as $name) {
                $xml = $this->readZipXml($zip, $name);
                $xml->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

                $texts = [];

                foreach ($xml->xpath('//a:t') ?: [] as $node) {
                    $texts[] = (string) $node;
                }

                $parts[] = implode(' ', $texts);
            }

            $text = $this->cleanText(implode("\n\n", $parts));

            return $text === ''
                ? $this->failure('empty_text', 'No readable slide text found in PPTX.')
                : ['status' => 'extracted', 'method' => 'php_zip_xml', 'text' => $text, 'slides' => count($slides)];
        } finally {
            $zip->close();
        }
    }

    private function openZip(string $file): ZipArchive
    {
        $zip = new ZipArchive();

        if ($zip->open($file) !== true) {
            throw new \RuntimeException('Invalid Office document archive.');
        }

        try {
            $maxEntries = 5000;
            $maxTotalSize = 150 * 1024 * 1024;
            $maxEntrySize = 50 * 1024 * 1024;
            $maxCompressionRatio = 100;
            $totalSize = 0;

            if ($zip->numFiles > $maxEntries) {
                throw new \RuntimeException('Document contains too many archive entries.');
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);

                if ($stat === false || !isset($stat['size'], $stat['comp_size'], $stat['name'])) {
                    throw new \RuntimeException('Unable to inspect document archive entry.');
                }

                $name = (string) $stat['name'];
                $size = (int) $stat['size'];
                $compressedSize = (int) $stat['comp_size'];

                if ($size < 0 || $compressedSize < 0) {
                    throw new \RuntimeException('Invalid document archive entry size.');
                }

                // Directory entries do not contribute to extracted file size.
                if (str_ends_with($name, '/')) {
                    continue;
                }

                if ($size > $maxEntrySize) {
                    throw new \RuntimeException('Document archive entry exceeds the 50 MB limit.');
                }

                $totalSize += $size;

                if ($totalSize > $maxTotalSize) {
                    throw new \RuntimeException('Document archive exceeds the 150 MB expanded size limit.');
                }

                if (
                    $size > 1024 * 1024
                    && ($compressedSize === 0 || ($size / $compressedSize) > $maxCompressionRatio)
                ) {
                    throw new \RuntimeException('Document archive has a suspicious compression ratio.');
                }
            }

            return $zip;
        } catch (\Throwable $e) {
            $zip->close();
            throw $e;
        }
    }

    private function readZipXml(ZipArchive $zip, string $name): SimpleXMLElement
    {
        $index = $zip->locateName($name);

        if ($index === false) {
            throw new \RuntimeException('Required document XML is missing.');
        }

        $stat = $zip->statIndex($index);

        if (!$stat || $stat['size'] > 20 * 1024 * 1024) {
            throw new \RuntimeException('Document XML part exceeds the 20 MB limit.');
        }

        $content = $zip->getFromIndex($index);

        if ($content === false) {
            throw new \RuntimeException('Unable to read document XML.');
        }

        // Remove UTF-8 BOM if present in the ZIP XML part.
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $xml = simplexml_load_string(
                $content,
                SimpleXMLElement::class,
                LIBXML_NONET | LIBXML_NOBLANKS
            );

            if ($xml === false) {
                $details = [];

                foreach (array_slice(libxml_get_errors(), 0, 3) as $error) {
                    $details[] = trim($error->message)
                        . ' (line ' . $error->line
                        . ', column ' . $error->column . ')';
                }

                throw new \RuntimeException(
                    'Invalid document XML: ' . implode('; ', $details)
                );
            }

            return $xml;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function run(array $command): string
    {
        $process = new Process($command);
        $process->setTimeout(self::PROCESS_TIMEOUT);
        $process->mustRun();

        return $process->getOutput();
    }

    private function cleanText(string $text): string
    {
        $text = str_replace("\0", '', $text);
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function failure(string $status, string $message): array
    {
        return [
            'status' => $status,
            'method' => null,
            'text' => '',
            'error' => $message,
        ];
    }
}

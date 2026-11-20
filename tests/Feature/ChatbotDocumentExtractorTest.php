<?php

namespace Tests\Feature;

use App\Services\Chatbot\ChatbotDocumentExtractor;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class ChatbotDocumentExtractorTest extends TestCase
{
    private ChatbotDocumentExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->extractor = app(ChatbotDocumentExtractor::class);
    }

    private function makeArchive(string $name, array $files): string
    {
        $path = Storage::disk('local')->path('chatbot-tests/' . $name);

        Storage::disk('local')->makeDirectory('chatbot-tests');

        $zip = new ZipArchive();

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create test archive.');
        }

        foreach ($files as $file => $content) {
            $zip->addFromString($file, $content);
        }

        $zip->close();

        return 'chatbot-tests/' . $name;
    }

    public function test_docx_extracts_english_and_nepali_text(): void
    {
        $path = $this->makeArchive('sample.docx', [
            'word/document.xml' => '<?xml version="1.0" encoding="UTF-8"?>
                <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
                    <w:body>
                        <w:p><w:r><w:t>Department of Roads</w:t></w:r></w:p>
                        <w:p><w:r><w:t>सडक विभाग</w:t></w:r></w:p>
                    </w:body>
                </w:document>',
        ]);

        $result = $this->extractor->extract('local', $path);

        $this->assertSame('extracted', $result['status']);
        $this->assertSame('php_zip_xml', $result['method']);
        $this->assertStringContainsString('Department of Roads', $result['text']);
        $this->assertStringContainsString('सडक विभाग', $result['text']);
    }

    public function test_xlsx_extracts_shared_and_inline_cell_values(): void
    {
        $path = $this->makeArchive('sample.xlsx', [
            'xl/sharedStrings.xml' => '<?xml version="1.0"?>
                <sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
                    <si><t>Office Name</t></si>
                    <si><t>सडक विभाग</t></si>
                </sst>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0"?>
                <worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
                    <sheetData>
                        <row r="1">
                            <c r="A1" t="s"><v>0</v></c>
                            <c r="B1" t="s"><v>1</v></c>
                        </row>
                        <row r="2">
                            <c r="A2" t="inlineStr"><is><t>Head Office</t></is></c>
                            <c r="B2"><v>4</v></c>
                        </row>
                    </sheetData>
                </worksheet>',
        ]);

        $result = $this->extractor->extract('local', $path);

        $this->assertSame('extracted', $result['status']);
        $this->assertStringContainsString('Office Name', $result['text']);
        $this->assertStringContainsString('सडक विभाग', $result['text']);
        $this->assertStringContainsString('Head Office', $result['text']);
        $this->assertStringContainsString('4', $result['text']);
    }

    public function test_pptx_extracts_slide_text_in_order(): void
    {
        $path = $this->makeArchive('sample.pptx', [
            'ppt/slides/slide2.xml' => '<p:sld xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"
                xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
                <a:t>Second Slide</a:t></p:sld>',
            'ppt/slides/slide1.xml' => '<p:sld xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"
                xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
                <a:t>First Slide</a:t></p:sld>',
        ]);

        $result = $this->extractor->extract('local', $path);

        $this->assertSame('extracted', $result['status']);
        $this->assertSame(2, $result['slides']);
        $this->assertLessThan(
            strpos($result['text'], 'Second Slide'),
            strpos($result['text'], 'First Slide')
        );
    }

    public function test_suspicious_zip_compression_ratio_is_rejected(): void
    {
        $largeText = str_repeat('A', 2 * 1024 * 1024);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:body><w:p><w:r><w:t>'
            . $largeText
            . '</w:t></w:r></w:p></w:body></w:document>';

        $path = $this->makeArchive('high-ratio.docx', [
            'word/document.xml' => $xml,
        ]);

        $result = $this->extractor->extract('local', $path);

        $this->assertSame('failed', $result['status']);
        $this->assertSame('', $result['text']);
    }

    public function test_digital_pdf_extracts_english_and_nepali_text(): void
    {
        $fixture = base_path('tests/Fixtures/chatbot/digital-nepali.pdf');

        $this->assertFileExists($fixture);

        $path = 'chatbot-tests/digital-nepali.pdf';
        Storage::disk('local')->put($path, file_get_contents($fixture));

        $result = $this->extractor->extract('local', $path);

        $this->assertSame('extracted', $result['status']);
        $this->assertSame('pdftotext', $result['method']);
        $this->assertStringContainsString('Department of Roads', $result['text']);
        $this->assertStringContainsString('नेपाल सरकार', $result['text']);
        $this->assertStringContainsString('सडक विभाग', $result['text']);
    }

    public function test_scanned_pdf_uses_english_and_nepali_ocr(): void
    {
        $fixture = base_path('tests/Fixtures/chatbot/scanned-nepali.pdf');

        $this->assertFileExists($fixture);

        $path = 'chatbot-tests/scanned-nepali.pdf';
        Storage::disk('local')->put($path, file_get_contents($fixture));

        $result = $this->extractor->extract('local', $path);

        $this->assertSame('extracted', $result['status']);
        $this->assertSame('tesseract_eng_nep', $result['method']);
        $this->assertSame(1, $result['pages']);
        $this->assertSame(1, $result['ocr_pages']);
        $this->assertStringContainsString('Department of Roads', $result['text']);
        $this->assertStringContainsString('नेपाल', $result['text']);
    }

    public function test_unsupported_legacy_doc_is_not_processed(): void
    {
        $result = $this->extractor->extract('local', 'missing.doc');

        $this->assertSame('unsupported', $result['status']);
        $this->assertSame('', $result['text']);
    }

    public function test_unapproved_storage_disk_is_rejected(): void
    {
        $result = $this->extractor->extract('s3', 'sample.docx');

        $this->assertSame('failed', $result['status']);
        $this->assertSame('', $result['text']);
    }

    public function test_missing_document_returns_safe_failure(): void
    {
        $result = $this->extractor->extract('local', 'missing.docx');

        $this->assertSame('failed', $result['status']);
        $this->assertSame('', $result['text']);
        $this->assertArrayHasKey('error', $result);
    }
}

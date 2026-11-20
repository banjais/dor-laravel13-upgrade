<?php

namespace App\Services\AutomaticImages;

use Illuminate\Validation\ValidationException;

class FlashBoxFileValidator
{
    private const MAX_COVER_BYTES = 5 * 1024 * 1024;
    private const MAX_PDF_BYTES = 20 * 1024 * 1024;
    private const MAX_VIDEO_BYTES = 100 * 1024 * 1024;

    private const MAX_IMAGE_WIDTH = 12000;
    private const MAX_IMAGE_HEIGHT = 12000;
    private const MAX_IMAGE_PIXELS = 40000000;

    /**
     * Validate a CKFinder cover image path.
     * The original stored path is not modified.
     */
    public function validateCover(?string $path): void
    {
        if ($path === null || trim($path) === '') {
            return;
        }

        $file = $this->resolveCkFinderFile($path);
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        $image = @getimagesize($file);
        $mime = $this->detectMime($file);

        $validTypes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];

        if (
            !isset($validTypes[$extension]) ||
            $mime !== $validTypes[$extension] ||
            $image === false ||
            !isset($image['mime']) ||
            $image['mime'] !== $mime
        ) {
            $this->reject('Cover Photo मा वास्तविक JPEG वा PNG फाइल मात्र स्वीकार हुन्छ।');
        }

        $width = (int) $image[0];
        $height = (int) $image[1];

        if (
            $width < 1 ||
            $height < 1 ||
            $width > self::MAX_IMAGE_WIDTH ||
            $height > self::MAX_IMAGE_HEIGHT ||
            ($width * $height) > self::MAX_IMAGE_PIXELS
        ) {
            $this->reject('Cover Photo को Image Dimensions स्वीकृत सीमाभन्दा ठूलो छ।');
        }

        $this->validateSize($file, self::MAX_COVER_BYTES, 'Cover Photo को आकार 5 MB भन्दा बढी हुनु हुँदैन।');
    }

    /**
     * Validate a CKFinder PDF attachment path.
     */
    public function validatePdf(?string $path): void
    {
        if ($path === null || trim($path) === '') {
            return;
        }

        $file = $this->resolveCkFinderFile($path);

        if (
            strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'pdf' ||
            $this->detectMime($file) !== 'application/pdf'
        ) {
            $this->reject('Attachment मा वास्तविक PDF फाइल मात्र स्वीकार हुन्छ।');
        }

        $this->validateSize($file, self::MAX_PDF_BYTES, 'PDF फाइलको आकार 20 MB भन्दा बढी हुनु हुँदैन।');
    }

    /**
     * Validate a CKFinder MP4 video path.
     */
    public function validateVideo(?string $path): void
    {
        if ($path === null || trim($path) === '') {
            return;
        }

        $file = $this->resolveCkFinderFile($path);

        if (
            strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'mp4' ||
            $this->detectMime($file) !== 'video/mp4'
        ) {
            $this->reject('Video मा वास्तविक MP4 फाइल मात्र स्वीकार हुन्छ।');
        }

        $this->validateSize($file, self::MAX_VIDEO_BYTES, 'Video फाइलको आकार 100 MB भन्दा बढी हुनु हुँदैन।');
    }

    /**
     * Resolve a URL-encoded CKFinder path and ensure it stays
     * inside the configured public CKFinder files/images folders.
     */
    private function resolveCkFinderFile(string $path): string
    {
        $path = trim($path);

        if (
            $path === '' ||
            preg_match('/[\x00-\x1F\x7F]/', $path) ||
            preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $path) ||
            str_contains($path, '\\') ||
            str_contains($path, '?') ||
            str_contains($path, '#')
        ) {
            $this->reject('CKFinder फाइलको Path मान्य छैन।');
        }

        // Decode URL-encoded filenames once, including spaces and commas.
        $decoded = rawurldecode($path);

        if (
            preg_match('/[\x00-\x1F\x7F]/', $decoded) ||
            str_contains($decoded, '\\') ||
            preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $decoded)
        ) {
            $this->reject('CKFinder फाइलको Path मान्य छैन।');
        }

        $decoded = ltrim($decoded, '/');

        $allowedPrefixes = [
            'uploads/ckfiles/files/',
            'uploads/ckfiles/images/',
        ];

        $allowed = false;

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($decoded, $prefix)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed || preg_match('#(^|/)\.\.?(/|$)#', $decoded)) {
            $this->reject('फाइल CKFinder को अनुमति प्राप्त फोल्डरभित्र हुनुपर्छ।');
        }

        $publicRoot = realpath(public_path());

        if ($publicRoot === false) {
            $this->reject('Public फोल्डर फेला परेन।');
        }

        $realFile = realpath($publicRoot . DIRECTORY_SEPARATOR . $decoded);

        if (
            $realFile === false ||
            !is_file($realFile) ||
            !is_readable($realFile)
        ) {
            $this->reject('चयन गरिएको फाइल फेला परेन वा पढ्न मिलेन।');
        }

        $realFile = str_replace('\\', '/', $realFile);
        $publicRoot = rtrim(str_replace('\\', '/', $publicRoot), '/') . '/';

        if (!str_starts_with($realFile, $publicRoot)) {
            $this->reject('फाइलको Path अनुमति प्राप्त स्थानबाहिर छ।');
        }

        $realRelative = substr($realFile, strlen($publicRoot));

        $insideAllowedFolder = false;

        foreach ($allowedPrefixes as $prefix) {
            $realPrefix = realpath($publicRoot . rtrim($prefix, '/'));
            if ($realPrefix === false) {
                continue;
            }

            $realPrefix = rtrim(str_replace('\\', '/', $realPrefix), '/') . '/';

            if (str_starts_with($realFile . '/', $realPrefix)) {
                $insideAllowedFolder = true;
                break;
            }
        }

        if (!$insideAllowedFolder || $realRelative === '') {
            $this->reject('फाइल CKFinder को अनुमति प्राप्त फोल्डरभित्र छैन।');
        }

        return $realFile;
    }

    private function detectMime(string $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file);

        if (!is_string($mime) || $mime === '') {
            $this->reject('फाइलको वास्तविक प्रकार जाँच गर्न सकिएन।');
        }

        return strtolower($mime);
    }

    private function validateSize(string $file, int $maximum, string $message): void
    {
        $size = filesize($file);

        if ($size === false || $size > $maximum) {
            $this->reject($message);
        }
    }

    private function reject(string $message): void
    {
        throw ValidationException::withMessages([
            'flash_box_file' => $message,
        ]);
    }
}

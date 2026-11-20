<?php

namespace App\Services\AutomaticImages;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class AutomaticImageProcessor
{
    /**
     * Process an image without cropping or stretching.
     * Keep the untouched upload separately under originals/.
     */
    public function process(
        UploadedFile $imageFile,
        string $destinationPath,
        string $filename,
        string $type
    ): array {
        if (!extension_loaded('gd')) {
            throw new RuntimeException('GD image processing is not available.');
        }

        $limits = [
            'banner' => ['width' => 2560, 'height' => 1600],
            'gallery' => ['width' => 2400, 'height' => 2400],
        ];

        if (!isset($limits[$type])) {
            throw new RuntimeException('Unsupported image type.');
        }

        $sourcePath = $imageFile->getPathname();
        $info = @getimagesize($sourcePath);

        if (
            !$info ||
            empty($info[0]) ||
            empty($info[1]) ||
            !in_array($info['mime'] ?? '', ['image/jpeg', 'image/png'], true)
        ) {
            throw new RuntimeException('Please upload a valid JPEG or PNG image.');
        }

        $sourceWidth = (int) $info[0];
        $sourceHeight = (int) $info[1];

        if (
            $sourceWidth > 12000 ||
            $sourceHeight > 12000 ||
            ($sourceWidth * $sourceHeight) > 40000000
        ) {
            throw new RuntimeException('Uploaded image dimensions are too large.');
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (
            !in_array($extension, ['jpg', 'jpeg', 'png'], true) ||
            ($info['mime'] === 'image/png' && $extension !== 'png') ||
            ($info['mime'] === 'image/jpeg' && !in_array($extension, ['jpg', 'jpeg'], true))
        ) {
            throw new RuntimeException('Image format and filename extension do not match.');
        }

        $originalDirectory = $destinationPath . DIRECTORY_SEPARATOR . 'originals';

        foreach ([$destinationPath, $originalDirectory] as $directory) {
            if (
                !is_dir($directory) &&
                !mkdir($directory, 0755, true) &&
                !is_dir($directory)
            ) {
                throw new RuntimeException('Unable to create image storage directory.');
            }
        }

        $filename = basename($filename);
        $processedPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;
        $originalPath = $originalDirectory . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($processedPath) || file_exists($originalPath)) {
            throw new RuntimeException('An image with this filename already exists.');
        }

        if (!copy($sourcePath, $originalPath)) {
            throw new RuntimeException('Unable to preserve the original image.');
        }

        $source = @imagecreatefromstring(file_get_contents($sourcePath));

        if (!$source) {
            @unlink($originalPath);
            throw new RuntimeException('Unable to read the uploaded image.');
        }

        // Correct the camera orientation using EXIF metadata.
        if (
            $info['mime'] === 'image/jpeg' &&
            function_exists('exif_read_data')
        ) {
            $exif = @exif_read_data($sourcePath);
            $orientation = (int) ($exif['Orientation'] ?? 1);

            if (in_array($orientation, [2, 3, 4, 5, 6, 7, 8], true)) {
                if (in_array($orientation, [2, 4, 5, 7], true)) {
                    imageflip(
                        $source,
                        in_array($orientation, [2, 5], true)
                            ? IMG_FLIP_HORIZONTAL
                            : IMG_FLIP_VERTICAL
                    );
                }

                if (in_array($orientation, [3, 5, 6, 7, 8], true)) {
                    $angle = in_array($orientation, [5, 6, 7], true) ? -90 : 90;
                    $rotated = imagerotate($source, $angle, 0);

                    if ($rotated !== false) {
                        imagedestroy($source);
                        $source = $rotated;
                    }
                }
            }
        }

        $width = imagesx($source);
        $height = imagesy($source);

        // Fit inside the type-specific maximum dimensions; never upscale.
        $scale = min(
            1,
            $limits[$type]['width'] / $width,
            $limits[$type]['height'] / $height
        );

        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $output = imagecreatetruecolor($targetWidth, $targetHeight);

        if (!$output) {
            imagedestroy($source);
            @unlink($originalPath);
            throw new RuntimeException('Unable to create the processed image.');
        }

        if ($info['mime'] === 'image/png') {
            imagealphablending($output, false);
            imagesavealpha($output, true);
            $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
            imagefill($output, 0, 0, $transparent);
        }

        imagecopyresampled(
            $output,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height
        );

        $temporaryPath = $processedPath . '.tmp-' . bin2hex(random_bytes(6));

        $saved = in_array($extension, ['jpg', 'jpeg'], true)
            ? imagejpeg($output, $temporaryPath, 88)
            : imagepng($output, $temporaryPath, 6);

        imagedestroy($source);
        imagedestroy($output);

        if (!$saved || !is_file($temporaryPath) || !rename($temporaryPath, $processedPath)) {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }

            @unlink($originalPath);
            throw new RuntimeException('Unable to save the processed image.');
        }

        return [
            'processed_path' => $processedPath,
            'original_path' => $originalPath,
            'width' => $targetWidth,
            'height' => $targetHeight,
            'aspect_ratio' => round($targetWidth / $targetHeight, 4),
            'type' => $type,
        ];
    }
}

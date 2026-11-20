<?php

namespace App\Services\PhotoStudio;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use RuntimeException;
use Symfony\Component\Process\Process;

class PhotoStudioService
{
    private string $workspace;
    private string $python;
    private string $processor;

    public function __construct()
    {
        $this->workspace = rtrim(sys_get_temp_dir(), '/')
            . '/dor-photo-studio';

        $this->python = (string) config('photo-studio.python');

        if ($this->python === '' || !is_executable($this->python)) {
            throw new RuntimeException('Photo Studio Python executable is not configured or executable.');
        }
        $this->processor = base_path(
            'app/Services/PhotoStudio/process_photo.py'
        );

        if (!is_dir($this->workspace)) {
            if (!mkdir($this->workspace, 0700, true) && !is_dir($this->workspace)) {
                throw new RuntimeException('Private AI workspace could not be created.');
            }
        }

        chmod($this->workspace, 0700);
    }

    /**
     * Save an uploaded original privately and generate its AI preview.
     * The original upload is never modified.
     */
    public function createPreview(
        UploadedFile $upload,
        string $framing = 'full',
        string $profile = 'standard'
    ): string {
        if (!in_array($framing, ['full', 'head_neck'], true)) {
            throw new RuntimeException('Invalid photo framing option.');
        }

        if (!in_array($profile, ['standard', 'office_chief'], true)) {
            throw new RuntimeException('Invalid photo processing profile.');
        }

        if (!$upload->isValid()) {
            throw new RuntimeException('The uploaded photo is invalid.');
        }

        if ($upload->getSize() > 1024 * 1024) {
            throw new RuntimeException('Photo size must not exceed 1 MB.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))
            ->file($upload->getRealPath());

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => throw new RuntimeException('Only JPEG and PNG photos are supported.'),
        };

        $imageInfo = getimagesize($upload->getRealPath());

        if (
            !$imageInfo ||
            $imageInfo[0] < 100 ||
            $imageInfo[1] < 100 ||
            ($imageInfo[0] * $imageInfo[1]) > 25000000
        ) {
            throw new RuntimeException('Photo dimensions are invalid or too large.');
        }

        $id = bin2hex(random_bytes(24));
        $directory = $this->workspace . '/' . $id;

        if (!mkdir($directory, 0700)) {
            throw new RuntimeException('Private photo workspace could not be created.');
        }

        chmod($directory, 0700);

        $originalPath = $directory . '/original.' . $extension;
        $previewPath = $directory . '/preview.jpg';

        try {
            if (!copy($upload->getRealPath(), $originalPath)) {
                throw new RuntimeException('Original photo could not be stored privately.');
            }

            chmod($originalPath, 0600);

            $process = new Process([
                $this->python,
                $this->processor,
                '--input',
                $originalPath,
                '--output',
                $previewPath,
                '--framing',
                $framing,
                '--profile',
                $profile,
            ]);

            $process->setTimeout(180);
            $process->run();

            if (!$process->isSuccessful() || !is_file($previewPath)) {
                $errorData = null;
                $errorLines = preg_split('/\\R/', trim($process->getErrorOutput()));

                foreach (array_reverse($errorLines) as $errorLine) {
                    $decodedError = json_decode($errorLine, true);

                    if (is_array($decodedError) && ($decodedError['status'] ?? null) === 'error') {
                        $errorData = $decodedError;
                        break;
                    }
                }

                $errorMessage = $errorData['message'] ?? null;

                if (
                    ($errorData['status'] ?? null) === 'error' &&
                    $errorMessage === "No human face detected. Please upload a valid photo."
                ) {
                    throw new RuntimeException($errorMessage);
                }

                throw new RuntimeException('AI photo processing failed. Please try again.');
            }

            chmod($previewPath, 0600);

            return $id;
        } catch (\Throwable $exception) {
            $this->delete($id);
            throw $exception;
        }
    }

    /**
     * Return a private preview path after validating its token.
     */
    public function getPreviewPath(string $id): string
    {
        $directory = $this->getDirectory($id);
        $path = $directory . '/preview.jpg';

        if (!is_file($path)) {
            throw new RuntimeException('AI preview is no longer available.');
        }

        return $path;
    }

    /**
     * Return the preserved original upload path.
     */
    public function getOriginalPath(string $id): string
    {
        $directory = $this->getDirectory($id);

        foreach (['jpg', 'png'] as $extension) {
            $path = $directory . '/original.' . $extension;

            if (is_file($path)) {
                return $path;
            }
        }

        throw new RuntimeException('Original photo is no longer available.');
    }

    /**
     * Remove one private preview workspace.
     */
    /**
     * Return an accepted AI preview as a temporary uploaded file.
     * The preview must belong to the current Admin session and user.
     */
    public function getAcceptedUpload(
        string $type,
        string $token,
        int $userId
    ): UploadedFile {
        $this->assertPendingPreview($type, $token, $userId);

        $path = $this->getPreviewPath($token);

        return new UploadedFile(
            $path,
            'photo-studio-preview.jpg',
            'image/jpeg',
            UPLOAD_ERR_OK,
            true
        );
    }

    /**
     * Delete the private original and preview after successful database save.
     */
    public function completeAcceptedPreview(
        string $type,
        string $token,
        int $userId
    ): void {
        $this->assertPendingPreview($type, $token, $userId);

        $this->delete($token);
        Session::forget('photo_studio.pending.' . $type);
    }

    private function assertPendingPreview(
        string $type,
        string $token,
        int $userId
    ): void {
        if (!in_array($type, ['staff', 'office_chief', 'home_officer'], true)) {
            throw new RuntimeException('Invalid Photo Studio type.');
        }

        if (!preg_match('/^[a-f0-9]{48}$/', $token)) {
            throw new RuntimeException('Invalid photo preview reference.');
        }

        $pending = Session::get('photo_studio.pending.' . $type);

        if (
            !is_array($pending) ||
            !isset($pending['token'], $pending['user_id'], $pending['created_at']) ||
            !is_string($pending['token']) ||
            !hash_equals($pending['token'], $token) ||
            (int) $pending['user_id'] !== $userId ||
            !is_numeric($pending['created_at']) ||
            (time() - (int) $pending['created_at']) > 1800 ||
            (int) $pending['created_at'] > time()
        ) {
            throw new RuntimeException('Photo preview is expired or does not belong to this Admin session.');
        }
    }
    public function delete(string $id): void
    {
        if (!preg_match('/^[a-f0-9]{48}$/', $id)) {
            return;
        }

        $directory = $this->workspace . '/' . $id;

        if (!is_dir($directory)) {
            return;
        }

        foreach (glob($directory . '/*') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        @rmdir($directory);
    }

    private function getDirectory(string $id): string
    {
        if (!preg_match('/^[a-f0-9]{48}$/', $id)) {
            throw new RuntimeException('Invalid photo preview reference.');
        }

        return $this->workspace . '/' . $id;
    }
}

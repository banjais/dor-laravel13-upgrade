<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAccessControlModel;
use App\Models\OfficeChief;
use App\Services\PhotoStudio\PhotoStudioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\UploadedFile;
use Throwable;

class PhotoStudioController extends Controller
{
    private const MODULE_ID = 29;
    private const SESSION_PREFIX = 'photo_studio.pending.';
    private const PREVIEW_LIFETIME = 1800;

    private function allowedTypes(): array
    {
        return ['staff', 'office_chief', 'home_officer'];
    }

    private function authorizePhotoStudio(string $type)
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        if (!UserAccessControlModel::getAccessAddByModuleAndUserMapId(
            ($type === 'home_officer' ? 1 : self::MODULE_ID),
            $user->id
        )) {
            abort(403, 'You do not have permission to use Photo Studio.');
        }

        return $user;
    }

    private function sessionKey(string $type): string
    {
        abort_unless(in_array($type, $this->allowedTypes(), true), 404);

        return self::SESSION_PREFIX . $type;
    }

    private function pendingPreview(string $type): ?array
    {
        $pending = Session::get($this->sessionKey($type));

        if (!is_array($pending)) {
            return null;
        }

        $user = Auth::user();

        if (
            !$user ||
            !isset($pending['token'], $pending['user_id'], $pending['created_at']) ||
            !is_string($pending['token']) ||
            !preg_match('/^[a-f0-9]{48}$/', $pending['token']) ||
            (int) $pending['user_id'] !== (int) $user->id ||
            !is_numeric($pending['created_at']) ||
            (time() - (int) $pending['created_at']) > self::PREVIEW_LIFETIME ||
            (int) $pending['created_at'] > time()
        ) {
            return null;
        }

        return $pending;
    }

    /**
     * Generate a private AI preview for Staff or Office Chief.
     */
    public function createPreview(Request $request, string $type, PhotoStudioService $studio)
    {
        $user = $this->authorizePhotoStudio($type);
        $this->sessionKey($type);

        $rules = $type === 'office_chief'
            ? [
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
                'existing_photo_id' => 'nullable|integer|required_without:attachment',
            ]
            : [
                'attachment' => 'required|file|mimes:jpg,jpeg,png|max:1024',
            ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $upload = $request->file('attachment');

        if ($type === 'office_chief' && !$upload) {
            $chief = OfficeChief::findOrFail((int) $request->input('existing_photo_id'));

            if (
                (int) $user->user_type_id === 12 &&
                (int) $chief->office_id !== (int) $user->office_id
            ) {
                abort(403);
            }

            $filename = (string) $chief->photo;

            if (
                $filename === '' ||
                basename($filename) !== $filename ||
                str_contains($filename, '/') ||
                str_contains($filename, '\\')
            ) {
                abort(422, 'The current photo is not a valid image file.');
            }

            $directory = realpath(public_path('uploads/office_chiefs'));
            $source = $directory
                ? realpath($directory . DIRECTORY_SEPARATOR . $filename)
                : false;

            if (
                !$directory ||
                !$source ||
                dirname($source) !== $directory ||
                !is_file($source) ||
                !is_readable($source)
            ) {
                abort(422, 'The current photo could not be safely accessed.');
            }

            $size = filesize($source);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($source);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (
                $size === false ||
                $size > 1024 * 1024 ||
                !in_array($mime, ['image/jpeg', 'image/png'], true) ||
                !in_array($extension, ['jpg', 'jpeg', 'png'], true)
            ) {
                abort(422, 'The current photo must be a JPEG or PNG image not exceeding 1 MB.');
            }

            $upload = new UploadedFile(
                $source,
                $filename,
                $mime,
                UPLOAD_ERR_OK,
                true
            );
        }

        if (!$upload) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select a photo.',
            ], 422);
        }

        $oldPending = $this->pendingPreview($type);
        $newToken = null;

        try {
            $framing = 'head_neck';

            $profile = $type === 'office_chief'
                ? 'office_chief'
                : 'standard';

            $newToken = $studio->createPreview(
                $upload,
                $framing,
                $profile
            );

            $studio->getPreviewPath($newToken);

            Session::put($this->sessionKey($type), [
                'token' => $newToken,
                'user_id' => (int) $user->id,
                'created_at' => time(),
            ]);

            if ($oldPending && $oldPending['token'] !== $newToken) {
                $studio->delete($oldPending['token']);
            }

            return response()->json([
                'status' => 'success',
                'token' => $newToken,
                'preview_url' => route('admin.photo-studio.preview', [
                    'type' => $type,
                    'token' => $newToken,
                ]),
                'expires_in' => self::PREVIEW_LIFETIME,
            ]);
        } catch (Throwable $exception) {
            if ($newToken) {
                $studio->delete($newToken);
            }

            report($exception);

            $faceNotDetectedMessage = "No human face detected. Please upload a valid photo.";

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() === $faceNotDetectedMessage
                    ? $faceNotDetectedMessage
                    : 'Photo processing failed. Please try another photo.',
            ], 422);
        }
    }

    /**
     * Display a preview only to the Admin session that created it.
     */
    public function showPreview(
        string $type,
        string $token,
        PhotoStudioService $studio
    ): BinaryFileResponse {
        $this->authorizePhotoStudio($type);
        $this->sessionKey($type);

        $pending = $this->pendingPreview($type);

        abort_unless(
            $pending && hash_equals($pending['token'], $token),
            404
        );

        try {
            $path = $studio->getPreviewPath($token);
        } catch (Throwable $exception) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Download a pending private preview without changing its session state.
     */
    public function downloadPreview(
        string $type,
        string $token,
        PhotoStudioService $studio
    ): BinaryFileResponse {
        $this->authorizePhotoStudio($type);
        $this->sessionKey($type);

        $pending = $this->pendingPreview($type);

        abort_unless(
            $pending && hash_equals($pending['token'], $token),
            404
        );

        try {
            $path = $studio->getPreviewPath($token);
        } catch (Throwable $exception) {
            abort(404);
        }

        return response()->download($path, 'dor-ai-photo.jpg', [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Reject and remove a pending private preview.
     */
    public function rejectPreview(
        Request $request,
        string $type,
        PhotoStudioService $studio
    ): Response {
        $this->authorizePhotoStudio($type);
        $key = $this->sessionKey($type);

        $validator = Validator::make($request->all(), [
            'token' => 'required|string|regex:/^[a-f0-9]{48}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid preview reference.',
            ], 422);
        }

        $pending = $this->pendingPreview($type);
        $token = (string) $request->input('token');

        abort_unless(
            $pending && hash_equals($pending['token'], $token),
            404
        );

        $studio->delete($token);
        Session::forget($key);

        return response()->json([
            'status' => 'success',
            'message' => 'Photo preview rejected.',
        ]);
    }
}

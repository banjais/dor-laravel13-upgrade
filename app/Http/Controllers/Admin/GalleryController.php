<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ConfigurationController;
use App\Models\Album;
use App\Models\Gallery;
use App\Services\AutomaticImages\AutomaticImageProcessor;
use App\Models\Gallerydetail;
use App\Models\Language;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class GalleryController extends ConfigurationController
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name = ConfigurationController::site_name();
    }

    private function removeUnreferencedGalleryFiles(?string $filename): void
    {
        if (!$filename || Gallery::where('gallery_attachment', $filename)->exists()) {
            return;
        }

        $base = public_path('uploads/gallery');
        foreach ([$base . '/' . basename($filename), $base . '/originals/' . basename($filename)] as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    private function isSuperAdmin($user): bool
    {
        return (int) $user->user_type_id === 2;
    }

    private function hasAccess(string $action, int $userId): bool
    {
        $methods = [
            'view' => 'getAccessViewByModuleAndUserMapId',
            'add' => 'getAccessAddByModuleAndUserMapId',
            'edit' => 'getAccessUpdateByModuleAndUserMapId',
            'publish' => 'getAccessPublishByModuleAndUserMapId',
            'delete' => 'getAccessDeleteByModuleAndUserMapId',
        ];

        $method = $methods[$action] ?? null;

        return $method !== null
            && (bool) UserAccessControlModel::$method(17, $userId);
    }

    private function albumForUser($albumId, $user): ?Album
    {
        $query = Album::where('id', $albumId)
            ->where('is_deleted', '0');

        if (!$this->isSuperAdmin($user)) {
            $query->where('office_id', $user->office_id);
        }

        return $query->first();
    }

    private function galleryForUser($galleryId, $user): ?Gallery
    {
        $gallery = Gallery::where('id', $galleryId)
            ->where('is_deleted', '0')
            ->first();

        if (!$gallery) {
            return null;
        }

        $album = $this->albumForUser($gallery->album_id, $user);

        if (!$album || (int) $gallery->office_id !== (int) $album->office_id) {
            return null;
        }

        return $gallery;
    }

    private function errorView()
    {
        return view('error')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }

    private function uploadGalleryImage($imageFile, $user): string
    {
        $destinationPath = public_path('uploads/gallery');

        if (!is_dir($destinationPath)
            && !mkdir($destinationPath, 0755, true)
            && !is_dir($destinationPath)) {
            throw new \RuntimeException('Gallery upload directory is unavailable.');
        }

        if (!is_writable($destinationPath)) {
            throw new \RuntimeException('Gallery upload directory is not writable.');
        }

        $extension = strtolower($imageFile->getClientOriginalExtension());
        $filename = 'gallery_' . date('YmdHis') . '_' . (int) $user->id . '_' . Str::random(12) . '.' . $extension;

        app(AutomaticImageProcessor::class)->process(
            $imageFile,
            $destinationPath,
            $filename,
            'gallery'
        );

        return $filename;
    }

    public function getIndex($albumId)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('view', $user->id)) {
            return $this->errorView();
        }

        $album = $this->albumForUser($albumId, $user);

        if (!$album) {
            return $this->errorView();
        }

        $galleries = Gallery::with(['details' => function ($query) {
            $query->where('language_id', 2);
        }])
            ->where('album_id', $album->id)
            ->where('office_id', $album->office_id)
            ->where('is_deleted', '0')
            ->get();

        return view('album.admin.gallery-list')
            ->with('galleries', $galleries)
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('addAccess', $this->hasAccess('add', $user->id))
            ->with('editAccess', $this->hasAccess('edit', $user->id))
            ->with('publishAccess', $this->hasAccess('publish', $user->id))
            ->with('deleteAccess', $this->hasAccess('delete', $user->id))
            ->with('viewAccess', true);
    }

    public function getAddGallery($albumId)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('add', $user->id)
            || !$this->albumForUser($albumId, $user)) {
            return $this->errorView();
        }

        return view('album.admin.gallery-add')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('languages', Language::getActiveLanguages())
            ->with('albumId', $albumId);
    }

    public function addGallery($albumId, Request $request)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('add', $user->id)) {
            return $this->errorView();
        }

        $album = $this->albumForUser($albumId, $user);

        if (!$album) {
            return $this->errorView();
        }

        $languages = Language::getActiveLanguages();
        $rules = ['attachment' => 'required|image|mimes:jpg,jpeg,png|max:2048'];

        foreach ($languages as $language) {
            $rules['gallery_title_' . $language->id] = 'required|string|max:500';
            $rules['gallery_caption_' . $language->id] = 'nullable|string|max:500';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $newFilename = null;

        try {
            $newFilename = $this->uploadGalleryImage($request->file('attachment'), $user);

            DB::transaction(function () use ($request, $languages, $album, $user, $newFilename) {
                $gallery = new Gallery();
                $gallery->album_id = $album->id;
                $gallery->is_active = $request->input('is_active', '0') === '1' ? '1' : '0';
                $gallery->is_deleted = '0';
                $gallery->gallery_attachment = $newFilename;
                $gallery->created_by = $user->id;
                $gallery->updated_by = $user->id;
                $gallery->office_id = $album->office_id;
                $gallery->save();

                $now = now();
                $details = [];

                foreach ($languages as $language) {
                    $details[] = [
                        'gallery_id' => $gallery->id,
                        'language_id' => $language->id,
                        'gallery_title' => trim($request->input('gallery_title_' . $language->id)),
                        'gallery_caption' => trim((string) $request->input('gallery_caption_' . $language->id)),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DB::table('gallery_detail')->insert($details);
            });

            return Redirect::route('admin.album.view.gallery', $album->id)
                ->with('type', 'success')
                ->with('message', 'New gallery is added successfully.');
        } catch (Throwable $e) {
            $this->removeUnreferencedGalleryFiles($newFilename);

            report($e);

            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New gallery is not added. Please check the image and try again.');
        }
    }

    public function getEditGallery($id)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('edit', $user->id)) {
            return $this->errorView();
        }

        $gallery = $this->galleryForUser($id, $user);

        if (!$gallery) {
            return $this->errorView();
        }

        return view('album.admin.gallery-edit')
            ->with('galleries', Gallery::getGalleryDetailsWithLanguages($gallery->id))
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('languages', Language::getActiveLanguages())
            ->with('addAccess', $this->hasAccess('add', $user->id))
            ->with('albumId', $gallery->album_id);
    }

    public function editGallery(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('edit', $user->id)) {
            return $this->errorView();
        }

        $gallery = $this->galleryForUser($id, $user);

        if (!$gallery) {
            return $this->errorView();
        }

        $languages = Language::getActiveLanguages();
        $rules = [
            'attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'required|in:0,1',
        ];

        foreach ($languages as $language) {
            $rules['gallery_title_' . $language->id] = 'required|string|max:500';
            $rules['gallery_caption_' . $language->id] = 'nullable|string|max:500';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $newFilename = null;

        try {
            if ($request->hasFile('attachment')) {
                $newFilename = $this->uploadGalleryImage($request->file('attachment'), $user);
            }

            DB::transaction(function () use ($request, $languages, $gallery, $user, $newFilename) {
                $gallery->is_active = $request->input('is_active');
                $gallery->updated_by = $user->id;

                if ($newFilename) {
                    $gallery->gallery_attachment = $newFilename;
                } elseif ($request->input('attachment_deleted') === '1') {
                    $gallery->gallery_attachment = null;
                }

                $gallery->save();

                foreach ($languages as $language) {
                    $detail = Gallerydetail::where('gallery_id', $gallery->id)
                        ->where('language_id', $language->id)
                        ->first();

                    if (!$detail) {
                        $detail = new Gallerydetail();
                        $detail->gallery_id = $gallery->id;
                        $detail->language_id = $language->id;
                    }

                    $detail->gallery_title = trim($request->input('gallery_title_' . $language->id));
                    $detail->gallery_caption = trim((string) $request->input('gallery_caption_' . $language->id));
                    $detail->save();
                }
            });

            // Previous image files are deliberately retained for recovery.
            return Redirect::route('admin.album.view.gallery', $gallery->album_id)
                ->with('type', 'success')
                ->with('message', 'Gallery is updated successfully.');
        } catch (Throwable $e) {
            $this->removeUnreferencedGalleryFiles($newFilename);

            report($e);

            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Gallery is not updated. Please try again.');
        }
    }

    public function deleteGallery(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('delete', $user->id)) {
            return response()->json(['type' => 'error', 'message' => 'You are not authorised to delete this gallery.'], 403);
        }

        $gallery = $this->galleryForUser($request->input('id'), $user);

        if (!$gallery) {
            return response()->json(['type' => 'error', 'message' => 'Gallery not found or access denied.'], 404);
        }

        $gallery->is_active = '0';
        $gallery->is_deleted = '1';
        $gallery->updated_by = $user->id;
        $gallery->save();

        // Soft deletion keeps the uploaded image file available for recovery.
        return response()->json(['type' => 'success', 'message' => 'Gallery deleted successfully.']);
    }

    public function changeStatus(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('publish', $user->id)) {
            return response()->json(['type' => 'error', 'message' => 'You are not authorised to change gallery status.'], 403);
        }

        $gallery = $this->galleryForUser($request->input('id'), $user);

        if (!$gallery) {
            return response()->json(['type' => 'error', 'message' => 'Gallery not found or access denied.'], 404);
        }

        $gallery->is_active = $gallery->is_active === '1' ? '0' : '1';
        $gallery->updated_by = $user->id;
        $gallery->save();

        $active = $gallery->is_active === '1';

        return response()->json([
            'type' => 'success',
            'message' => $active
                ? 'The gallery has been activated successfully'
                : 'The gallery has been deactivated successfully',
            'icon' => $active ? '<i class="icon-check"></i>' : '<i class="icon-check-empty"></i>',
        ]);
    }
}

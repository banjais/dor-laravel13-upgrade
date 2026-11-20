<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ConfigurationController;
use App\Models\Album;
use App\Models\Language;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class AlbumController extends ConfigurationController
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
        $query = Album::where('id', (int) $albumId)
            ->where('is_deleted', '0');

        if (!$this->isSuperAdmin($user)) {
            $query->where('office_id', $user->office_id);
        }

        return $query->first();
    }

    private function errorView()
    {
        return view('error')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }

    private function uploadAlbumImage($imageFile, $user): string
    {
        $destinationPath = public_path('uploads/album');

        if (!is_dir($destinationPath)
            && !mkdir($destinationPath, 0755, true)
            && !is_dir($destinationPath)) {
            throw new \RuntimeException('Album upload directory is unavailable.');
        }

        if (!is_writable($destinationPath)) {
            throw new \RuntimeException('Album upload directory is not writable.');
        }

        $extension = strtolower($imageFile->getClientOriginalExtension());
        $filename = 'album_' . date('YmdHis') . '_' . (int) $user->id
            . '_' . Str::random(12) . '.' . $extension;

        $imageFile->move($destinationPath, $filename);

        return $filename;
    }

    private function albumListViewData($user): array
    {
        $query = Album::with(['details' => function ($query) {
            $query->where('language_id', 2);
        }])->where('is_deleted', '0');

        if (!$this->isSuperAdmin($user)) {
            $query->where('office_id', $user->office_id);
        }

        return [
            'albums' => $query->get(),
            'abs_site_url' => $this->abs_site_url,
            'rel_site_url' => $this->rel_site_url,
            'site_name' => $this->site_name,
            'addAccess' => $this->hasAccess('add', $user->id),
            'editAccess' => $this->hasAccess('edit', $user->id),
            'publishAccess' => $this->hasAccess('publish', $user->id),
            'deleteAccess' => $this->hasAccess('delete', $user->id),
            'viewAccess' => true,
        ];
    }

    public function getIndex()
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('view', $user->id)) {
            return $this->errorView();
        }

        return view('album.admin.album-list', $this->albumListViewData($user));
    }

    public function getAddAlbum()
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('add', $user->id)) {
            return $this->errorView();
        }

        return view('album.admin.album-add')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('languages', Language::getActiveLanguages());
    }

    public function addAlbum(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('add', $user->id)) {
            return $this->errorView();
        }

        $languages = Language::getActiveLanguages();
        $rules = [
            'album_url' => 'required|string|max:255',
            'attachment' => 'required|image|mimes:jpg,jpeg,png|max:1024',
            'is_active' => 'required|in:0,1',
        ];

        foreach ($languages as $language) {
            $rules['album_title_' . $language->id] = 'required|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $albumUrl = trim(
            preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('album_url')))),
            '-'
        );

        if ($albumUrl === '' || Album::checkExist($albumUrl, $user->office_id)) {
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Album URL is invalid or already exists in this office.');
        }

        $newImage = null;

        try {
            $newImage = $this->uploadAlbumImage($request->file('attachment'), $user);

            DB::transaction(function () use ($request, $languages, $user, $albumUrl, $newImage) {
                $album = new Album();
                $album->album_url = $albumUrl;
                $album->album_attachment = $newImage;
                $album->created_by = $user->id;
                $album->updated_by = $user->id;
                $album->office_id = $user->office_id;
                $album->is_active = $request->get('is_active');
                $album->is_deleted = '0';
                $album->save();

                $now = now();
                $details = [];

                foreach ($languages as $language) {
                    $details[] = [
                        'album_id' => $album->id,
                        'language_id' => $language->id,
                        'album_title' => trim($request->get('album_title_' . $language->id)),
                        'album_caption' => trim($request->get('album_caption_' . $language->id, '')),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DB::table('album_detail')->insert($details);
            });

            return Redirect::route('admin.albums')
                ->with('type', 'success')
                ->with('message', 'New album is added successfully.');
        } catch (Throwable $e) {
            report($e);

            if ($newImage) {
                $newPath = public_path('uploads/album/' . basename($newImage));
                if (is_file($newPath)) {
                    unlink($newPath);
                }
            }

            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New album could not be added.');
        }
    }

    public function getEditAlbum($id)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('edit', $user->id)) {
            return $this->errorView();
        }

        $album = $this->albumForUser($id, $user);

        if (!$album) {
            return $this->errorView();
        }

        try {
            $albums = Album::getAlbumDetailsWithLanguages((int) $album->id);

            if (empty($albums)) {
                return $this->errorView();
            }

            return view('album.admin.album-edit')
                ->with('albums', $albums)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', Language::getActiveLanguages())
                ->with('addAccess', $this->hasAccess('add', $user->id));
        } catch (Throwable $e) {
            report($e);
            return $this->errorView();
        }
    }

    public function editAlbum(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('edit', $user->id)) {
            return $this->errorView();
        }

        $album = $this->albumForUser($id, $user);

        if (!$album) {
            return $this->errorView();
        }

        $languages = Language::getActiveLanguages();
        $rules = [
            'album_url' => 'required|string|max:255',
            'attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'is_active' => 'required|in:0,1',
        ];

        foreach ($languages as $language) {
            $rules['album_title_' . $language->id] = 'required|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $albumUrl = trim(
            preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('album_url')))),
            '-'
        );

        if ($albumUrl === '') {
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Album URL is invalid.');
        }

        $existingId = Album::checkExist($albumUrl, $album->office_id);

        if ($existingId && (int) $existingId !== (int) $album->id) {
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Album URL already exists in this office.');
        }

        $newImage = null;

        try {
            if ($request->hasFile('attachment')) {
                $newImage = $this->uploadAlbumImage($request->file('attachment'), $user);
            }

            DB::transaction(function () use ($request, $languages, $user, $album, $albumUrl, $newImage) {
                $album->album_url = $albumUrl;
                $album->is_active = $request->get('is_active');
                $album->updated_by = $user->id;

                if ($newImage) {
                    $album->album_attachment = $newImage;
                } elseif ($request->get('attachment_deleted') === '1') {
                    $album->album_attachment = null;
                }

                $album->save();

                foreach ($languages as $language) {
                    $values = [
                        'album_title' => trim($request->get('album_title_' . $language->id)),
                        'album_caption' => trim($request->get('album_caption_' . $language->id, '')),
                        'updated_at' => now(),
                    ];

                    DB::table('album_detail')->updateOrInsert(
                        [
                            'album_id' => $album->id,
                            'language_id' => $language->id,
                        ],
                        $values + ['created_at' => now()]
                    );
                }
            });

            return Redirect::route('admin.albums')
                ->with('type', 'success')
                ->with('message', 'Album is updated successfully.');
        } catch (Throwable $e) {
            report($e);

            if ($newImage) {
                $newPath = public_path('uploads/album/' . basename($newImage));
                if (is_file($newPath)) {
                    unlink($newPath);
                }
            }

            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Album could not be updated.');
        }
    }

    public function deleteAlbum(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('delete', $user->id)) {
            return response()->json([
                'type' => 'error',
                'message' => 'You are not authorized to delete albums.',
            ], 403);
        }

        $validator = Validator::make($request->all(), ['id' => 'required|integer|min:1']);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Invalid album identifier.',
            ], 422);
        }

        $album = $this->albumForUser($request->get('id'), $user);

        if (!$album) {
            return response()->json([
                'type' => 'error',
                'message' => 'Album not found or access denied.',
            ], 404);
        }

        $album->is_active = '0';
        $album->is_deleted = '1';
        $album->updated_by = $user->id;
        $album->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Album deleted successfully.',
        ]);
    }

    public function changeStatus(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAccess('publish', $user->id)) {
            return response()->json([
                'type' => 'error',
                'message' => 'You are not authorized to change album status.',
            ], 403);
        }

        $validator = Validator::make($request->all(), ['id' => 'required|integer|min:1']);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Invalid album identifier.',
            ], 422);
        }

        $album = $this->albumForUser($request->get('id'), $user);

        if (!$album) {
            return response()->json([
                'type' => 'error',
                'message' => 'Album not found or access denied.',
            ], 404);
        }

        $album->is_active = $album->is_active == '0' ? '1' : '0';
        $album->updated_by = $user->id;
        $album->save();

        return response()->json([
            'type' => 'success',
            'message' => $album->is_active == '1'
                ? 'The album has been activated successfully.'
                : 'The album has been deactivated successfully.',
            'icon' => $album->is_active == '1'
                ? '<i class="icon-check"></i>'
                : '<i class="icon-check-empty"></i>',
        ]);
    }
}

<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Services\AutomaticImages\AutomaticImageProcessor;
use App\Models\Bannerdetail;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BannerController extends ConfigurationController
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name    = ConfigurationController::site_name();
    }

    /*
     * get banner list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    /**
     * Store banner images at 1291x487 without cropping or stretching.
     * The complete original image is proportionally fitted on a #eeeeee canvas.
     */
    private function removeUnreferencedBannerFiles(?string $filename): void
    {
        if (!$filename || Banner::where('banner_attachment', $filename)->exists()) {
            return;
        }

        $base = public_path('uploads/banner');
        foreach ([$base . '/' . basename($filename), $base . '/originals/' . basename($filename)] as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    private function storeNormalizedBannerImage($imageFile, $destinationPath, $photoName)
    {
        $result = app(AutomaticImageProcessor::class)->process(
            $imageFile,
            $destinationPath,
            $photoName,
            'banner'
        );

        return $result['processed_path'];
    }

    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(16, $authUser->id);

        if ($viewAccess) {
            $banners = Banner::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(16, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(16, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(16, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(16, $authUser->id);

            return view('banner.admin.list')
                ->with('banners', $banners)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addAccess', $addAccess)
                ->with('editAccess', $editAccess)
                ->with('publishAccess', $publishAccess)
                ->with('deleteAccess', $deleteAccess);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get banner add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddBanner()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(16, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('banner.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * post banner add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addBanner(Request $request)
    {
        $authUser = Auth::user();

        if (!$authUser || !UserAccessControlModel::getAccessAddByModuleAndUserMapId(16, $authUser->id)) {
            abort(403);
        }

        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        $logo = null;

        try {
            $rules = ['attachment' => 'required|image|mimes:jpg,jpeg,png|max:2048'];
            foreach ($languages as $language) {
                $rules['banner_title_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $imageFile = $request->file('attachment');
                $extension = strtolower($imageFile->getClientOriginalExtension());
                $new_image_name = 'banner_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4));
                $logo = $new_image_name . '.' . $extension;

                $this->storeNormalizedBannerImage(
                    $imageFile,
                    public_path('uploads/banner'),
                    $logo
                );

                $objectBanner                    = new Banner();
                $objectBanner->banner_attachment = $logo;
                $objectBanner->created_by        = $authUser->id;
                $objectBanner->updated_by        = $authUser->id;
                $objectBanner->office_id         = $authUser->office_id;
                $objectBanner->is_active         = $request->get('is_active');
                $objectBanner->save();

                if ($objectBanner->id) {
                    foreach ($languages as $language) {
                        $insertArray[] = [
                            'banner_id'      => $objectBanner->id,
                            'language_id'    => $language->id,
                            'banner_title'   => $request->get('banner_title_' . $language->id),
                            'banner_caption' => $request->get('banner_caption_' . $language->id),
                            'banner_description' => $request->get('banner_description_' . $language->id),
                            'created_at'     => date("Y-m-d H:i:s"),
                            'updated_at'     => date("Y-m-d H:i:s"),
                        ];
                    }
                    DB::table('banner_detail')->insert($insertArray);

                    return Redirect::route('admin.banners')
                        ->with('type', 'success')
                        ->with('message', 'New banner is added successfully.');
                }
            }
        } catch (Throwable $e) {
            $this->removeUnreferencedBannerFiles($logo);
            report($e);
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New banner is not added.');
        }
    }

    /**
     * get banner edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditBanner($id)
    {

        try {
            $officeId     = Auth::user()->office_id;
            $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(16, Auth::User()->id);
            $editAccess   = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(16, Auth::User()->id);
            $objectBanner = Banner::find($id);

            if ($objectBanner && (string) $objectBanner->office_id === (string) $officeId && $editAccess) {
                $languages = Language::getActiveLanguages();
                $banners   = Banner::getBannerDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('banner.admin.edit')
                    ->with('banners', $banners)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('addAccess', $addAccess);
            } else {
                return view('error')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name);
            }
        } catch (Throwable $e) {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * post banner edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editBanner(Request $request)
    {
        $bannerId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $officeId = Auth::user()->office_id;
        $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(16, Auth::User()->id);
        $objectBanner = Banner::where('id', $bannerId)->where('office_id', $officeId)->first();

        if (!$objectBanner || !$editAccess) {
            abort(403);
        }

        $languages = Language::getActiveLanguages();
        $banners   = Banner::getBannerDetailsWithLanguages($bannerId);
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(16, Auth::User()->id);

        $logo = null;

        try {
            $rules = ['attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'];
            foreach ($languages as $language) {
                $rules['banner_title_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $imageFile = $request->file('attachment');
                $logo = null;

                if ($imageFile) {
                    $extension = strtolower($imageFile->getClientOriginalExtension());
                    $new_image_name = 'banner_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4));
                    $logo = $new_image_name . '.' . $extension;

                    $this->storeNormalizedBannerImage(
                        $imageFile,
                        public_path('uploads/banner'),
                        $logo
                    );
                } else {
                    $attachment_deleted = $request->get('attachment_deleted');
                }

                $objectBanner->is_active = $request->get('is_active');

                if ($logo) {
                    $objectBanner->banner_attachment = $logo;
                } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                    $objectBanner->banner_attachment = null;
                }

                $objectBanner->updated_by = Auth::user()->id;
                $objectBanner->save();

                if ($objectBanner->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        // Only update a detail belonging to this banner and language.
                        $objectBannerDetail = Bannerdetail::where('banner_id', $objectBanner->id)
                            ->where('language_id', $language->id)
                            ->first();
                        if ($objectBannerDetail) {
                            $objectBannerDetail->banner_title   = trim($request->get('banner_title_' . $language->id));
                            $objectBannerDetail->banner_caption = trim($request->get('banner_caption_' . $language->id));
                            $objectBannerDetail->banner_description = $request->get('banner_description_' . $language->id);
                            $objectBannerDetail->save();
                            continue;
                        } else {
                            $insertArray[] = [
                                'banner_id'      => $objectBanner->id,
                                'language_id'    => $language->id,
                                'banner_title'   => trim($request->get('banner_title_' . $language->id)),
                                'banner_caption' => trim($request->get('banner_caption_' . $language->id)),
                                'banner_description' => $request->get('banner_description_' . $language->id),
                                'created_at'     => date("Y-m-d H:i:s"),
                                'updated_at'     => date("Y-m-d H:i:s"),
                            ];
                        }
                    }

                    if (count($insertArray) > 0) {
                        DB::table('banner_detail')->insert($insertArray);
                    }
                }
                return Redirect::route('admin.banners')
                    ->with('type', 'success')
                    ->with('message', 'Banner is updated successfully.');
            }
        } catch (Throwable $e) {
            $this->removeUnreferencedBannerFiles($logo);
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Banner is not updated.');
        }
    }

    /*
     * banner delete action admin panel
     * @return JSON
     */
    public function deleteBanner(Request $request)
    {
        $authUser = Auth::user();

        if (
            !$authUser ||
            !UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(16, $authUser->id)
        ) {
            abort(403);
        }

        $objectBanner = Banner::where('id', $request->get('id'))
            ->where('office_id', $authUser->office_id)
            ->where('is_deleted', '0')
            ->first();

        if (!$objectBanner) {
            exit(json_encode([
                'type' => 'error',
                'message' => 'Banner not found or access denied.',
            ]));
        }

        $objectBanner->is_active = '0';
        $objectBanner->is_deleted = '1';
        $objectBanner->updated_by = $authUser->id;
        $objectBanner->save();

        exit(json_encode([
            'type' => 'success',
            'message' => 'Banner deleted Successfully.',
        ]));
    }

    /*
     * banner status change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $authUser = Auth::user();

        if (
            !$authUser ||
            !UserAccessControlModel::getAccessPublishByModuleAndUserMapId(16, $authUser->id)
        ) {
            abort(403);
        }

        $objectBanner = Banner::where('id', $request->get('id'))
            ->where('office_id', $authUser->office_id)
            ->where('is_deleted', '0')
            ->first();

        if (!$objectBanner) {
            exit(json_encode([
                'type' => 'error',
                'message' => 'Banner not found or access denied.',
            ]));
        }

        $objectBanner->is_active = $objectBanner->is_active == '0' ? '1' : '0';
        $objectBanner->updated_by = $authUser->id;
        $objectBanner->save();

        exit(json_encode([
            'type' => 'success',
            'message' => $objectBanner->is_active == '1'
                ? 'The banner has been activated successfully'
                : 'The banner has been deactivated successfully',
            'icon' => $objectBanner->is_active == '1'
                ? '<i class="icon-check"></i>'
                : '<i class="icon-check-empty"></i>',
        ]));
    }
}

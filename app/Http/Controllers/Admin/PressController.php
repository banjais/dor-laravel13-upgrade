<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Press;
use App\Models\Pressdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PressController extends ConfigurationController
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
     * get press list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(10, $authUser->id);

        if ($viewAccess) {
            $presses = Press::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderBy('created_at', 'desc')
                ->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(10, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(10, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(10, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(10, $authUser->id);

            return view('press.admin.list')
                ->with('presses', $presses)
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
     * get press add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddPress()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(10, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('press.admin.add')
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
     * post press add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addPress(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['press_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip';
            foreach ($languages as $language) {
                $rules['press_heading_' . $language->id]     = 'required';
                $rules['press_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $pressUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('press_url')))), '-');
                $pressExist = Press::checkExist($pressUrl, $authUser->office_id);
                if ($pressExist) {
                    $message = 'Press with url <b>' . $pressUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/press/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|zip){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "press" . "_" . time() . "_" . $authUser->office_id . "_" . Auth::user()->id;

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 10MB!");
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectPress                         = new Press();
                    $objectPress->press_url              = $pressUrl;
                    $objectPress->press_meta_tags        = $request->get('press_meta_tags');
                    $objectPress->press_meta_description = $request->get('press_meta_description');
                    $objectPress->published_date         = $request->get('published_date');
                    $objectPress->is_active              = $request->get('is_active');
                    $objectPress->press_attachment       = $logo;
                    $objectPress->created_by             = $authUser->id;
                    $objectPress->updated_by             = $authUser->id;
                    $objectPress->office_id              = $authUser->office_id;
                    $objectPress->download_count = 0;
                    $objectPress->press_meta_tags = '';
                    $objectPress->press_meta_description = '';
                    $objectPress->save();

                    if ($objectPress->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'press_id'                => $objectPress->id,
                                'language_id'             => $language->id,
                                'press_heading'           => $request->get('press_heading_' . $language->id),
                                'press_description'       => $request->get('press_description_' . $language->id),
                                'press_page_title'        => $request->get('press_page_title_' . $language->id),
                                'press_short_description' => '',
                                'created_at'              => date("Y-m-d H:i:s"),
                                'updated_at'              => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('press_detail')->insert($insertArray);

                        return Redirect::route('admin.presses')
                            ->with('type', 'success')
                            ->with('message', 'New press is added successfully.');
                    }
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New press is not added.');
        }
    }

    /**
     * get press edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditPress($id)
    {

        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(10, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(10, Auth::User()->id);
            $pressData  = Press::find($id);

            if ($pressData->office_id == $officeId && $editAccess) {
                $languages = Language::getActiveLanguages();
                $Presses   = Press::getPressDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('press.admin.edit')
                    ->with('Presses', $Presses)
                    ->with('pressData', $pressData)
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
            dd($e->getMessage());
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * post press edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editPress(Request $request)
    {
        $pressId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        $Presses   = Press::getPressDetailsWithLanguages($pressId);
        $officeId  = Auth::user()->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(10, Auth::User()->id);
        $pressData = Press::find($pressId);

        try {
            $rules = ['press_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip';
            foreach ($languages as $language) {
                $rules['press_heading_' . $language->id]     = 'required';
                $rules['press_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $pressUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('press_url')))), '-');
                $pressExist = Press::checkExist($pressUrl, $officeId);
                // dd($pressExist);

                if ($pressExist && $pressExist != $pressId) {
                    $message = 'Press with url <b>' . $pressUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/press/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|ppt|pptx|doc|docx){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "press" . "_" . time() . "_" . $officeId . "_" . Auth::user()->id;

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                        }
                    } else {
                        $attachment_deleted = $request->get('attachment_deleted');
                    }

                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectPress                         = Press::find($pressId);
                    $objectPress->press_url              = $pressUrl;
                    $objectPress->press_meta_tags        = $request->get('press_meta_tags');
                    if (!$objectPress->press_meta_tags) {
                        $objectPress->press_meta_tags = '';
                    }
                    $objectPress->press_meta_description = $request->get('press_meta_description');
                    if (!$objectPress->press_meta_description) {
                        $objectPress->press_meta_description = '';
                    }
                    $objectPress->published_date         = $request->get('published_date');
                    $objectPress->is_active              = $request->get('is_active');

                    if ($logo) {
                        if (file_exists("uploads/press/" . $objectPress->press_attachment) && $objectPress->press_attachment != '') {
                            unlink('uploads/press/' . $objectPress->press_attachment);
                        }
                        $objectPress->press_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        if (file_exists("uploads/press/" . $objectPress->press_attachment) && $objectPress->press_attachment != '') {
                            unlink('uploads/press/' . $objectPress->press_attachment);
                        }

                        $objectPress->press_attachment = null;
                    }

                    $objectPress->updated_by = Auth::user()->id;
                    $objectPress->save();

                    if ($objectPress->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId = $request->get('detail_id_' . $language->id);

                            $objectPressDetail = Pressdetail::find($detailId);
                            if ($objectPressDetail) {
                                $objectPressDetail->press_heading           = trim($request->get('press_heading_' . $language->id));
                                $objectPressDetail->press_description       = trim($request->get('press_description_' . $language->id));
                                $objectPressDetail->press_page_title        = trim($request->get('press_page_title_' . $language->id));
                                $objectPressDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'press_id'                => $objectPress->id,
                                    'language_id'             => $language->id,
                                    'press_heading'           => trim($request->get('press_heading_' . $language->id)),
                                    'press_description'       => trim($request->get('press_description_' . $language->id)),
                                    'press_page_title'        => trim($request->get('press_page_title_' . $language->id)),
                                    'created_at'              => date("Y-m-d H:i:s"),
                                    'updated_at'              => date("Y-m-d H:i:s"),
                                ];
                            }
                        }

                        if (count($insertArray) > 0) {
                            DB::table('press_detail')->insert($insertArray);
                        }
                    }
                    return Redirect::route('admin.presses')
                        ->with('type', 'success')
                        ->with('message', 'Press is successfully updated.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Press is not updated.');
        }
    }

    /*
     * press delete action admin panel
     * @return JSON
     */
    public function deletePress(Request $request)
    {
        $pressId                 = $request->get('id');
        $objectPress             = Press::find($pressId);
        $objectPress->is_active  = '0';
        $objectPress->is_deleted = '1';
        $objectPress->save();

        if ($objectPress->id) {
            $data['type']    = "success";
            $data['message'] = "Press deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * press staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $pressId     = $request->get('id');
        $objectPress = Press::find($pressId);
        if ($objectPress->is_active == '0') {
            $objectPress->is_active = '1';
            $objectPress->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The press has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectPress->is_active = '0';
            $objectPress->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The press has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

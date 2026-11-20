<?php

/**
 * @author Sunil Adhikari
 * @copy Peace Nepal DOT Com
 * June 2015
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Law;
use App\Models\Lawdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LawController extends ConfigurationController
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
     * get law list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(13, $authUser->id);

        if ($viewAccess) {
            $laws          = Law::getUndeletedLaws();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(13, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(13, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(13, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(13, $authUser->id);

            return view('laws.admin.list')
                ->with('laws', $laws)
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
     * get law add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddLaw()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(13, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('laws.admin.add')
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
     * post law add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addLaw(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['law_url' => 'required|unique:laws,law_url'];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip';
            foreach ($languages as $language) {
                $rules['law_heading_' . $language->id]     = 'required';
                $rules['law_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $lawUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('law_url')))), '-');
                $lawExist = Law::checkExist($lawUrl);
                if ($lawExist) {
                    $message = 'law with url <b>' . $lawUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/law/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|zip){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "law" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectLaw                       = new Law();
                    $objectLaw->law_url              = $lawUrl;
                    $objectLaw->law_meta_tags        = $request->get('law_meta_tags');
                    $objectLaw->law_meta_description = $request->get('law_meta_description');
                    $objectLaw->published_date       = $request->get('published_date');
                    $objectLaw->is_active            = $request->get('is_active');
                    $objectLaw->office_id                 = $authUser->office_id;
                    $objectLaw->law_attachment       = $logo;
                    $objectLaw->created_by           = $authUser->id;
                    $objectLaw->updated_by           = $authUser->id;
                    $objectLaw->download_count      = 0;
                    $objectLaw->save();

                    if ($objectLaw->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'law_id'          => $objectLaw->id,
                                'language_id'     => $language->id,
                                'law_heading'     => $request->get('law_heading_' . $language->id),
                                'law_description' => $request->get('law_description_' . $language->id),
                                'law_page_title'  => $request->get('law_page_title_' . $language->id),
                                'created_at'      => date("Y-m-d H:i:s"),
                                'updated_at'      => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('law_detail')->insert($insertArray);

                        return Redirect::route('admin.laws')
                            ->with('type', 'success')
                            ->with('message', 'New law is added successfully.');
                    }
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New law is not added.');
        }
    }

    /**
     * get law edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditLaw($id)
    {

        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(13, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(13, Auth::User()->id);
            $lawData    = Law::find($id);

            if ($lawData && $editAccess) {
                $languages = Language::getActiveLanguages();
                $laws      = Law::getLawDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('laws.admin.edit')
                    ->with('laws', $laws)
                    ->with('lawData', $lawData)
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
     * post law edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editLaw(Request $request)
    {
        $lawId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        $laws      = Law::getLawDetailsWithLanguages($lawId);
        $officeId  = Auth::user()->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(13, Auth::User()->id);
        $lawData   = Law::find($lawId);

        try {

            $rules = ['law_url' => 'required|unique:laws,law_url,' . $lawId];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip';
            foreach ($languages as $language) {
                $rules['law_heading_' . $language->id]     = 'required';
                $rules['law_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $lawUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('law_url')))), '-');
                $lawExist = Law::checkExist($lawUrl);

                if ($lawExist && $lawExist != $lawId) {
                    $message = 'Law with url <b>' . $lawUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/law/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|zip){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "law" . "_" . time();

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

                    $objectLaw                       = Law::find($lawId);
                    $objectLaw->law_url              = $lawUrl;
                    $objectLaw->law_meta_tags        = $request->get('law_meta_tags');
                    $objectLaw->law_meta_description = $request->get('law_meta_description');
                    $objectLaw->published_date       = $request->get('published_date');
                    $objectLaw->is_active            = $request->get('is_active');

                    if ($logo) {
                        $objectLaw->law_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        $objectLaw->law_attachment = null;
                    }

                    $objectLaw->updated_by = Auth::user()->id;
                    $objectLaw->save();

                    if ($objectLaw->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId = $request->get('detail_id_' . $language->id);

                            $objectLawDetail = Lawdetail::find($detailId);
                            if ($objectLawDetail) {
                                $objectLawDetail->law_heading     = trim($request->get('law_heading_' . $language->id));
                                $objectLawDetail->law_description = trim($request->get('law_description_' . $language->id));
                                $objectLawDetail->law_page_title  = trim($request->get('law_page_title_' . $language->id));
                                $objectLawDetail->save();
                                continue;
                            } else {
                                $publishDate   = ($language->id == 2) ? date("Y-m-d", strtotime($request->get('law_date_' . $language->id))) : trim($request->get('law_date_' . $language->id));
                                $insertArray[] = [
                                    'law_id'          => $objectLaw->id,
                                    'language_id'     => $language->id,
                                    'law_heading'     => trim($request->get('law_heading_' . $language->id)),
                                    'law_description' => trim($request->get('law_description_' . $language->id)),
                                    'law_page_title'  => trim($request->get('law_page_title_' . $language->id)),
                                    'created_at'      => date("Y-m-d H:i:s"),
                                    'updated_at'      => date("Y-m-d H:i:s"),
                                ];
                            }
                        }

                        if (count($insertArray) > 0) {
                            DB::table('law_detail')->insert($insertArray);
                        }
                    }
                    return Redirect::route('admin.laws')
                        ->with('type', 'success')
                        ->with('message', 'Law is updated successfully.');
                }
            }
        } catch (Throwable $e) {
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Law is not updated.');
        }
    }

    /*
     * law delete action admin panel
     * @return JSON
     */
    public function deleteLaw(Request $request)
    {
        $lawId                 = $request->get('id');
        $objectLaw             = Law::find($lawId);
        $objectLaw->is_active  = '0';
        $objectLaw->is_deleted = '1';
        $objectLaw->save();

        if ($objectLaw->id) {
            $data['type']    = "success";
            $data['message'] = "Law deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * law staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $lawId     = $request->get('id');
        $objectLaw = Law::find($lawId);
        if ($objectLaw->is_active == '0') {
            $objectLaw->is_active = '1';
            $objectLaw->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The law has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectLaw->is_active = '0';
            $objectLaw->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The law has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

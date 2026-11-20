<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Guideline;
use App\Models\GuidelineDetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class GuidelineController extends ConfigurationController
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
     * get guideline list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(32, Auth::User()->id);

        if ($viewAccess) {
            $guidelines = Guideline::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderBy('created_at', 'desc')
                ->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(4, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(4, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(4, $authUser->guideline);

            return view('guideline.admin.list')
                ->with('guidelines', $guidelines)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addAccess', $addAccess)
                ->with('editAccess', $editAccess)
                ->with('publishAccess', $publishAccess)
                ->with('deleteAccess', $deleteAccess);
        } else {
            return view('error')
                ->with('abs_site_url', $this->guideline_e_url)
                ->with('rel_site_url', $this->guidelinete_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get guideline add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddGuideline()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(32, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('guideline.admin.add')
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
     * post guideline add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addGuideline(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['guideline_url' => 'required'];
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip,rar';
            foreach ($languages as $language) {
                $rules['guideline_heading_' . $language->id]     = 'required';
                $rules['guideline_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);



            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $guidelineUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('guideline_url')))), '-');
                $guidelineExists = Guideline::checkExist($guidelineUrl, $authUser->office_id);
                if ($guidelineExists) {
                    $message = 'Guideline with url <b>' . $guidelineUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/guideline/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|zip|rar){1}$/i";
                    $maximum_filesize = 100 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "guideline" . "_" . time() . "_" . $authUser->office_id . "_" . Auth::user()->id;

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 10MB!");
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectGuideline                          = new Guideline();
                    $objectGuideline->guideline_url              = $guidelineUrl;
                    $objectGuideline->guideline_meta_tags        = $request->get('guideline_meta_tags');
                    $objectGuideline->guideline_meta_description = $request->get('guideline_meta_description');
                    $objectGuideline->published_date          = trim($request->get('published_date'));
                    $objectGuideline->is_active               = $request->get('is_active');
                    $objectGuideline->guideline_attachment       = $logo;
                    $objectGuideline->created_by              = $authUser->id;
                    $objectGuideline->updated_by              = $authUser->id;
                    $objectGuideline->office_id               = $authUser->office_id;
                    $objectGuideline->download_count          = 0;
                    $objectGuideline->save();

                    if ($objectGuideline->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'guideline_id'                => $objectGuideline->id,
                                'language_id'              => $language->id,
                                'guideline_heading'           => $request->get('guideline_heading_' . $language->id),
                                'guideline_description'       => $request->get('guideline_description_' . $language->id),
                                'guideline_page_title'        => $request->get('guideline_page_title_' . $language->id),
                                'guideline_short_description'  => '',
                                'created_at'               => date("Y-m-d H:i:s"),
                                'updated_at'               => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('guideline_detail')->insert($insertArray);

                        return Redirect::route('admin.guidelines')
                            ->with('type', 'success')
                            ->with('message', 'New guidline is added successfully.');
                    }
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New guidline is not added.');
        }
    }

    /**
     * get guidline edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditGuideline($id)
    {

        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(32, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(32, Auth::User()->id);
            $guidelineData = Guideline::find($id);

            if ($guidelineData->office_id == $officeId && $editAccess) {
                $languages = Language::getActiveLanguages();
                $guidelines   = Guideline::getGuidelineDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('guideline.admin.edit')
                    ->with('guidelines', $guidelines)
                    ->with('guidelineData', $guidelineData)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('addAccess', $addAccess);
            } else {
                return view('error')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->guideline_e_url)
                    ->with('site_name', $this->guidelinee);
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
     * post guideline edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editGuideline(Request $request)
    {
        $guidelineId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages  = Language::getActiveLanguages();
        $guidelines    = Guideline::getGuidelineDetailsWithLanguages($guidelineId);
        $officeId   = Auth::user()->office_id;
        $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(32, Auth::User()->id);
        $guidelineData = Guideline::find($guidelineId);

        try {
            $rules = ['guideline_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip,rar';
            foreach ($languages as $language) {
                $rules['guideline_heading_' . $language->id]     = 'required';
                $rules['guideline_description_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $guidelineUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('guideline_url')))), '-');
                $guidelineExists = Guideline::checkExist($guidelineUrl, $officeId);

                if ($guidelineExists && $guidelineExists != $guidelineId) {
                    $message = 'Guideline with url <b>' . $guidelineUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/guideline/';
                    $rEFileTypes      = "/^\.(zip|jpg|jpeg|pdf|rar){1}$/i";
                    $maximum_filesize = 100 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "guideline" . "_" . time() . "_" . $officeId . "_" . Auth::user()->id;

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

                    $objectGuideline                          = Guideline::find($guidelineId);
                    $objectGuideline->guideline_url              = $guidelineUrl;
                    $objectGuideline->guideline_meta_tags        = $request->get('guideline_meta_tags');
                    $objectGuideline->guideline_meta_description = $request->get('guideline_meta_description');
                    $objectGuideline->published_date          = trim($request->get('published_date'));
                    $objectGuideline->is_active               = $request->get('is_active');
                    if ($logo) {
                        if (file_exists("uploads/guideline/" . $objectGuideline->guideline_attachment) && $objectGuideline->guideline_attachment != '') {
                            unlink('uploads/guideline/' . $objectGuideline->guideline_attachment);
                        }
                        $objectGuideline->guideline_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        if (file_exists("uploads/guideline/" . $objectGuideline->guideline_attachment) && $objectGuideline->guideline_attachment != '') {
                            unlink('uploads/guideline/' . $objectGuideline->guideline_attachment);
                        }

                        $objectGuideline->guideline_attachment = null;
                    }

                    $objectGuideline->updated_by = Auth::user()->id;
                    $objectGuideline->save();

                    if ($objectGuideline->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId = $request->get('detail_id_' . $language->id);

                            $objectGuidelineDetail = GuidelineDetail::find($detailId);
                            if ($objectGuidelineDetail) {
                                $objectGuidelineDetail->guideline_heading           = trim($request->get('guideline_heading_' . $language->id));
                                $objectGuidelineDetail->guideline_description       = trim($request->get('guideline_description_' . $language->id));
                                $objectGuidelineDetail->guideline_page_title        = trim($request->get('guideline_page_title_' . $language->id));
                                $objectGuidelineDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'guideline_id'                => $objectGuideline->id,
                                    'language_id'              => $language->id,
                                    'guideline_heading'           => trim($request->get('guideline_heading_' . $language->id)),
                                    'guideline_description'       => trim($request->get('guideline_description_' . $language->id)),
                                    'guideline_page_title'        => trim($request->get('guideline_page_title_' . $language->id)),
                                    'created_at'               => date("Y-m-d H:i:s"),
                                    'updated_at'               => date("Y-m-d H:i:s"),
                                ];
                            }
                        }

                        if (count($insertArray) > 0) {
                            DB::table('guideline_detail')->insert($insertArray);
                        }
                    }
                    return Redirect::route('admin.guidelines')
                        ->with('type', 'success')
                        ->with('message', 'Guideline is successfully updated.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Guideline is not updated.');
        }
    }

    /*
     * guideline delete action admin panel
     * @return JSON
     */
    public function deleteGuideline(Request $request)
    {
        $itemId                 = $request->get('id');
        $objectGuideline             = Guideline::find($itemId);
        $objectGuideline->is_active  = '0';
        $objectGuideline->is_deleted = '1';
        $objectGuideline->save();

        if ($objectGuideline->id) {
            $data['type']    = "success";
            $data['message'] = "Guideline deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * guideline stauts change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $guidelineId     = $request->get('id');
        $objectGuideline = Guideline::find($guidelineId);
        if ($objectGuideline->is_active == '0') {
            $objectGuideline->is_active = '1';
            $objectGuideline->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The guideline has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectGuideline->is_active = '0';
            $objectGuideline->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The guideline has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

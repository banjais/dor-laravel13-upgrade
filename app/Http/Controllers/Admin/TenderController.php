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
use App\Models\Tender;
use App\Models\Tenderdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TenderController extends ConfigurationController
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
     * get tender list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(22, $authUser->id);

        if ($viewAccess) {
            $tenders = Tender::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderBy('created_at', 'desc')
                ->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(22, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(22, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(22, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(22, $authUser->id);

            return view('tender.admin.list')
                ->with('tenders', $tenders)
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
     * get tender add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddTender()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(22, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('tender.admin.add')
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
     * post tender add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addTender(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $officeId     = $authUser->office_id;

        try {
            $rules   = ['tender_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['expiry_date']    = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip';
            foreach ($languages as $language) {
                $rules['tender_heading_' . $language->id]     = 'required';
                $rules['tender_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $tenderUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('tender_url')))), '-');
                $tenderExist = Tender::checkExist($tenderUrl, $authUser->office_id);
                if ($tenderExist) {
                    $message = 'Tender with url <b>' . $tenderUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/tender/';
                    $rEFileTypes      = "/^\.(zip|jpg|jpeg|pdf){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "tender" . "_" . time() . "_" . $officeId . "_" . Auth::user()->id;

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 10 MB!");
                        }
                    }
                    $logo                                  = isset($attachment) ? $new_image_name . $extension : null;
                    $objectTender                          = new Tender();
                    $objectTender->tender_url              = $tenderUrl;
                    $objectTender->tender_meta_tags        = $request->get('tender_meta_tags');
                    $objectTender->tender_meta_description = $request->get('tender_meta_description');
                    $objectTender->is_active               = $request->get('is_active');
                    $objectTender->tender_attachment       = $logo;
                    $objectTender->published_date          = trim($request->get('published_date'));
                    $objectTender->expiry_date             = trim($request->get('expiry_date'));
                    $objectTender->created_by              = $authUser->id;
                    $objectTender->updated_by              = $authUser->id;
                    $objectTender->office_id               = $authUser->office_id;
                    $objectTender->download_count = 0;
                    $objectTender->save();

                    if ($objectTender->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'tender_id'                => $objectTender->id,
                                'language_id'              => $language->id,
                                'tender_heading'           => $request->get('tender_heading_' . $language->id),
                                'tender_description'       => $request->get('tender_description_' . $language->id),
                                'tender_page_title'        => $request->get('tender_page_title_' . $language->id),
                                'tender_short_description' => '',
                                'created_at'               => date("Y-m-d H:i:s"),
                                'updated_at'               => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('tender_detail')->insert($insertArray);

                        return Redirect::route('admin.tenders')
                            ->with('type', 'success')
                            ->with('message', 'New tender notice is added successfully.');
                    }
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New tender notice is not added.');
        }
    }

    /**
     * get tender edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditTender($id)
    {

        try {
            $officeId     = Auth::user()->office_id;
            $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(22, Auth::User()->id);
            $editAccess   = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(22, Auth::User()->id);
            $objectTender = Tender::find($id);

            if ($objectTender->office_id == $officeId && $editAccess) {
                $languages = Language::getActiveLanguages();
                $tenders   = Tender::getTenderDetailsWithLanguages($id);

                return view('tender.admin.edit')
                    ->with('tenderData', $objectTender)
                    ->with('tenders', $tenders)
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
     * post tender edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editTender(Request $request)
    {
        $tenderId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages    = Language::getActiveLanguages();
        $tenders      = Tender::getTenderDetailsWithLanguages($tenderId);
        $officeId     = Auth::user()->office_id;
        $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(22, Auth::User()->id);
        $objectTender = Tender::find($tenderId);

        try {
            $rules = ['tender_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['expiry_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip';
            foreach ($languages as $language) {
                $rules['tender_heading_' . $language->id]     = 'required';
                $rules['tender_description_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $tenderUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('tender_url')))), '-');
                $tenderExist = Tender::checkExist($tenderUrl, $officeId);

                if ($tenderExist && $tenderExist != $tenderId) {
                    $message = 'Tender with url <b>' . $tenderUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/tender/';
                    $rEFileTypes      = "/^\.(zip|jpg|jpeg|pdf){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "tender" . "_" . time() . "_" . $officeId . "_" . Auth::user()->id;

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

                    $objectTender->tender_url              = $tenderUrl;
                    $objectTender->published_date          = trim($request->get('published_date'));
                    $objectTender->expiry_date             = trim($request->get('expiry_date'));
                    $objectTender->tender_meta_tags        = $request->get('tender_meta_tags');
                    $objectTender->tender_meta_description = $request->get('tender_meta_description');
                    $objectTender->is_active               = $request->get('is_active');

                    if ($logo) {
                        if (file_exists("uploads/tender/" . $objectTender->tender_attachment) && $objectTender->tender_attachment != '') {
                            unlink('uploads/tender/' . $objectTender->tender_attachment);
                        }
                        $objectTender->tender_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        if (file_exists("uploads/tender/" . $objectTender->tender_attachment) && $objectTender->tender_attachment != '') {
                            unlink('uploads/tender/' . $objectTender->tender_attachment);
                        }

                        $objectTender->tender_attachment = null;
                    }

                    $objectTender->updated_by = Auth::user()->id;
                    $objectTender->save();

                    if ($objectTender->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId = $request->get('detail_id_' . $language->id);

                            $objectTenderDetail = Tenderdetail::find($detailId);
                            if ($objectTenderDetail) {
                                $objectTenderDetail->tender_heading           = trim($request->get('tender_heading_' . $language->id));
                                $objectTenderDetail->tender_description       = trim($request->get('tender_description_' . $language->id));
                                $objectTenderDetail->tender_page_title        = trim($request->get('tender_page_title_' . $language->id));
                                $objectTenderDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'tender_id'                => $objectTender->id,
                                    'language_id'              => $language->id,
                                    'tender_heading'           => trim($request->get('tender_heading_' . $language->id)),
                                    'tender_description'       => trim($request->get('tender_description_' . $language->id)),
                                    'tender_page_title'        => trim($request->get('tender_page_title_' . $language->id)),
                                    'created_at'               => date("Y-m-d H:i:s"),
                                    'updated_at'               => date("Y-m-d H:i:s"),
                                ];
                            }
                        }

                        if (count($insertArray) > 0) {
                            DB::table('tender_detail')->insert($insertArray);
                        }
                    }
                    return Redirect::route('admin.tenders')
                        ->with('type', 'success')
                        ->with('message', 'Tender is successfully updated.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', $e->getMessage());
        }
    }

    /*
     * tender delete action admin panel
     * @return JSON
     */
    public function deleteTender(Request $request)
    {
        $tenderId                 = $request->get('id');
        $objectTender             = Tender::find($tenderId);
        $objectTender->is_active  = '0';
        $objectTender->is_deleted = '1';
        $objectTender->save();

        if ($objectTender->id) {
            $data['type']    = "success";
            $data['message'] = "Tender deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * tender staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $tenderId     = $request->get('id');
        $objectTender = Tender::find($tenderId);
        if ($objectTender->is_active == '0') {
            $objectTender->is_active = '1';
            $objectTender->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The tender has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectTender->is_active = '0';
            $objectTender->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The tender has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

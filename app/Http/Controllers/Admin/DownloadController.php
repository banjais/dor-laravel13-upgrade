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
use App\Models\Download;
use App\Models\Downloaddetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DownloadController extends ConfigurationController
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
     * get download list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(18, $authUser->id);

        if ($viewAccess) {
            $downloads = Download::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderByDesc('created_at')
                ->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(18, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(18, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(18, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(18, $authUser->id);

            return view('download.admin.list')
                ->with('downloads', $downloads)
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
     * get download add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddDownload()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(18, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('download.admin.add')
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
     * post download add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addDownload(Request $request)
    {
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $officeId = Auth::user()->office_id;

        try {
            $rules['published_date'] = 'required';
            $rules['download_url'] = 'required|unique:download,download_url,NULL,id,is_deleted,0';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip';
            foreach ($languages as $language) {
                $rules['download_heading_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $imageFile        = $request->file('attachment');
                $destinationPath  = 'uploads/download/';
                $rEFileTypes      = "/^\.(zip|jpg|jpeg|pdf){1}$/i";
                $maximum_filesize = 10 * 1024 * 1024;

                if ($imageFile) {
                    $filename       = $imageFile->getClientOriginalName();
                    $extension      = strrchr($filename, '.');
                    $size           = $imageFile->getSize();
                    $new_image_name = "download" . "_" . time();

                    if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                        $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                    } else if (preg_match($rEFileTypes, $extension) == false) {
                        Session::flash('attachment-error', 'Warning : Invalid Image File!');
                    } else if ($size > $maximum_filesize) {
                        Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                    }
                }
                $logo = isset($attachment) ? $new_image_name . $extension : null;

                $objectDownload                      = new Download();
                $objectDownload->download_attachment = $logo;
                $objectDownload->download_url        = $request->get('download_url');
                $objectDownload->published_date      = $request->get('published_date');
                $objectDownload->is_active           = $request->get('is_active');
                $objectDownload->created_by          = Auth::user()->id;
                $objectDownload->updated_by          = Auth::user()->id;
                $objectDownload->office_id           = $officeId;
                $objectDownload->download_count           = 0;
                $objectDownload->save();

                if ($objectDownload->id) {
                    foreach ($languages as $language) {
                        $downlaodDate  = ($language->id == 2) ? date("Y-m-d", strtotime($request->get('download_date_' . $language->id))) : trim($request->get('download_date_' . $language->id));
                        $insertArray[] = [
                            'download_id'      => $objectDownload->id,
                            'language_id'      => $language->id,
                            'download_heading' => $request->get('download_heading_' . $language->id),
                            'created_at'       => date("Y-m-d H:i:s"),
                            'updated_at'       => date("Y-m-d H:i:s"),
                        ];
                    }
                    DB::table('download_detail')->insert($insertArray);

                    return Redirect::route('admin.downloads')
                        ->with('type', 'success')
                        ->with('message', 'Download is successfully added.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Download is not added.');
        }
    }

    /**
     * get download edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditDownload($id)
    {
        try {
            $officeId     = Auth::user()->office_id;
            $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(18, Auth::User()->id);
            $editAccess   = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(18, Auth::User()->id);
            $downloadData = Download::find($id);

            if ($downloadData->office_id == $officeId && $editAccess) {
                $languages = Language::getActiveLanguages();
                $downloads = Download::getDownloadDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('download.admin.edit')
                    ->with('downloads', $downloads)
                    ->with('downloadData', $downloadData)
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
     * post download edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editDownload(Request $request)
    {
        $downloadId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages    = Language::getActiveLanguages();
        $downloads    = Download::getDownloadDetailsWithLanguages($downloadId);
        $officeId     = Auth::user()->office_id;
        $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(18, Auth::User()->id);
        $downloadData = Download::find($downloadId);

        try {
            $rules['published_date'] = 'required';
            $rules['download_url'] = 'required|unique:download,download_url,' . $downloadId . ',id,is_deleted,0';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,zip';
            foreach ($languages as $language) {
                $rules['download_heading_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $imageFile        = $request->file('attachment');
                $destinationPath  = 'uploads/download/';
                $rEFileTypes      = "/^\.(jpg|jpeg|pdf|zip){1}$/i";
                $maximum_filesize = 10 * 1024 * 1024;

                if ($imageFile) {
                    $filename       = $imageFile->getClientOriginalName();
                    $extension      = strrchr($filename, '.');
                    $size           = $imageFile->getSize();
                    $new_image_name = "download" . "_" . time();

                    if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                        $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                    } else if (preg_match($rEFileTypes, $extension) == false) {
                        Session::flash('attachment-error', 'Warning : Invalid Image File!');
                    } else if ($size > $maximum_filesize) {
                        Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                    }
                }else {
                    $attachment_deleted = $request->get('attachment_deleted');
                }

                $logo = isset($attachment) ? $new_image_name . $extension : null;
                // dd($logo);

                $objectDownload                 = Download::find($downloadId);
                $objectDownload->download_url   = $request->get('download_url');
                $objectDownload->published_date = $request->get('published_date');
                $objectDownload->is_active      = $request->get('is_active');

                if ($logo) {
                    $objectDownload->download_attachment = $logo;
                }else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                    $objectDownload->download_attachment = null;
                }

                $objectDownload->updated_by = Auth::user()->id;
                $objectDownload->save();

                if ($objectDownload->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);

                        $objectDownloadDetail = Downloaddetail::find($detailId);
                        if ($objectDownloadDetail) {
                            $objectDownloadDetail->download_heading = trim($request->get('download_heading_' . $language->id));
                            $objectDownloadDetail->save();
                            continue;
                        } else {
                            // $downlaodDate  = ($language->id == 2) ? date("Y-m-d", strtotime($request->get('download_date_' . $language->id))) : trim($request->get('download_date_' . $language->id));
                            $insertArray[] = [
                                'download_id'      => $objectDownload->id,
                                'language_id'      => $language->id,
                                'download_heading' => trim($request->get('download_heading_' . $language->id)),
                                'created_at'       => date("Y-m-d H:i:s"),
                                'updated_at'       => date("Y-m-d H:i:s"),
                            ];
                        }
                    }

                    if (count($insertArray) > 0) {
                        DB::table('download_detail')->insert($insertArray);
                    }
                }
                return Redirect::route('admin.downloads')
                    ->with('type', 'success')
                    ->with('message', 'Download is successfully updated.');
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Download is not updated.');
        }
    }

    /*
     * download delete action admin panel
     * @return JSON
     */
    public function deleteDownload(Request $request)
    {
        $downloadId                 = $request->get('id');
        $objectDownload             = Download::find($downloadId);
        $objectDownload->is_active  = '0';
        $objectDownload->is_deleted = '1';
        $objectDownload->save();

        if ($objectDownload->id) {
            $data['type']    = "success";
            $data['message'] = "Download deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * download staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $downloadId     = $request->get('id');
        $objectDownload = Download::find($downloadId);
        if ($objectDownload->is_active == '0') {
            $objectDownload->is_active = '1';
            $objectDownload->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The download has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectDownload->is_active = '0';
            $objectDownload->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The download has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

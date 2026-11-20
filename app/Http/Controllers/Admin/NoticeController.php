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
use App\Models\Notice;
use App\Models\Noticedetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class NoticeController extends ConfigurationController
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
     * get notice list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(4, Auth::User()->id);

        if ($viewAccess) {
            $notices = Notice::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderBy('created_at', 'desc')
                ->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(4, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(4, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(4, $authUser->id);

            return view('notice.admin.list')
                ->with('notices', $notices)
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
     * get notice add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddNotice()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('notice.admin.add')
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
     * post notice add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addNotice(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['notice_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf|max:10240';
            foreach ($languages as $language) {
                $rules['notice_heading_' . $language->id]     = 'required';
                $rules['notice_description_' . $language->id] = 'required';
            }

            $messages = [
                'attachment.max'   => 'Maximum attachment size is 10 MB.',
                'attachment.mimes' => 'Invalid file format. Please upload JPG, JPEG or PDF only.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                if ($validator->errors()->has('attachment')) {
                    return Redirect::back()->withInput()
                        ->with('attachment-error', $validator->errors()->first('attachment'));
                }

                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $noticeUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('notice_url')))), '-');
                $noticeExist = Notice::checkExist($noticeUrl, $authUser->office_id);
                if ($noticeExist) {
                    $message = 'Notice with url <b>' . $noticeUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/notice/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "notice" . "_" . time() . "_" . $authUser->office_id . "_" . Auth::user()->id;

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Invalid file format. Please upload JPG, JPEG or PDF only.');
                        } else if ($size > $maximum_filesize) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Maximum attachment size is 10 MB.');
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectNotice                          = new Notice();
                    $objectNotice->notice_url              = $noticeUrl;
                    $objectNotice->notice_meta_tags        = $request->get('notice_meta_tags');
                    $objectNotice->notice_meta_description = $request->get('notice_meta_description');
                    $objectNotice->published_date          = trim($request->get('published_date'));
                    $objectNotice->is_active               = $request->get('is_active');
                    $objectNotice->notice_attachment       = $logo;
                    $objectNotice->created_by              = $authUser->id;
                    $objectNotice->updated_by              = $authUser->id;
                    $objectNotice->office_id               = $authUser->office_id;
                    $objectNotice->download_count          = 0;
                    $objectNotice->save();

                    if ($objectNotice->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'notice_id'                => $objectNotice->id,
                                'language_id'              => $language->id,
                                'notice_heading'           => $request->get('notice_heading_' . $language->id),
                                'notice_description'       => $request->get('notice_description_' . $language->id),
                                'notice_page_title'        => $request->get('notice_page_title_' . $language->id),
                                'notice_short_description'  => '',
                                'created_at'               => date("Y-m-d H:i:s"),
                                'updated_at'               => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('notice_detail')->insert($insertArray);

                        app(\App\Services\NoticeSearchIndexer::class)->rebuildNotice((int) $objectNotice->id);

                        return Redirect::route('admin.notices')
                            ->with('type', 'success')
                            ->with('message', 'New notice is added successfully.');
                    }
                }
            }
        } catch (Throwable $e) {
            report($e);
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New notice is not added.');
        }
    }

    /**
     * get notice edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditNotice($id)
    {

        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(4, Auth::User()->id);
            $noticeData = Notice::find($id);

            if ($noticeData->office_id == $officeId && $editAccess) {
                $languages = Language::getActiveLanguages();
                $notices   = Notice::getNoticeDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('notice.admin.edit')
                    ->with('notices', $notices)
                    ->with('noticeData', $noticeData)
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
     * post notice edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editNotice(Request $request)
    {
        $noticeId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages  = Language::getActiveLanguages();
        $notices    = Notice::getNoticeDetailsWithLanguages($noticeId);
        $officeId   = Auth::user()->office_id;
        $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, Auth::User()->id);
        $noticeData = Notice::find($noticeId);

        try {
            $rules = ['notice_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf|max:10240';
            foreach ($languages as $language) {
                $rules['notice_heading_' . $language->id]     = 'required';
                $rules['notice_description_' . $language->id] = 'required';
            }
            $messages = [
                'attachment.max'   => 'Maximum attachment size is 10 MB.',
                'attachment.mimes' => 'Invalid file format. Please upload JPG, JPEG or PDF only.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                if ($validator->errors()->has('attachment')) {
                    return Redirect::back()->withInput()
                        ->with('attachment-error', $validator->errors()->first('attachment'));
                }

                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $noticeUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('notice_url')))), '-');
                $noticeExist = Notice::checkExist($noticeUrl, $officeId);

                if ($noticeExist && $noticeExist != $noticeId) {
                    $message = 'Notice with url <b>' . $noticeUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/notice/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "notice" . "_" . time() . "_" . $officeId . "_" . Auth::user()->id;

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Invalid file format. Please upload JPG, JPEG or PDF only.');
                        } else if ($size > $maximum_filesize) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Maximum attachment size is 10 MB.');
                        }
                    } else {
                        $attachment_deleted = $request->get('attachment_deleted');
                    }

                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectNotice                          = Notice::find($noticeId);
                    $objectNotice->notice_url              = $noticeUrl;
                    $objectNotice->notice_meta_tags        = $request->get('notice_meta_tags');
                    $objectNotice->notice_meta_description = $request->get('notice_meta_description');
                    $objectNotice->published_date          = trim($request->get('published_date'));
                    $objectNotice->is_active               = $request->get('is_active');
                    $oldAttachment = $objectNotice->notice_attachment;

                    if ($logo) {
                        $objectNotice->notice_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        $objectNotice->notice_attachment = null;
                    }

                    $objectNotice->updated_by = Auth::user()->id;
                    $objectNotice->save();

                    if (($logo || (isset($attachment_deleted) && $attachment_deleted == '1')) && $oldAttachment && $oldAttachment !== $objectNotice->notice_attachment) {
                        $oldAttachmentPath = 'uploads/notice/' . $oldAttachment;
                        if (is_file($oldAttachmentPath)) {
                            unlink($oldAttachmentPath);
                        }
                    }

                    if ($objectNotice->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId = $request->get('detail_id_' . $language->id);

                            $objectNoticeDetail = Noticedetail::find($detailId);
                            if ($objectNoticeDetail) {
                                $objectNoticeDetail->notice_heading           = trim($request->get('notice_heading_' . $language->id));
                                $objectNoticeDetail->notice_description       = trim($request->get('notice_description_' . $language->id));
                                $objectNoticeDetail->notice_page_title        = trim($request->get('notice_page_title_' . $language->id));
                                $objectNoticeDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'notice_id'                => $objectNotice->id,
                                    'language_id'              => $language->id,
                                    'notice_heading'           => trim($request->get('notice_heading_' . $language->id)),
                                    'notice_description'       => trim($request->get('notice_description_' . $language->id)),
                                    'notice_page_title'        => trim($request->get('notice_page_title_' . $language->id)),
                                    'created_at'               => date("Y-m-d H:i:s"),
                                    'updated_at'               => date("Y-m-d H:i:s"),
                                ];
                            }
                        }

                        if (count($insertArray) > 0) {
                            DB::table('notice_detail')->insert($insertArray);
                        }
                    }

                    app(\App\Services\NoticeSearchIndexer::class)->rebuildNotice((int) $objectNotice->id);

                    return Redirect::route('admin.notices')
                        ->with('type', 'success')
                        ->with('message', 'Notice is successfully updated.');
                }
            }
        } catch (Throwable $e) {
            report($e);
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Notice is not updated.');
        }
    }

    /*
     * notice delete action admin panel
     * @return JSON
     */
    public function deleteNotice(Request $request)
    {
        $noticeId                 = $request->get('id');
        $objectNotice             = Notice::find($noticeId);
        $objectNotice->is_active  = '0';
        $objectNotice->is_deleted = '1';
        $objectNotice->save();
        app(\App\Services\NoticeSearchIndexer::class)->rebuildNotice((int) $objectNotice->id);

        if ($objectNotice->id) {
            $data['type']    = "success";
            $data['message'] = "Notice deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * notice staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $noticeId     = $request->get('id');
        $objectNotice = Notice::find($noticeId);
        if ($objectNotice->is_active == '0') {
            $objectNotice->is_active = '1';
            $objectNotice->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The notice has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectNotice->is_active = '0';
            $objectNotice->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The notice has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        app(\App\Services\NoticeSearchIndexer::class)->rebuildNotice((int) $objectNotice->id);
        exit(json_encode($feedback));
    }
}

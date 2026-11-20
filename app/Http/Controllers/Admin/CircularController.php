<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Circular;
use App\Models\Circulardetail;
use App\Models\Configuration;
use App\Models\UserAccessControlModel;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CircularController extends ConfigurationController
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

    /**
     * Restrict Circular management actions to Super Admin.
     */
    private function requireSuperAdmin(): void
    {
        abort_unless(
            Auth::check() && (int) Auth::user()->user_type_id === 2,
            403,
            'You are not authorized to manage Circulars.'
        );
    }

    /*
     * get circular list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        // dd(9);
        $authUser = Auth::user();
        $user_type_id = $authUser->user_type_id;
        // dd($authUser);
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(28, $authUser->id);

        if ($viewAccess) {
            $query = Circular::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('is_deleted', '0');
            // ->where('office_id', $authUser->office_id)

            $circulars = $query->orderBy('published_date', 'desc')
                ->get();

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(28, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(28, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(28, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(28, $authUser->id);
            $downloadAccess  = UserAccessControlModel::getAccessDownloadByModuleAndUserMapId(28, $authUser->id);
            // dd($downloadAccess);

            return view('circular.admin.list')
                ->with('circulars', $circulars)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('viewAccess', $viewAccess)
                ->with('addAccess', $addAccess)
                ->with('editAccess', $editAccess)
                ->with('publishAccess', $publishAccess)
                ->with('deleteAccess', $deleteAccess)
                ->with('downloadAccess', $downloadAccess);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get circular add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddCircular()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(28, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('circular.admin.add')
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
     * post circular add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addCircular(Request $request)
    {
        $this->requireSuperAdmin();

        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['circular_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'nullable|file|mimes:png,jpg,jpeg,pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:102400';
            foreach ($languages as $language) {
                $rules['circular_heading_' . $language->id]     = 'required';
                $rules['circular_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $circularUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('circular_url')))), '-');
                $circularExist = Circular::checkExist($circularUrl, $authUser->office_id);
                if ($circularExist) {
                    $message = 'circular with url <b>' . $circularUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile = $request->file('attachment');
                    $destinationPath = public_path('uploads/circular');
                    $uploadedPath = null;
                    $logo = null;

                    if ($imageFile) {
                        $extension = strtolower($imageFile->getClientOriginalExtension());

                        if (!preg_match('/^(png|jpg|jpeg|pdf|doc|docx|ppt|pptx|xls|xlsx|zip|rar)$/', $extension)) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Warning: Invalid file type.');
                        }

                        if ($imageFile->getSize() > 100 * 1024 * 1024) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Warning: The attachment must not exceed 100 MB.');
                        }

                        if (!is_dir($destinationPath) || !is_writable($destinationPath)) {
                            throw new \RuntimeException('Circular upload directory is unavailable.');
                        }

                        $logo = 'circular_' . date('Ymd_His') . '_' . $authUser->id . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                        $attachment = $imageFile->move($destinationPath, $logo);
                        $uploadedPath = $attachment->getPathname();
                    }

                    try {
                        DB::transaction(function () use ($request, $languages, $authUser, $circularUrl, $logo) {
                            $objectCircular = new Circular();
                            $objectCircular->circular_url = $circularUrl;
                            $objectCircular->circular_meta_tags = $request->get('circular_meta_tags');
                            $objectCircular->circular_meta_description = $request->get('circular_meta_description');
                            $objectCircular->published_date = trim($request->get('published_date'));
                            $objectCircular->is_active = $request->get('is_active');
                            $objectCircular->circular_attachment = $logo;
                            $objectCircular->created_by = $authUser->id;
                            $objectCircular->updated_by = $authUser->id;
                            $objectCircular->office_id = $authUser->office_id;
                            $objectCircular->download_count = 0;
                            $objectCircular->save();

                            $insertArray = [];
                            foreach ($languages as $language) {
                                $insertArray[] = [
                                    'circular_id' => $objectCircular->id,
                                    'language_id' => $language->id,
                                    'circular_heading' => $request->get('circular_heading_' . $language->id),
                                    'circular_description' => $request->get('circular_description_' . $language->id),
                                    'circular_page_title' => $request->get('circular_page_title_' . $language->id),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }

                            if (!empty($insertArray)) {
                                DB::table('circular_detail')->insert($insertArray);
                            }
                        });

                        return Redirect::route('admin.circulars')
                            ->with('type', 'success')
                            ->with('message', 'New circular is added successfully.');
                    } catch (Throwable $e) {
                        if ($uploadedPath && is_file($uploadedPath)) {
                            @unlink($uploadedPath);
                        }

                        throw $e;
                    }
                }
            }
        } catch (Throwable $e) {
            report($e);
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New circular is not added.');
        }
    }

    /**
     * get circular edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditCircular($id)
    {
        try {
            $user         = Auth::user();
            $officeId     = $user->office_id;
            $isSuperAdmin = (int) $user->user_type_id === 2;
            $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(28, $user->id);
            $editAccess   = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(28, $user->id);
            $circularData = Circular::where('is_deleted', '0')->find($id);

            if ($circularData && $editAccess && ($isSuperAdmin || (int) $circularData->office_id === (int) $officeId)) {
                $languages = Language::getActiveLanguages();
                $circulars = Circular::getCircularDetailsWithLanguages($id);

                return view('circular.admin.edit')
                    ->with('circulars', $circulars)
                    ->with('circularData', $circularData)
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
     * post circular edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editCircular(Request $request, $id)
    {
        $this->requireSuperAdmin();

        abort_unless((string) $request->input('id') === (string) $id, 404);

        $circularId = $id;
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();

        try {
            $objectCircular = Circular::where('is_deleted', '0')->findOrFail($circularId);
            $officeId = $objectCircular->office_id;

            $rules = [
                'circular_url' => 'required',
                'published_date' => 'required',
            ];
            $rules['attachment'] = 'nullable|file|mimes:png,jpg,jpeg,pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:102400';
            foreach ($languages as $language) {
                $rules['circular_heading_' . $language->id]     = 'required';
                $rules['circular_description_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $circularUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('circular_url')))), '-');
                $circularExist = Circular::checkExist($circularUrl, $officeId);

                if ($circularExist != 0 && $circularExist != $circularId) {
                    $message = 'Circular with url <b>' . $circularUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    // Validate all submitted detail IDs before changing files or records.
                    $validatedDetails = [];

                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);

                        if (!empty($detailId)) {
                            $detail = Circulardetail::where('circular_id', $circularId)
                                ->where('language_id', $language->id)
                                ->where('id', $detailId)
                                ->first();

                            if (!$detail) {
                                return Redirect::back()->withInput()
                                    ->with('type', 'error')
                                    ->with('message', 'Invalid Circular language detail.');
                            }

                            $validatedDetails[$language->id] = $detail;
                        }
                    }

                    $newAttachmentPath = null;
                    $newAttachmentName = null;
                    $oldAttachmentName = $objectCircular->circular_attachment;

                    $imageFile = $request->file('attachment');
                    $maximumFilesize = 100 * 1024 * 1024;

                    if ($imageFile) {
                        $extension = strtolower($imageFile->getClientOriginalExtension());
                        $allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar'];

                        if (!in_array($extension, $allowedExtensions, true)) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Warning : Invalid File!');
                        } elseif ($imageFile->getSize() > $maximumFilesize) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Warning : The attachment is too large.');
                        } else {
                            $destinationPath = public_path('uploads/circular');

                            if (!is_dir($destinationPath)) {
                                throw new \RuntimeException('Circular upload directory is missing.');
                            }

                            $newAttachmentName = 'circular_' . time() . '_' .
                                Auth::user()->id . '_' . bin2hex(random_bytes(6)) . '.' . $extension;

                            $newAttachmentPath = $destinationPath . DIRECTORY_SEPARATOR . $newAttachmentName;
                            $imageFile->move($destinationPath, $newAttachmentName);
                        }
                    }

                    try {
                        DB::transaction(function () use (
                            $request,
                            $languages,
                            $objectCircular,
                            $circularUrl,
                            $newAttachmentName,
                            $validatedDetails,
                            &$oldAttachmentName
                        ) {
                            $objectCircular->circular_url = $circularUrl;
                            $objectCircular->circular_meta_tags = $request->get('circular_meta_tags');
                            $objectCircular->circular_meta_description = $request->get('circular_meta_description');
                            $objectCircular->published_date = trim($request->get('published_date'));
                            $objectCircular->is_active = $request->get('is_active');
                            $objectCircular->updated_by = Auth::user()->id;

                            if ($newAttachmentName) {
                                $objectCircular->circular_attachment = $newAttachmentName;
                            } elseif ($request->get('attachment_deleted') === '1') {
                                $objectCircular->circular_attachment = null;
                            }

                            $objectCircular->save();

                            $insertArray = [];

                            foreach ($languages as $language) {
                                $detail = $validatedDetails[$language->id] ?? null;

                                if ($detail) {
                                    $detail->circular_heading = trim($request->get('circular_heading_' . $language->id));
                                    $detail->circular_description = trim($request->get('circular_description_' . $language->id));
                                    $detail->circular_page_title = trim($request->get('circular_page_title_' . $language->id));
                                    $detail->save();
                                } else {
                                    $insertArray[] = [
                                        'circular_id' => $objectCircular->id,
                                        'language_id' => $language->id,
                                        'circular_heading' => trim($request->get('circular_heading_' . $language->id)),
                                        'circular_description' => trim($request->get('circular_description_' . $language->id)),
                                        'circular_page_title' => trim($request->get('circular_page_title_' . $language->id)),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];
                                }
                            }

                            if ($insertArray) {
                                DB::table('circular_detail')->insert($insertArray);
                            }
                        });

                        // Remove the previous file only after the database transaction commits.
                        if (
                            ($newAttachmentName || $request->get('attachment_deleted') === '1') &&
                            $oldAttachmentName &&
                            basename($oldAttachmentName) === $oldAttachmentName
                        ) {
                            $oldPath = public_path('uploads/circular/' . $oldAttachmentName);

                            if (is_file($oldPath)) {
                                @unlink($oldPath);
                            }
                        }

                    } catch (Throwable $e) {
                        report($e);

                        if ($newAttachmentPath && is_file($newAttachmentPath)) {
                            @unlink($newAttachmentPath);
                        }

                        return Redirect::back()->withInput()
                            ->with('type', 'error')
                            ->with('message', 'Circular is not updated.');
                    }

                    return Redirect::route('admin.circulars')
                        ->with('type', 'success')
                        ->with('message', 'Circular is updated successfully.');
                }
            }
        } catch (Throwable $e) {
            report($e);

            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Circular is not updated.');
        }
    }

    /*
     * circular delete action admin panel
     * @return JSON
     */
    public function deleteCircular(Request $request)
    {
        $this->requireSuperAdmin();

        $circularId = $request->get('id');
        $objectCircular = Circular::where('is_deleted', '0')->findOrFail($circularId);
        $objectCircular->is_active  = '0';
        $objectCircular->is_deleted = '1';
        $objectCircular->save();

        if ($objectCircular->id) {
            $data['type']    = "success";
            $data['message'] = "Circular deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * circular staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $this->requireSuperAdmin();

        $circularId = $request->get('id');
        $objectCircular = Circular::where('is_deleted', '0')->findOrFail($circularId);
        if ($objectCircular->is_active == '0') {
            $objectCircular->is_active = '1';
            $objectCircular->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The circular has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectCircular->is_active = '0';
            $objectCircular->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The circular has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }

    /*
     * get circular list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getNewsletterIndex()
    {
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(28, Auth::User()->id);

        if ($viewAccess) {
            $officeId   = Auth::user()->office_id;
            $newsletter = DB::table('newsletter')->orderBy('id', 'DESC')->get();
            //echo Configuration::get_last_query(); exit();

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, Auth::User()->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(4, Auth::User()->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(4, Auth::User()->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(4, Auth::User()->id);

            return view('circular.admin.letterlist')
                ->with('newsletters', $newsletter)
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
     * get circular add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getaddNewsletter()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(28, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('circular.admin.createnews')
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
     * post circular add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addNewsletter(Request $request)
    {
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $officeId = Auth::user()->office_id;

        try {
            $rules                           = ['email_list' => 'required'];
            $rules                           = ['published_date' => 'required'];
            $rules['newsletter_subject']     = 'required';
            $rules['newsletter_description'] = 'required';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                Session::flash('class', 'alert alert-error');
                Session::flash('message', 'Some fields with * are missing.');
                return view('circular::admin.createnews')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('messages', $messages);
            } else {

                $imageFiles = $request->file('attachments');
                foreach ($imageFiles as $imageFile) {
                    $destinationPath  = 'uploads/circular/';
                    $rEFileTypes      = "/^\.(png|jpg|jpeg|pdf|doc|docx|ppt|pptx|xls|xlsx|zip|rar)$/i";
                    $maximum_filesize = 100 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "circular" . "_" . time() . "_" . $officeId . "_" . Auth::user()->id;

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 100 MB!");
                        }
                    }
                    $logo[] = isset($attachment) ? $new_image_name . $extension : null;
                }

                $datas       = $request->get('email_list');
                $email_lists = array_filter($datas);
                // foreach($email_lists as $id => $data){
                $subject = $request->get('newsletter_subject');
                $home    = '<table cellspacing="0" cellpadding="0" width="100%">';
                $home .= '<tr><td><p>Dear <strong>Sir/Madam,</strong> <br />';
                $home .= '<br /> An Email send from DOR Central Office.</td>';
                $home .= '</table>';
                $home .= '<table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;margin-top: 20px;">';
                // $home    .= '<tr><td >Publish Date:</td><td width="150">' . $request->get('published_date')  .'</td></tr>';
                $home .= '<tr style="margin-top: 10px;"><td width="500">' . $request->get('newsletter_description') . '</td></tr>';
                $home .= '<tr style="margin-top: 10px;"><td width="500"><strong>Downloads</strong></td></tr>';
                foreach ($logo as $image) {
                    $extension = strrchr($image, '.');
                    if ($extension == 'jpg' || $extension == 'png') {
                        $home .= '<tr style="margin-top: 10px;"><td ><img src="' . $this->abs_site_url . 'uploads/circular/' . $image . '" alt="Image" /></td></tr>';
                    } else {
                        $home .= '<tr style="margin-top: 10px;"><td ><a href="' . $this->abs_site_url . 'uploads/circular/' . $image . '" target="_blank" download>Download</a></td></tr>';
                    }
                }

                $home .= '</table>';
                $home .= '<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">';
                $home .= '<tr><td><p>Kind Regards,</strong> <br />';
                $home .= $this->site_name . '</td>';
                $home .= '</table>';
                $toEmail = Configuration::getValueFromName('developer_email');
                // $toEmail = $data;

                $email = Helper::sendEmail($email_lists, $subject, $home);

                // }

                $insertArray[] = [
                    'emails'         => implode(',', $email_lists),
                    'subject'        => $request->get('newsletter_subject'),
                    'description'    => $request->get('newsletter_description'),
                    'attachment'     => implode(',', $logo),
                    'published_date' => $request->get('published_date'),
                    'created_by'     => Auth::user()->id,
                    'updated_by'     => Auth::user()->id,
                    'created_at'     => date("Y-m-d H:i:s"),
                    'updated_at'     => date("Y-m-d H:i:s"),
                ];
                DB::table('newsletter')->insert($insertArray);

                Session::flash('class', 'alert alert-success');
                Session::flash('message', 'Email Send successfully.');
                return view('circular::admin.createnews')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages);
            }
        } catch (Throwable $e) {
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view('circular::admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages);
        }
    }
    /**
     * get circular edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getviewNewsletter($id)
    {
        try {
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(28, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(28, Auth::User()->id);

            if ($editAccess) {
                $languages = Language::getActiveLanguages();

                $newsletters = DB::table('newsletter')->where('id', $id)->first();

                return view('circular.admin.viewnewsletter')
                    ->with('newsletters', $newsletters)
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
     * get circular view page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getViewCircular($id)
    {
        // dd($id);
        try {
            $viewAccess    = UserAccessControlModel::getAccessViewByModuleAndUserMapId(28, Auth::User()->id);
            $circularData = Circular::where('id', $id)
                ->where('circular.is_deleted', '0')->first();
            // dd($circularData);

            if ($viewAccess) {
                if (!$circularData) {
                    return view('error')
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name);
                }

                $languages = Language::getActiveLanguages();
                $circulars = Circular::getCircularDetailsWithLanguages($id);

                $downloadAccess = UserAccessControlModel::getAccessDownloadByModuleAndUserMapId(28, Auth::user()->id);

                return view('circular.admin.view')
                    ->with('circulars', $circulars)
                    ->with('circularData', $circularData)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('viewAccess', $viewAccess)
                    ->with('downloadAccess', $downloadAccess)
                    ->with('circularData', $circularData);
            } else {
                return view('error')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name);
            }
        } catch (Throwable $e) {
            report($e);
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }
}

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
use App\Models\UserAccessControlModel;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class NewsletterController extends ConfigurationController
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
    public function getNewsletterIndex()
    {
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(4, Auth::User()->id);

        if ($viewAccess) {
            $officeId   = Auth::user()->office_id;
            $newsletter = DB::table('newsletter')->orderBy('id', 'DESC')->get();
            //echo Configuration::get_last_query(); exit();

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, Auth::User()->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(4, Auth::User()->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(4, Auth::User()->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(4, Auth::User()->id);

            return view('notice.admin.letterlist')
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
     * get notice add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getaddNewsletter()
    {
        $authUser = Auth::user();
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, $authUser->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('notice.admin.createnews')
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
    public function addNewsletter(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules    = ['published_date' => 'required',
                        'newsletter_subject' => 'required',
                        'newsletter_description' => 'required'];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $imageFiles = $request->file('attachments');
                $attachments = $logo = [];

                foreach ($imageFiles as $index=>$imageFile) {
                    $destinationPath  = 'uploads/newsletters/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|png|pdf|ppt|pptx|doc|docx){1}$/i";
                    $maximum_filesize = 10 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "newsletter_" . $index . "_" . time() . "_" . $authUser->office_id . "_" . $authUser->id;

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                            $logo[] = isset($attachment) ? $new_image_name . $extension : null;
                            $attachments[] = public_path() .$destinationPath.$new_image_name . $extension;
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Attachment!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 10 MB!");
                        }
                    }

                }
                $email_lists = [];
                if($request->get('email_list')){
                    $email_lists = array_filter($request->get('email_list'));
                }
                if($request->has('more_emails')){
                    foreach ($request->get('more_emails') as $key => $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                            $email_lists[] = $email;
                        }
                    }
                }

                $subject = $request->get('newsletter_subject');
                $home    = '<table cellspacing="0" cellpadding="0" width="100%">';
                $home .= '<tr><td><p>Dear <strong>Sir/Madam,</strong> <br />';
                $home .= '<br /> An Email send from DOR Central Office.</td>';
                $home .= '</table>';
                $home .= '<table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;margin-top: 20px;">';
                // $home    .= '<tr><td >Publish Date:</td><td width="150">' . $request->get('published_date')  .'</td></tr>';
                $home .= '<tr style="margin-top: 10px;"><td width="500">' . $request->get('newsletter_description') . '</td></tr>';
                $home .= '</table>';
                $home .= '<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">';
                $home .= '<tr><td><p>Kind Regards,</strong> <br />';
                $home .= $this->site_name . '</td>';
                $home .= '</table>';

                $insertArray[] = [
                    'emails'         => implode(',', $email_lists),
                    'subject'        => $request->get('newsletter_subject'),
                    'description'    => $request->get('newsletter_description'),
                    'attachment'     => implode(',', $logo),
                    'published_date' => $request->get('published_date'),
                    'created_by'     => $authUser->user_id,
                    'updated_by'     => $authUser->user_id,
                    'created_at'     => date("Y-m-d H:i:s"),
                    'updated_at'     => date("Y-m-d H:i:s"),
                ];
                DB::table('newsletter')->insert($insertArray);
                // $email = Helper::sendEmail($email_lists, $subject, $home, $attachments);

                return Redirect::route('admin.newsletters')
                                    ->with('type','success')
                                    ->with('message','Email Send successfully.');

            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()
                            ->withInput()
                            ->with('message','Email Send successfully.');

        }

    }
    /**
     * get notice edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getviewNewsletter($id)
    {

        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(4, Auth::User()->id);

            if ($editAccess) {
                $languages = Language::getActiveLanguages();

                $newsletters = DB::table('newsletter')->where('id', $id)->first();

                return view('notice.admin.viewnewsletter')
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

}

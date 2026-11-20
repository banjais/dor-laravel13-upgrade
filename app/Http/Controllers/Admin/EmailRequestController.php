<?php

/**
 * Project Name: Department of Road(DOR)
 * @author Veshraj Joshi | Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Configuration;
use App\Models\EmailRequest;
use App\Models\Office;
use App\Models\StaffDesignation;
use App\Models\User;
use App\Models\UserAccessControlModel;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EmailRequestController extends ConfigurationController
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

    public function emailRequestForms()
    {
        $authUser   = User::find(Auth::User()->id);
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(26, $authUser->id);
        if ($viewAccess) {
            $emailrequests = EmailRequest::with('designation', 'office.detail')
                ->orderBy('created_at', 'desc')
                ->get();
            return view('staff.staffs.listforms')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('emailrequests', $emailrequests);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    public function getEmailRequestForm()
    {
        $authUser   = User::find(Auth::User()->id);
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(26, $authUser->id);
        if ($viewAccess) {
            $designations = StaffDesignation::all();
            return view('staff.staffs.emailrequest')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('designations', $designations);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    public function submitEmailRequestForm(Request $request)
    {
        $rules = [
            'name'           => 'required',
            'designation_id' => 'required | exists:staffdesignations,id',
            'username'       => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $authUser                     = User::find(Auth::User()->id);
        $emailRequest                 = new EmailRequest();
        $emailRequest->office_id      = $authUser->office_id;
        $emailRequest->name           = $request->get('name');
        $emailRequest->designation_id = $request->get('designation_id');
        $emailRequest->username       = $request->get('username');
        $emailRequest->email_address  = $request->get('email_address');
        $emailRequest->contact_number = $request->get('contact_number');
        $emailRequest->created_by     = $authUser->id;

        $htmlContent = '<html>
                       <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                          <title>
                             Email request form
                          </title>
                          <style type="text/css">
                             img {padding-left: 75px;}
                             #page_1 {position:relative; overflow: hidden;margin: 0px 0px 0px 0px;padding: 0px;border: none;}
                             #page_1 #id_1 {border:none;margin: 0px 0px 0px 70px;padding: 30px;border:none;overflow: hidden;}
                          </style>
                       </head>
                       <body>
                          <div id="page_1">
                             <div id="id_1">
                                <p>
                                   <img src="' . asset('assets/img/emailrequest_header.jpg') . '" width="400">
                                </p>
                                <p>Name : ' . ucwords($request->get('name')) . '</p>
                                <p>Designation : ' . StaffDesignation::find($request->get('designation_id'))->heading . '</p>
                                <p>Username : ' . $request->get('username') . '</p>
                                <p>(The user name should be abbreviated /short name to access your account)</p>
                                <p>First time passwogd : dort23</p>
                                <p>(password may be provided by lT center. You may change it anytime after the the account
                                   established. The password should be of six characters )
                                </p>
                                <p>Contact Telephone No. : ' . $request->get('contact_number') . '</p>
                                <p>Signature : </p>
                                <p>Date : ' . date("d M, Y") . ' </p>
                                <p>Note : You will be soon notified after opening of your email account.</p>
                                <p>For official use only</p>
                                <p>
                                <table  width="100%">
                                   <tr>
                                      <td width="50%">Email Address : ' . $request->get('email_address') . '</td>
                                   </tr>
                                   <tr>
                                      <td width="50%">Username : ' . $request->get('username') . '</td>
                                   </tr>
                                   <tr>
                                      <td width="50%">First Time Password : dort23</td>
                                   </tr>
                                </table>
                                </p>
                             </div>
                          </div>
                       </body>
                    </html>';

        $emailRequest->save();
        $pdf = PDF::loadHTML($htmlContent)
            ->save('uploads/emailrequests/emailrequestform' . $emailRequest->id . '.pdf');

        $emailRequest->attachment = 'emailrequestform' . $emailRequest->id . '.pdf';
        $emailRequest->save();

        if ($emailRequest) {
            $office = Office::with('detail')->find($authUser->office_id);

            $adminEmail = Configuration::getValueFromName('admin_email');
            $subject = 'Email request form submitted';
            $content = '<table cellspacing="0" cellpadding="0" width="100%">';
            $content .= '<tr><td><p>Dear <strong>admin,</strong> <br />';
            $content .= '<br />An office <strong>' . $office->detail[0]->office_name . '</strong> has requested to create an email with following details.</td>';
            $content .= '</table>';
            $content .= '<table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;margin-top: 20px;">';
            $content .= '<tr><td width="150">Name :</td><td>' . ucwords($request->get('name')) . '</td></tr>';
            $content .= '<tr><td width="150">Designation :</td><td>' . StaffDesignation::find($request->get('designation_id'))->heading . '</td></tr>';
            $content .= '<tr style="margin-top: 10px;"><td width="150">Email Address :</td><td>' . $request->get('email_address') . '</td></tr>';
            $content .= '<tr style="margin-top: 10px;"><td width="150">Contact Number:</td><td>' . $request->get('contact_number') . '</td></tr>';
            $content .= '<tr style="margin-top: 10px;"><td width="150">Prefered Username:</td><td>' . $request->get('username') . '</td></tr>';
            $content .= '</table>';
            $content .= '<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">';
            $content .= '<tr><td><p>Kind Regards,</strong> <br />';
            $content .= $this->site_name . '</td>';
            $content .= '</table>';

            $attachment = array('uploads/emailrequests/emailrequestform' . $emailRequest->id . '.pdf');
            $email = Helper::sendEmail($adminEmail, $subject, $content, $attachment, '', array(), $office->office_email_address, $office->detail[0]->office_name);
        }
        return Redirect::back()
            ->with('type', 'success')
            ->with('message', 'Your email request form is submitted successfully.');
    }

    public function changeStatus(Request $request)
    {
        $id    = $request->get('id');
        $staff = EmailRequest::find($id);
        if ($staff->is_created == '0') {
            $staff->is_created = '1';
            $staff->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The status has been changed successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
            exit(json_encode($feedback));
        }
        $staff->is_created = '0';
        $staff->save();
        $feedback['type']    = 'success';
        $feedback['message'] = 'The status has been changed successfully';
        $feedback['icon']    = '<i class="icon-check-empty"></i>';
        exit(json_encode($feedback));
    }

    /**
     * force download notice attachment
     *
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getForceDownload($id)
    {
        $emailRequest = EmailRequest::find($id);
        if ($emailRequest) {
            $file = public_path() . '/uploads/emailrequests/' . $emailRequest->attachment;
            $fileName = $emailRequest->attachment;
            $emailRequest->increment('download_count', 1);
            return Response::download($file, $fileName);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }
}

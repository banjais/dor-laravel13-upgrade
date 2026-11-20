<?php

/**
 * Project Name: Department of Road(DOR)
 * @author Veshraj Joshi | Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ConfigurationController;
use App\Models\Configuration;
use App\Models\Language;
use App\Models\Office;
use App\Models\Project;
use App\Models\User;
use App\Models\UserAccessControlModel;
use App\Models\UserTypeModel;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends ConfigurationController
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
     * This method is used to get the list of undeleted users(both active and deactive)
     *
     * @param array $array - no paramenter
     * @param string $blah - none
     * @return view(users::users..users)
     */
    public function getIndex()
    {
        $officeId = Auth::user()->office_id;
        $authUserId = Auth::user()->id;
        $users = User::with(['office.details' => function ($query) {
            $query->where('language_id', 2);
        }, 'usertype'])->get();

        $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(5, $authUserId);
        $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(5, $authUserId);
        $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(5, $authUserId);
        $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(5, $authUserId);

        return view('users.users.users')
            ->with('users', $users)
            ->with('addAccess', $addAccess)
            ->with('editAccess', $editAccess)
            ->with('publishAccess', $publishAccess)
            ->with('deleteAccess', $deleteAccess)
            ->with('site_name', $this->site_name)
            ->with('abs_site_url', $this->abs_site_url);
    }
    /**
     * This method is used to get the view part of the add user
     *
     * @param array $array - no paramenter
     * @param string
     * @return view(users::users..adduser)
     */
    public function getAddUser()
    {
        $officeId = Auth::user()->office_id;

        return view('users.users.adduser')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('usertypelist', $this->getUserTypeList())
            ->with('officelist', $this->getOfficeList())
            ->with('projectlist', $this->getProjectList())
            ->with('officeId', $officeId)
            ->with('languages', Language::getActiveLanguages());
    }

    /**
     * This method is used add  the user
     *
     * @param array $array - no paramenter
     * @param string  - none
     * @return json_message_string_array
     */
    public function addUser(Request $request)
    {
        $rules = [
            'usertype' => 'required',
            'username' => 'required|email|unique:users,username',
            'email_address' => 'required|email|max:254|unique:users,email_address',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'office_id' => 'required_unless:usertype,3',
            'project_id' => 'required_if:usertype,3',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $user = new User();
        $user->username = trim($request->get('username'));
        $user->email_address = mb_strtolower(trim($request->get('email_address')));
        $user->password = Hash::make($request->get('password'));
        $user->office_id = $request->get('office_id');
        $user->project_id = $request->get('project_id');
        $user->user_type_id = $request->get('usertype');
        $user->is_active = $request->get('is_active');
        $user->created_by = Auth::user()->id;
        $user->save();

        return Redirect::route('admin.users')
            ->with('message', "User added successfully.");
    }
    /**
     * This method is used add  the user
     *
     * @param int - $id
     * @return Illuminate\View\View
     */
    public function getEditUser($id)
    {
        $user       = User::find($id);
        $authUserId = Auth::user()->id;
        $offices    = Office::getPublishedOffices();
        $projects   = Project::getPublishedProjects();
        $usertypes  = UserTypeModel::getPublishedUserTypes();
        $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(5, $authUserId);

        return view('users.users.edituser')
            ->with('user', $user)
            ->with('usertypes', $usertypes)
            ->with('offices', $offices)
            ->with('projects', $projects)
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('addAccess', $addAccess);
    }
    /**
     * This method is used edit  the user
     *
     * @param int $id
     * @return json_message_string_array
     */
    public function editUser($id, Request $request)
    {

        $rules = [
            'office_id' => 'required_unless:usertype,3',
            'project_id' => 'required_if:usertype,3',
            'username' => 'required|email|unique:users,username,' . $id,
            'email_address' => 'required|email|max:254|unique:users,email_address,' . $id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $user = User::find($id);
        $user->username = trim($request->get('username'));
        $user->email_address = mb_strtolower(trim($request->get('email_address')));
        $user->office_id = $request->get('office_id');
        $user->project_id = $request->get('project_id');
        $user->user_type_id = $request->get('usertype');
        $user->is_active = $request->get('is_active');
        $user->updated_by = Auth::user()->id;
        $user->save();

        return Redirect::route('admin.users')
            ->with('message', "User is updated successfully.");
    }

    /**
     * This method is used delete  the user
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function deleteUser(Request $request)
    {
        $id               = $request->get('id');
        $user             = User::find($id);
        $user->is_active  = '0';
        $user->delete();
        $data['type']    = "success";
        $data['message'] = "Deleted Successfully";
        echo json_encode($data);
    }

    /**
     * This method is used add  the user
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function changeStatus(Request $request)
    {
        $id   = $request->get('id');
        $user = User::find($id);
        if ($user->is_active == '0') {
            $user->is_active = '1';
            $user->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The user has been activate successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
            exit(json_encode($feedback));
        }
        $user->is_active = '0';
        $user->save();
        $feedback['type']    = 'success';
        $feedback['message'] = 'The user has been deactivate successfully';
        $feedback['icon']    = '<i class="icon-check-empty"></i>';
        exit(json_encode($feedback));
    }

    /**
     * This method is used get user details dynamically to the front end
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function getUserDetails(Request $request)
    {
        $id      = $request->get('id');
        $user    = User::find($id);
        $details = User::getUserDetailsByIdForUpdate($id);

        $feedback['type'] = 'success';
        $feedback['id']   = $id;

        $feedback['email']      = $user->user_email_address;
        $feedback['status']     = $user->is_active;
        $feedback['attachment'] = (is_null($user->user_attachment)) ? $this->rel_site_url . "assets/admin/img/no-image.png" : $this->rel_site_url . "uploads/users/" . $user->user_attachment;
        $feedback['gender']     = $user->user_gender;
        foreach ($details as $detail) {
            $feedback['full_name_' . $detail->language_code]         = $detail->user_full_name;
            $feedback['permanent_address_' . $detail->language_code] = $detail->user_permanent_address;
            $feedback['temporary_address_' . $detail->language_code] = $detail->user_temporary_address;
            $feedback['staff_id_' . $detail->language_code]          = $detail->user_staff_id;
            $feedback['mobile_number_' . $detail->language_code]     = $detail->user_mobile_number;
            $feedback['contact_number_' . $detail->language_code]    = $detail->user_contact_number;
            $feedback['join_date' . $detail->language_code]          = $detail->user_office_join_date;
            $feedback['id_' . $detail->language_code]                = $detail->detail_id;
        }
        exit(json_encode($feedback));
    }
    /**
     * This method is used get user type list
     *
     *
     * @param array $array - no paramenter
     *
     * @param string  - none
     *
     * @return string

     */
    public function getUserTypeList()
    {
        $typelist = '';
        $types    = UserTypeModel::getPublishedUserTypes();
        foreach ($types as $type) {
            $typelist .= '<option value="' . $type->id . '">' . $type->user_type_name . '</option>';
        }
        return $typelist;
    }

    /**
     * This method is used get office name list
     *
     * @param string  - none
     * @return stirng

     */
    public function getOfficeList()
    {
        $officeId   = Auth::user()->office_id;
        $officelist = '';
        $offices    = Office::getPublishedOffices();
        foreach ($offices as $office) {
            $selected = ($officeId == $office->id) ? 'selected' : '';
            $officelist .= '<option value="' . e($office->id) . '" data-primary-email="' . e($office->office_email_address ?? '') . '" data-alternative-email="' . e($office->office_alternative_email ?? '') . '" ' . $selected . '>' . e($office->office_name) . ' ( ' . e($office->office_address) . ' )</option>';
        }
        return $officelist;
    }

    /**
     * This method is used get project lists
     *
     * @param string  - none
     * @return stirng

     */
    public function getProjectList()
    {
        $projectId = 0;
        $projectlist = '';
        $projects    = Project::getPublishedProjects();
        foreach ($projects as $project) {
            $selected = ($projectId == $project->id) ? 'selected' : '';
            $projectlist .= '<option value="' . $project->id . '" ' . $selected . '>' . $project->project_heading . '</option>';
        }
        return $projectlist;
    }
    /**
     * This method is used get the get change passowrd form of ownself
     *
     *
     * @param array $array - no paramenter
     *
     * @param string  - none
     *
     * @return View

     */
    public function getChangePassword()
    {
        return view('users.users.changepassword')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('user', Auth::User());
    }

    /**
     * This method is used get the  change passowrd of the user
     *
     * @param int $id
     * @return Redirect
     */
    public function changeOwnPassword(Request $request)
    {
        if ((int) Auth::user()->user_type_id === 13) {
            $rules = [
                'new_password' => [
                    'required',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[^A-Za-z0-9]/',
                ],
                'confirm_password' => 'required|same:new_password',
            ];
        } else {
            $rules = [
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password',
            ];
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return Redirect::back()->withErrors($validator);

        $user = User::find(Auth::user()->id);
        $user->password            = Hash::make($request->get('new_password'));
        $user->password_changed_on = date('Y-m-d H:i:s');
        $user->save();

        return Redirect::back()
            ->with('type', 'success')
            ->with('message', 'Password has been changed successfully.');
    }
    /**
     * This method is used get the get change passowrd form of others by admin
     *
     * @param int $userid
     * @return View chagne passwor form

     */
    public function getChangePasswordByAdmin($userid)
    {
        $user = User::findOrFail($userid);
        return view('users.users.changepassword')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('user', $user);
    }

    /**
     * This method is used get the  change passowrd of the user
     *
     * @param int $id
     * @return Redirect
     */
    public function changePassword($id, Request $request)
    {
        $rules = [
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return Redirect::back()->withErrors($validator);

        $id   = $request->get('id');
        $user = User::find($id);
        $user->password            = Hash::make($request->get('new_password'));
        $user->password_changed_on = date('Y-m-d H:i:s');
        $user->save();

        return Redirect::route('admin.users')
            ->with('message', 'Password has been changed successfully.');
    }

    public function getsortUser()
    {
        $users = User::getUndeletedSortUsers($this->officeId);
        //var_dump($sort);die();

        return view('users.users.sort_users')->with('abs_site_url', $this->abs_site_url)
            ->with('users', $users)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('languages', Language::getActiveLanguages());
    }

    public function sortUser(Request $request)
    {
        $returnArray = array('type' => 'error', 'message' => 'Server Error!');
        $users       = $request->get('user');

        $position = 1;
        foreach ($users as $userId) {
            $objectUser                 = User::find($userId);
            $objectUser->order_position = $position;
            $objectUser->save();
            $position++;
        }
        $returnArray = array('type' => 'success', 'users' => $users, 'message' => 'Successfully Users are sorted.');
        return json_encode($returnArray);
    }

    public function getAddEmail()
    {
        $user = User::find(Auth::User()->id);

        $useremail = isset($user->user_email_address) ? $user->user_email_address : '';

        return view('users.users.emailrequest')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('useremail', $useremail)
            ->with('officelist', $this->getOfficeList())
            ->with('languages', Language::getActiveLanguages());
    }

    /**
     * This method is used add  the user
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function addEmail(Request $request)
    {
        $change_email = $request->get('email_new');
        $user         = User::find(Auth::User()->id);
        $details      = User::getUserDetailsByIdForUpdate(Auth::User()->id);
        $office       = Office::getPublishedOfficesbyID($user->office_id);

        $useremail  = isset($user->user_email_address) ? $user->user_email_address : '';
        $useroffice = isset($office->office_name) ? $office->office_name : '';
        $username   = isset($details[0]->user_full_name) ? $details[0]->user_full_name : '';
        if ($change_email) {

            $subject = "Email Need To Change.";

            $home = '<table cellspacing="0" cellpadding="0" width="100%">';
            $home .= '<tr><td><p>Dear <strong>admin,</strong> <br />';
            $home .= '<br />An Admin <strong>' . $username . '</strong> has send email to change new email.</td>';
            $home .= '</table>';
            $home .= '<table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;margin-top: 20px;">';
            $home .= '<tr><td width="150">Current Email :</td><td>' . $useremail . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">New Email To change :</td><td>' . $change_email . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Office:</td><td>' . $useroffice . '</td></tr>';
            $home .= '</table>';
            $home .= '<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">';
            $home .= '<tr><td><p>Kind Regards,</strong> <br />';
            $home .= $this->site_name . '</td>';
            $home .= '</table>';

            $toEmail = Configuration::getValueFromName('developer_email');
            //$toEmail = $officeData->office_email_address;

            $email = Helper::sendEmail($toEmail, $subject, $home);

            $response['type']    = 'success';
            $response['message'] = "User has been created successfully.";
            exit(json_encode($response));
        }
    }
}

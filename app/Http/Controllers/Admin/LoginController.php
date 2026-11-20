<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Veshraj Joshi
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ConfigurationController;
use App\Models\Circular;
use App\Models\Content;
use App\Models\IpMapping;
use App\Models\Notice;
use App\Models\Project;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends ConfigurationController
{
    public $rel_site_url;
    public $abs_site_url;
    public $site_name;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name    = ConfigurationController::site_name();
    }

    public function getLogin()
    {
        $log_id = Session::get('log_id');
        if ($log_id) {
            return Redirect::to('/admin/home');
        }
        return view('users.login')->with('rel_site_url', $this->rel_site_url)
            ->with('abs_site_url', $this->abs_site_url)
            ->with('site_name', $this->site_name)
            ->with('error_message', '');
    }

    public function postLogin(Request $request)
    {
        $rules = array(
            'username' => 'Required',
            'password' => 'Required');

        $v = Validator::make($request->all(), $rules);
        if (!$v->fails()) {
            $auth = Auth::attempt(array(
                'username'   => $request->get('username'),
                'password'   => $request->get('password'),
                'is_active'  => '1'));
            // print_r($request->all());

            if ($auth) {
                $authUser = Auth::User();

                $data = array(
                    'user_id'      => $authUser->id,
                    'user_type_id' => $authUser->user_type_id,
                    'office_id'    => $authUser->office_id,
                    'username'     => $authUser->username,
                    'ip_address'   => $_SERVER['REMOTE_ADDR'],
                    'login_ts'     => date('Y-m-d H:i:s'),
                    'agent'        => $_SERVER['HTTP_USER_AGENT'],
                );
                $user       = IpMapping::create($data);
                $insertedId = $user->id;
                Session::put('log_id', $insertedId);

                if($authUser->user_type_id == 3){
                    return Redirect::route('project.admin.dashboard');
                } else{
                   return Redirect::route('dashboard');
                }
            } else {

                return view('users.login')->with('error_message', 'Invalid username or password')
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('site_name', $this->site_name);
            }
        } else {
            return view('users.login')->with('error_message', 'Either username or password is empty.')
                ->with('rel_site_url', $this->rel_site_url)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('site_name', $this->site_name);
        }
    }
    public function logoutUser()
    {
        $log_id = Session::get('log_id');

        if ($log_id) {
            $user = IpMapping::find($log_id);

            if ($user) {
                $user->logout_ts = date('Y-m-d H:i:s');
                $user->save();
            }
        }

        Auth::logout();
        Session::flush();

        return Redirect::route('admin.login');
    }
//==================================================================================================================================
    public function getChangePasswordOwn()
    {
        return view('users.changepassword')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);

    }
//==================================================================================================================================
    public function changePasswordOwn(Request $request)
    {
        $rule = array('newpassword' => 'required|min:6',
            'conpassword'               => 'required|same:newpassword');
        $v = Validator::make($request->all(), $rule);
        if ($v->fails()) {
            $feedback['type']    = 'validationerror';
            $feedback['message'] = $v->getMessageBag();
            exit(json_encode($feedback));
        }
        $user           = User::find(Auth::User()->id);
        $user->password = Hash::make($request->get('newpassword'));
        $user->save();
        $feedback['type']    = 'success';
        $feedback['message'] = 'Your password has been changed successfully';
        exit(json_encode($feedback));

    }
//==================================================================================================================================
    public function getChangePasswordByAdmin()
    {
        return view('users.changepasswordadmin')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }
//==================================================================================================================================
    public function changePasswordByAdmin(Request $request)
    {
        $rule = array('username' => 'required',
            'newpassword'            => 'required|min:6',
            'conpassword'            => 'required');
        $v = Validator::make($request->all(), $rule);
        if ($v->fails()) {
            $feedback['type']    = 'validationerror';
            $feedback['message'] = $v->getMessageBag();
            exit(json_encode($feedback));
        }
        $rec = DB::table('users')->where('username', $request->get('username'))->update(array('password' => Hash::make($request->get('newpassword'))));
        if ($rec) {
            $feedback['type']    = 'success';
            $feedback['message'] = 'Update Successfully.';
            exit(json_encode($feedback));
        }
        $feedback['type']    = 'error';
        $feedback['message'] = 'Such username doesnot exist in the database.';
        exit(json_encode($feedback));

    }

    public function getHome()
    {
        $user     = Auth::User();
        if($user->user_type_id == 3){
            $projectId = $user->project_id;
            $project = Project::with('detailEng')
                        ->find($projectId);

            return view('project_home')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('project', $project);
        }
        $officeId = $user->office_id;

        $staff_count = Staff::where('office_id', $officeId)
            ->where('is_deleted', '0')->count();
        $project_count = Project::where('office_id', $officeId)
            ->where('is_deleted', '0')->count();
        $content_count = Content::where('office_id', $officeId)
            ->where('is_deleted', '0')->count();
        $notice_count = Notice::where('office_id', $officeId)
            ->where('is_deleted', '0')->count();
        $circular_count = Circular::where(['is_deleted'=> '0','is_active'=>'1'])->count();

        return view('home')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('staff_count', $staff_count)
            ->with('project_count', $project_count)
            ->with('content_count', $content_count)
            ->with('notice_count', $notice_count)
            ->with('circular_count', $circular_count)
            ->with('site_name', $this->site_name);
    }
}

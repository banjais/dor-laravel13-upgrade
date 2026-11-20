<?php

/**
 * Project Name: Department of Road(DOR)
 * @author Veshraj Joshi | Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin\Project;
use App\Http\Controllers\ConfigurationController;
use App\Models\User;
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
 * This method is used get the get change passowrd form of ownself
 *
 * @return Illuminate\View\View
 */
    public function changePassword()
    {
        return view('users.project.change_password')->with('abs_site_url', $this->abs_site_url)
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
    public function updatePassword(Request $request)
    {
        $rules = ['new_password'=>'required',
                'confirm_password'=>'required|same:new_password'];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
            return Redirect::back()->withErrors($validator);

        $user = User::find(Auth::user()->id);
        $user->password            = Hash::make($request->get('new_password'));
        $user->password_changed_on = date('Y-m-d H:i:s');
        $user->save();

        return Redirect::back()
                ->with('type', 'success')
                ->with('message', 'Password has been changed successfully.');
    }
}

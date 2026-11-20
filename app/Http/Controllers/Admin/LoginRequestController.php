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
use App\Models\LoginRequest;
use App\Models\Office;
use App\Models\StaffDesignation;
use App\Models\User;
use App\Models\UserAccessControlModel;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LoginRequestController extends ConfigurationController
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

    public function getAll()
    {
        $authUser   = User::find(Auth::User()->id);
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(26, $authUser->id);
        if ($viewAccess) {
            $loginrequests = LoginRequest::orderBy('created_at', 'desc')
                ->get();
            return view('loginrequest.admin.listforms')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('loginrequests', $loginrequests);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    public function changeStatus(Request $request)
    {
        $id    = $request->get('id');
        $item = LoginRequest::find($id);

        // dd($item);

        $item->is_complete = $item->is_complete ? 0 : 1;
        $item->save();

        $feedback['type']    = 'success';
        $feedback['message'] = 'The status has been changed successfully';

        if ($item->is_complete)
            $feedback['icon']    = '<i class="icon-check"></i>';
        else
            $feedback['icon']    = '<i class="icon-check-empty"></i>';

        exit(json_encode($feedback));
    }
}

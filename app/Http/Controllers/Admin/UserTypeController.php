<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Veshraj Joshi
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ConfigurationController;
use App\Models\AccessControlModel;
use App\Models\ModuleModel;
use App\Models\UserTypeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserTypeController extends ConfigurationController
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

     * This method is used to get the list of undeleted user types (both active and deactive)
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no parameter

     * @param string  - none

     * @return view(users::.users)

     */
    public function getIndex()
    {

        $usertypes    = UserTypeModel::getUndeletedUserTypes();
        $usertypelist = '';
        $sn           = 1;
        foreach ($usertypes as $type) {
            $publishlink = '';
            $pulishicon  = ($type->is_active == 1) ? '<i class="icon-check"></i>' : '<i class="icon-check-empty"></i>';
            $editlink    = '';
            $deletelink  = '';
            if ($type->id > 2) {
                $editlink    = '<a href="#myModal1" class="edit-user-type" id="' . $type->id . '" role="modal" data-toggle="modal"><i class="icon-edit"></i> Edit</a>';
                $deletelink  = '<a href="javascript:;" class="delete-user-type" id="' . $type->id . '"><i class="icon-trash"></i> Delete</a>';
                $publishlink = '<a href="javascript:;" class="change-status" style="font-size:1.3em" id="' . $type->id . '">' . $pulishicon . '</a>';
            }
            $usertypelist .= '<tr>
									<td>' . $sn . '</td>
									<td>' . $type->user_type_name . '</td>
									<td style="text-align:center;">' . $publishlink . '</td>
									<td>' . $editlink . ' ' . $deletelink . ' </td>
								</tr>';
            $sn++;
        }
        return view('users.usertype.usertypes')->with('usertypes', $usertypelist)
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }

    /**

     * This method is used to get the view part of the add user

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string

     * @return view(users::.adduser)

     */
    public function getAddUserType()
    {
        return view('users.usertype.addusertype')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }

    /**
     * This method is used add  the user  type
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function addUserType(Request $request)
    {
        $rule = array(
            'user_type' => 'required',
        );
        $v = Validator::make($request->all(), $rule);
        if ($v->fails()) {
            $feedback['type']    = 'validationerror';
            $feedback['message'] = $v->getMessageBag();
            exit(json_encode($feedback));
        }
        $data = array(
            'user_type_name' => $request->get('user_type'),
            'created_by'                   => Auth::User()->id,
            'updated_by'                   => Auth::User()->id,
            'is_active'                    => $request->get('status'),
            'is_deleted'                   => '0',
        );

        $usertype = UserTypeModel::create($data);
        $modules  = ModuleModel::getUndeletedModules();

        if ($usertype) {
            foreach ($modules as $module) {
                $data = array(
                    'user_type_id' => $usertype->id,
                    'module_id'                  => $module->id,
                    'access_add'                 => '0',
                    'access_delete'              => '0',
                    'access_update'              => '0',
                    'access_publish'             => '0',
                    'access_view'                => '0',
                    'access_download'            => '0',
                    'access_upload'              => '0'
                );
                AccessControlModel::create($data);
            }
            $response['type']    = 'success';
            $response['message'] = 'User Type has been added successfully!!';
            exit(json_encode($response));
        }
        $info['type']    = 'error';
        $info['message'] = 'Could not be added due to some reason';
        echo json_encode($info);
    }

    /**
     * This method is used edit  the user
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function editUserType(Request $request)
    {
        $id   = $request->get('id');
        $rule = array(
            'user_type' => 'required',

        );
        $v = Validator::make($request->all(), $rule);
        if ($v->fails()) {
            $feedback['type']    = 'validationerror';
            $feedback['message'] = $v->getMessageBag();
            echo json_encode($feedback);
        } else {
            $usertype = UserTypeModel::find($id);

            if (!UserTypeModel::isUserTypeUniqueForUpdate($request->get('user_type'), $id)) {
                $feedback['type']    = 'error';
                $feedback['message'] = 'This user type is already in use. Try with another value';
                exit(json_encode($feedback));
            }
            $usertype->user_type_name = trim($request->get('user_type'));

            $rec = $usertype->save();
            if (!empty($rec)) {
                $feedback['type']         = 'success';
                $feedback['message']      = 'User Type has been Updated Successfully!!';
                $feedback['usertypelist'] = $this->getUserTypeList();
                exit(json_encode($feedback));
            } else {
                $feedback['type']    = 'error';
                $feedback['message'] = 'Could not be upadated due to some reason.';
                exit(json_encode($feedback));
            }
        }
    }
    /**
     * This method is used delete  the user
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function deleteUserType(Request $request)
    {
        $id                   = $request->get('id');
        $usertype             = UserTypeModel::find($id);
        $usertype->is_active  = '0';
        $usertype->is_deleted = '1';
        $usertype->save();
        $data['type']    = "success";
        $data['message'] = "User Type Deleted Successfully";
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
        $user = UserTypeModel::find($id);
        if ($user->is_active == '0') {
            $user->is_active = '1';
            $user->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The user type has been activate successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
            exit(json_encode($feedback));
        }
        $user->is_active = '0';
        $user->save();
        $feedback['type']    = 'success';
        $feedback['message'] = 'The user type has been deactivate successfully';
        $feedback['icon']    = '<i class="icon-check-empty"></i>';
        exit(json_encode($feedback));
    }

    /**
     * This method is used get user type details dynamically to the front end
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function getUserTypeDynamically(Request $request)
    {
        $id                   = $request->get('id');
        $usertype             = UserTypeModel::find($id);
        $response['type']     = 'success';
        $response['usertype'] = $usertype->user_type_name;
        exit(json_encode($response));
    }

    /**
     * This method is used get list of undeleted user type
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function getUserTypeList()
    {
        $types       = UserTypeModel::getUndeletedUserTypes();
        $userstypes  = '';
        $sn          = 1;
        $editlink    = '';
        $deletelink  = '';
        $publishlink = '';
        foreach ($types as $type) {
            $publish = ($type->is_active == 1) ? '<i class="icon-check"></i>' : '<i class=" icon-check-empty"></i>';

            if ($type->id > 2) {
                $editlink    = '<a href="#myModal1" id="' . $type->id . '" class="edit-user-type" data-toggle="modal"><i class="icon-edit"></i>Edit</a>';
                $deletelink  = '<a href="javascript:;" class="delete-user-type" id="' . $type->id . '"><i class="icon-trash"></i> Delete</a>';
                $publishlink = '<a href="javascript:;" style="font-size:1.4em;"  class="change-status" id="' . $type->id . '"><span >' . $publish . '</span></a>';
            }

            $userstypes .= '<tr class="">
								<td>' . $sn . '</td>
								<td>' . $type->user_type_name . '</td>
								<td style="text-align:center">' . $publishlink . '</td>
								<td> ' . $editlink . ' ' . $deletelink . ' </td>

							</tr>';
            $sn++;
        }
        return $userstypes;
    }
}

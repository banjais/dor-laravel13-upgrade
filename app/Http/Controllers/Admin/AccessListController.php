<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Veshraj Joshi
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use App\Models\UserAccessControlModel;
use App\Http\Controllers\ConfigurationController;
use App\Models\AccessControlModel;
use App\Models\ModuleModel;
use App\Models\UserMap;
use Illuminate\Http\Request;
use App\Models\UserTypeModel;

class AccessListController extends ConfigurationController
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

     * This method is used to get the page of access control list (Multi Level Access)

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */

    public function getIndex()
    {
        /*if(Session::get('12_view')!=1){
        return view('500')->with('abs_site_url',$this->abs_site_url)
        ->with('rel_site_url',$this->rel_site_url);

        }*/
        $accesslist = $this->getAccessList();
        //$accesslist= '';
        return view('users.access.accesslist')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('accesslist', $accesslist);
    }

    /**

     * This method is used to get the page of access control list for particular user (Multi Level Access)

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */

    public function getIndexUser()
    {
        /*if(Session::get('12_view')!=1){
        return view('500')->with('abs_site_url',$this->abs_site_url)
        ->with('rel_site_url',$this->rel_site_url);

        }*/
        $accesslist = $this->getAccessListUser();
        //$accesslist= '';
        return view('users.access.useraccesslist')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('accesslist', $accesslist);
    }

    /**

     * This method is used to add new user type for control list (Multi Level Access)

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function getcontrollist($usertypeid)
    {

        $accessarray = AccessControlModel::getAccessListByUserTypeId($usertypeid);

        $accesslist = '<div class="table-responsive"><table  class="table table-striped table-bordered">
							<thead>
								<tr>
									<th rowspan="2" style="text-align:middle;"> Module Name</th>
									<th colspan="5" style="text-align:center">Action Privilages</tr>
								</tr>
								<tr>
									<th style="text-align:center;">View</th>
									<th style="text-align:center;">Add</th>
									<th style="text-align:center;">Delete</th>
									<th style="text-align:center;">Update</th>
									<th style="text-align:center;">Publish</th>
									<th style="text-align:center;">Upload</th>
									<th style="text-align:center;">Download</th>

								</tr>
							</thead>
							<tbody></div>';
        if (count($accessarray)) {
            foreach ($accessarray as $access) {
                $add      = ($access->access_add == 0) ? "<a id='access_add-" . $access->id . "' id='access_add-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_add-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $delete   = ($access->access_delete == 0) ? "<a id='access_delete-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_delete-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $update   = ($access->access_update == 0) ? "<a id='access_update-" . $access->id . "' class='check'><span><i class='icon-check-empty'></i></span></a>" : "<a id='access_update-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $publish  = ($access->access_publish == 0) ? "<a id='access_publish-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_publish-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $view     = ($access->access_view == 0) ? "<a id='access_view-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_view-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $download = ($access->access_download == 0) ? "<a id='access_download-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_download-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $upload   = ($access->access_upload == 0) ? "<a id='access_upload-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_upload-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $accesslist .= '
								<tr>
									<td>' . $access->module_name . '</td>
									<td style="text-align:center;">' . $view . '</td>
									<td style="text-align:center;">' . $add . '</td>
									<td style="text-align:center;">' . $delete . '</td>
									<td style="text-align:center;">' . $update . '</td>
									<td style="text-align:center;">' . $publish . '</td>
									<td style="text-align:center;">' . $upload . '</td>
									<td style="text-align:center;">' . $download . '</td>

								</tr>

							';
            }
            $accesslist .= '</tbody>
							</table>
						';
            return $accesslist;
        }
    }
    /**

     * This method is used to add new user type for control list (Multi Level Access)

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function getcontrollistUser($usermapid)
    {

        $usertypeid = UserMap::find($usermapid)->user_type_id;

        $groupaccessarray = AccessControlModel::getAccessListByUserTypeId($usertypeid);

        foreach ($groupaccessarray as $access) {
            if (UserAccessControlModel::checkAccessListItemByModuleAndUserMapId($access->module_id, $usermapid)) {
                $data = array(
                    'module_id' => $access->module_id,
                    'user_map_id'             => $usermapid,
                    'access_add'              => $access->access_add,
                    'access_delete'           => $access->access_delete,
                    'access_update'           => $access->access_update,
                    'access_publish'          => $access->access_publish,
                    'access_view'             => $access->access_view,
                    'access_download'         => $access->access_download,
                    'access_upload'           => $access->access_upload,
                );
                UserAccessControlModel::create($data);
            }
        }

        $accessarray = UserAccessControlModel::getAccessListByUserMapId($usermapid);

        $accesslist = '<div class="table-responsive">
        <table  class="table table-striped table-bordered">
							<thead>
								<tr>
									<th rowspan="2" style="text-align:middle;"> Module Name</th>
									<th colspan="5" style="text-align:center">Action Privilages</tr>
								</tr>
								<tr>
									<th style="text-align:center;">View</th>
									<th style="text-align:center;">Add</th>
									<th style="text-align:center;">Delete</th>
									<th style="text-align:center;">Update</th>
									<th style="text-align:center;">Publish</th>
									<th style="text-align:center;">Download</th>
									<th style="text-align:center;">Upload</th>

								</tr>
							</thead>
							<tbody>
                            </div>';
        if (count($accessarray)) {
            foreach ($accessarray as $access) {
                $add      = ($access->access_add == 0) ? "<a id='add-" . $access->id . "' id='access_add-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_add-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $delete   = ($access->access_delete == 0) ? "<a id='access_delete-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_delete-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $update   = ($access->access_update == 0) ? "<a id='access_update-" . $access->id . "' class='check'><span><i class='icon-check-empty'></i></span></a>" : "<a id='access_update-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $publish  = ($access->access_publish == 0) ? "<a id='access_publish-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_publish-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $view     = ($access->access_view == 0) ? "<a id='access_view-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_view-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $download = ($access->access_download == 0) ? "<a id='access_download-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_download-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $upload   = ($access->access_upload == 0) ? "<a id='access_upload-" . $access->id . "' class='check'><i class='icon-check-empty'></i></a>" : "<a id='access_upload-" . $access->id . "' class='check'><i class='icon-check'></i></a>";
                $accesslist .= '
								<tr>
									<td>' . $access->module_name . '</td>
									<td style="text-align:center;">' . $view . '</td>
									<td style="text-align:center;">' . $add . '</td>
									<td style="text-align:center;">' . $delete . '</td>
									<td style="text-align:center;">' . $update . '</td>
									<td style="text-align:center;">' . $publish . '</td>
									<td style="text-align:center;">' . $upload . '</td>
									<td style="text-align:center;">' . $download . '</td>

								</tr>

							';
            }
            $accesslist .= '</tbody>
							</table>
						';
            return $accesslist;
        }
    }
    /**

     * This method is used to add new user type for control list (Multi Level Access)

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function addUserType(Request $request)
    {
        $usertype = $request->get('user_type');
        $data     = array(
            'user_type' => $usertype,
        );
        $usertype = UserTypeModel::create($data);
        $modules  = ModuleModel::getUndeletedModules();

        if (count($usertype)) {
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
        $response['type']    = 'error';
        $response['message'] = 'User Type can not be added!!';
        exit(json_encode($response));
    }
    /**

     * This method is used to add new user type for control list (Multi Level Access)

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function addModule(Request $request)
    {
        $module_name = $request->get('module_name');
        $data        = array(
            'module_name' => $module_name,
            'is_deleted'                       => '0',
        );
        $module = ModuleModel::create($data);
        if (count($module)) {
            $usertypes = UserTypeModel::getUndeletedUserTypes();
            foreach ($usertypes as $usertype) {
                if (AccessControlModel::checkAccessListItemByModuleAndUsertype($module->id, $usertype->id)) {
                    if ($usertype->id == 1) {
                        continue;
                    } elseif ($usertype->id == 2) {
                        $data = array(
                            'module_id' => $module->id,
                            'user_type_id'            => $usertype->id,
                            'access_add'              => '1',
                            'access_delete'           => '1',
                            'access_update'           => '1',
                            'access_publish'          => '1',
                            'access_view'             => '1',
                            'access_upload'           => '1',
                            'access_download'         => '1'
                        );
                    } else {
                        $data = array(
                            'module_id' => $module->id,
                            'user_type_id'            => $usertype->id,
                            'access_add'              => '0',
                            'access_delete'           => '0',
                            'access_update'           => '0',
                            'access_publish'          => '0',
                            'access_view'             => '0',
                            'access_upload'           => '0',
                            'access_download'         => '0'
                        );
                    }
                    AccessControlModel::create($data);
                }
            }
            $response['type']    = 'success';
            $response['message'] = 'Module has been added successfully!!';
            exit(json_encode($response));
        }
        $response['type']    = 'error';
        $response['message'] = 'Module can not be added!!';
        exit(json_encode($response));
    }
    /**

     * This method is used to add change the status of privilage

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function getEditAccessList($id)
    {
        /*if(Session::get('12_edit')!=1)
        {
        return view('500')->with('abs_site_url',$this->abs_site_url)
        ->with('rel_site_url',$this->rel_site_url);
        }*/
        $usertype   = UserTypeModel::find($id);
        $accesslist = '<tr>

									<td style="vertical-align:middle;">' . $usertype->user_type_name . '</td>
									<td style="text-align:center;">' . $this->getcontrollist($usertype->id) . '</td>

							  </tr>';

        return view('users.access.editaccess')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('accesslist', $accesslist);
    }
    /**

     * This method is used to add change the status of privilage

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function getEditAccessListUser($id)
    {
        /*if(Session::get('12_edit')!=1)
        {
        return view('500')->with('abs_site_url',$this->abs_site_url)
        ->with('rel_site_url',$this->rel_site_url);
        }*/
        $usermap    = UserMap::find($id);
        $userdetail = UserMap::getUserMapWithDetails($id);
        $accesslist = '<tr>

									<td style="vertical-align:middle;">
										<ul style="list-style:none; max-width: 150px;">
											<li>Name:</li>
											<li style="color:red;">' . $usermap->user_name . '</li>
											<li> User Details: </li>
											<li style="color:red; ">' . $userdetail[0]->detail . '</li>

										</ul>
									</td>
									<td style="text-align:center;">' . $this->getcontrollistUser($usermap->id) . '</td>

							  </tr>';

        return view('users.access.usereditaccess')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('accesslist', $accesslist);
    }
    /**

     * This method is used to add change the status of privilage of particular user

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function changeStatus(Request $request)
    {
        $data   = $request->get('data');
        $id     = substr(strstr($data, '-'), 1);
        $field  = strstr($data, '-', true);
        $access = AccessControlModel::find($id);
        $icon   = '';
        // dd();
        if ($access->$field == '0') {
            $access->$field = '1';
            $icon           = "<i class='icon-check'></i>";
        } else {
            $access->$field = '0';
            $icon           = "<i class='icon-check-empty'></i>";
        }

        // dd($access->$field);

        $access->save();
        $response['type']    = "success";
        $response['message'] = $icon;
        exit(json_encode($response));
    }

    /**

     * This method is used to add change the status of privilage of particular user

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function changeStatusUser(Request $request)
    {
        $data   = $request->get('data');
        $id     = substr(strstr($data, '-'), 1);
        $field  = strstr($data, '-', true);
        $access = UserAccessControlModel::find($id);
        $icon   = '';
        if ($access->$field == '0') {
            $access->$field = '1';
            $icon           = "<i class='icon-check'></i>";
        } else {
            $access->$field = '0';
            $icon           = "<i class='icon-check-empty'></i>";
        }
        $access->save();
        $response['type']    = "success";
        $response['message'] = $icon;
        exit(json_encode($response));
    }

    /**

     * This method is used to add change the status of privilage

     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string $blah - none

     * @return null
     */
    public function updateUserType(Request $request)
    {
        $id                  = $request->get('id');
        $usertype            = UserTypeModel::find($id);
        $usertype->user_type = $request->get('user_type');
        $usertype->save();
        $response['type']       = "success";
        $response['message']    = "User type Updated Sucessfully.";
        $response['accesslist'] = $this->getAccessList();
        exit(json_encode($response));
    }

    public function deleteUserType(Request $request)
    {
        $id                      = $request->get('id');
        $usertype                = UserTypeModel::find($id);
        $usertype->delete_status = 1;
        $usertype->save();
        $response['type']       = "success";
        $response['message']    = "User type has been deleted  Sucessfully.";
        $response['accesslist'] = $this->getAccessList();
        exit(json_encode($response));
    }

    public function getAccessList()
    {
        $usertypes  = UserTypeModel::getUndeletedUserTypes();
        $editlink   = '';
        $deletelink = '';
        $accesslist = '';
        $sn         = 1;
        foreach ($usertypes as $usertype) {
            $editlink = '';
            if ($usertype->id > 3) {
                $editlink = '<a href="' . '/admin/access-control/edit/' . $usertype->id . '" style="text-decoration:none;"><i class="icon-edit"></i> Update Privillages</a>';
            }

            $accesslist .= '<tr>
									<td style="vertical-align:middle;">' . $sn . '</td>
									<td style="vertical-align:middle;">' . $usertype->user_type_name . '</td>
									<td style="vertical-align:middle;"> ' . $editlink . '</td>

							  </tr>';
            $sn++;
        }
        return $accesslist;
    }
    //=============================================================================
    public function getUserTypeDynamically(Request $request)
    {
        $id                   = $request->get('id');
        $usertype             = UserTypeModel::find($id);
        $response['type']     = 'success';
        $response['usertype'] = $usertype->user_type;
        exit(json_encode($response));
    }
    //==================================================================================
    public function getAccessListUser()
    {
        $users      = UserMap::getActiveUserMapWithDetails();
        $editlink   = '';
        $deletelink = '';
        $accesslist = '';
        $sn         = 1;
        foreach ($users as $user) {
            $editlink = '';
            $editlink = '<a href="' . '/admin/access-control-user/edit/' . $user->id . '" style="text-decoration:none;"><i class="icon-edit"></i> Update Privillages</a>';

            $accesslist .= '<tr>
									<td style="vertical-align:middle;">' . $sn . '</td>
									<td style="vertical-align:middle;">' . $user->user_name . '</td>
									<td style="vertical-align:middle;">' . $user->detail . '</td>
									<td style="vertical-align:middle;"> ' . $editlink . '</td>

							  </tr>';
            $sn++;
        }
        return $accesslist;
    }
}

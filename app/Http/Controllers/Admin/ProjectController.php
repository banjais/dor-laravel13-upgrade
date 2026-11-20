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
use App\Models\Project;
use App\Models\Projectdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends ConfigurationController
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
     * get project list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(20, $authUser->id);

        if ($viewAccess) {
            $projects = Project::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderBy('created_at', 'desc')
                ->get();

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(20, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(20, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(20, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(20, $authUser->id);

            return view('projects.admin.list')
                ->with('projects', $projects)
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
     * get project add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddProject()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(20, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('projects.admin.add')
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
     * post project add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addProject(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['project_url' => 'required|unique:project,project_url'];
            foreach ($languages as $language) {
                $rules['project_heading_' . $language->id]     = 'required';
                $rules['project_description_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $projectUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('project_url')))), '-');
                $projectExist = Project::checkExist($projectUrl, $authUser->office_id);
                if ($projectExist) {
                    $message = 'project with url <b>' . $projectUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/pages/';
                    $rEFileTypes      = "/^\.(png|jpg|jpeg|gif|png){1}$/i";
                    $maximum_filesize = 2 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "pages" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectProject                           = new Project();
                    $objectProject->project_url              = $projectUrl;
                    $objectProject->project_meta_tags        = $request->get('project_meta_tags');
                    $objectProject->project_meta_description = $request->get('project_meta_description');

                    $objectProject->project_class              = $request->get('project_class');
                    $objectProject->project_ref_no             = $request->get('project_ref_no');
                    $objectProject->project_link_code          = $request->get('project_link_code');
                    $objectProject->project_black_tapped       = $request->get('project_black_tapped');
                    $objectProject->project_gravel             = $request->get('project_gravel');
                    $objectProject->project_earthen            = $request->get('project_earthen');
                    $objectProject->project_under_construction = $request->get('project_under_construction');
                    $objectProject->project_plan               = $request->get('project_plan');

                    $objectProject->project_latitude  = $request->get('project_latitude');
                    $objectProject->project_longitude = $request->get('project_longitude');
                    $objectProject->project_zoom      = $request->get('project_zoom');
                    // dd($request->get('google_map_embed'));
                    $objectProject->google_map_embed      = $request->get('google_map_embed');
                    $objectProject->is_active         = $request->get('is_active');
                    if ($logo) {
                        $objectProject->project_attachment = $logo;
                    }

                    $objectProject->created_by = $authUser->id;
                    $objectProject->updated_by = $authUser->id;
                    $objectProject->office_id  = $authUser->office_id;
                    $objectProject->save();

                    if ($objectProject->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'project_id'                => $objectProject->id,
                                'language_id'               => $language->id,
                                'project_heading'           => trim($request->get('project_heading_' . $language->id)),
                                'project_description'       => $request->get('project_description_' . $language->id),
                                'project_page_title'        => trim($request->get('project_page_title_' . $language->id)),
                                'created_at'                => date("Y-m-d H:i:s"),
                                'updated_at'                => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('project_detail')->insert($insertArray);

                        return Redirect::route('admin.projects')
                            ->with('type', 'success')
                            ->with('message', 'New project is added successfully.');
                    }
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New project is not added.');
        }
    }

    /**
     * get project edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditProject($id)
    {
        try {
            $officeId      = Auth::user()->office_id;
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(20, Auth::User()->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(20, Auth::User()->id);
            $objectProject = Project::find($id);

            if ($objectProject && $editAccess) {
                if ($objectProject->office_id == $officeId) {
                    $languages   = Language::getActiveLanguages();
                    $projectData = Project::find($id);
                    $projects = Project::getProjectDetailsWithLanguages($id);
                    //var_dump($projectData); exit;
                    return view('projects.admin.edit')
                        ->with('projects', $projects)
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name)
                        ->with('languages', $languages)
                        ->with('addAccess', $addAccess)
                        ->with('projectData', $projectData);
                }
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
     * post project edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editProject(Request $request)
    {
        $projectId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages   = Language::getActiveLanguages();
        $projects    = Project::getProjectDetailsWithLanguages($projectId);
        $projectData = Project::find($projectId);
        $officeId    = Auth::user()->office_id;
        $addAccess   = UserAccessControlModel::getAccessAddByModuleAndUserMapId(20, Auth::User()->id);

        try {
            $rules = ['project_url' => 'required|unique:project,project_url,' . $projectId];
            foreach ($languages as $language) {
                $rules['project_heading_' . $language->id]     = 'required';
                $rules['project_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $projectUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('project_url')))), '-');
                $projectExist = Project::checkExist($projectUrl, $officeId);

                if ($projectExist && $projectExist != $projectId) {
                    $message = 'Project with url <b>' . $projectUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/projects/';
                    $rEFileTypes      = "/^\.(png|jpg|jpeg|gif|png){1}$/i";
                    $maximum_filesize = 2 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "pages" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                        }
                    } else {
                        $attachment_deleted = $request->get('attachment_deleted');
                    }

                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectProject                           = Project::find($projectId);
                    $objectProject->project_url              = $projectUrl;
                    $objectProject->project_meta_tags        = $request->get('project_meta_tags');
                    $objectProject->project_meta_description = $request->get('project_meta_description');

                    $objectProject->project_class              = $request->get('project_class');
                    $objectProject->project_ref_no             = $request->get('project_ref_no');
                    $objectProject->project_link_code          = $request->get('project_link_code');
                    $objectProject->project_black_tapped       = $request->get('project_black_tapped');
                    $objectProject->project_gravel             = $request->get('project_gravel');
                    $objectProject->project_earthen            = $request->get('project_earthen');
                    $objectProject->project_under_construction = $request->get('project_under_construction');
                    $objectProject->project_plan               = $request->get('project_plan');

                    $objectProject->project_latitude  = $request->get('project_latitude');
                    $objectProject->project_longitude = $request->get('project_longitude');
                    // $objectProject->project_zoom      = $request->get('project_zoom');
                    $objectProject->google_map_embed      = $request->get('google_map_embed');
                    // dd($request->get('google_map_embed'));
                    $objectProject->is_active         = $request->get('is_active');
                    if ($logo) {
                        $objectProject->project_attachment = $logo;
                    }
                    else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        $objectProject->project_attachment = null;
                    }

                    $objectProject->updated_by = Auth::user()->id;
                    $objectProject->save();

                    if ($objectProject->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId            = $request->get('detail_id_' . $language->id);
                            $objectProjectDetail = Projectdetail::find($detailId);
                            if ($objectProjectDetail) {
                                $objectProjectDetail->project_heading           = trim($request->get('project_heading_' . $language->id));
                                $objectProjectDetail->project_description       = trim($request->get('project_description_' . $language->id));
                                $objectProjectDetail->project_page_title        = trim($request->get('project_page_title_' . $language->id));
                                $objectProjectDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'project_id'                => $objectProject->id,
                                    'language_id'               => $language->id,
                                    'project_heading'           => trim($request->get('project_heading_' . $language->id)),
                                    'project_description'       => trim($request->get('project_description_' . $language->id)),
                                    'project_page_title'        => trim($request->get('project_page_title_' . $language->id)),
                                    'created_at'                => date("Y-m-d H:i:s"),
                                    'updated_at'                => date("Y-m-d H:i:s"),
                                ];
                            }
                        }
                        if (count($insertArray) > 0) {
                            DB::table('project_detail')->insert($insertArray);
                        }
                    }
                    return Redirect::route('admin.projects')
                        ->with('type', 'success')
                        ->with('message', 'Project is updated successfully.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Project is not updated.');
        }
    }

    /*
     * project delete action admin panel
     * @return JSON
     */
    public function deleteProject(Request $request)
    {
        $projectId                 = $request->get('id');
        $objectProject             = Project::find($projectId);
        $objectProject->is_active  = '0';
        $objectProject->is_deleted = '1';
        $objectProject->save();

        if ($objectProject->id) {
            $data['type']    = "success";
            $data['message'] = "Project deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * project staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $projectId     = $request->get('id');
        $objectProject = Project::find($projectId);
        if ($objectProject->is_active == '0') {
            $objectProject->is_active = '1';
            $objectProject->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The project has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectProject->is_active = '0';
            $objectProject->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The project has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

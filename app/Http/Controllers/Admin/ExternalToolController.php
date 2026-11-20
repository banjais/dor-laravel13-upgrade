<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\ExternalTool;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ExternalToolController extends ConfigurationController
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
     * get guideline list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(33, Auth::User()->id);

        if ($viewAccess) {
            $tools = ExternalTool::orderBy('created_at', 'desc')->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(4, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(4, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(4, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(4, $authUser->guideline);

            return view('tools.admin.tools')
                ->with('tools', $tools)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addAccess', $addAccess)
                ->with('editAccess', $editAccess)
                ->with('publishAccess', $publishAccess)
                ->with('deleteAccess', $deleteAccess);
        } else {
            return view('error')
                ->with('abs_site_url', $this->guideline_e_url)
                ->with('rel_site_url', $this->guidelinete_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get tool add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddTool()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(33, Auth::User()->id);
        if ($addAccess) {
            return view('tools.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * post tool add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addTool(Request $request)
    {
        $authUser = Auth::user();
        Session::forget('class');
        Session::forget('message');

        try {
            $rules = [
                'tool_url' => 'required',
                'tool_icon' => 'required',
                'display_text' => 'required',
                'hover_text' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $data = $request->only(['tool_url', 'tool_icon', 'hover_text', 'display_text']);
                $data['created_by']              = $authUser->id;
                $data['updated_by']              = $authUser->id;
                $data['published_date']          = trim($request->get('published_date'));
                $data['is_active']               = $request->get('is_active');
                ExternalTool::create($data);
                return Redirect::route('admin.tools')
                    ->with('type', 'success')
                    ->with('message', 'Tool successfully added.');
            }
        } catch (Throwable $e) {
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New tool could not be added.');
        }
    }

    /**
     * get tool edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditTool($id)
    {
        try {
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(33, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(33, Auth::User()->id);
            $toolData = ExternalTool::find($id);

            if ($editAccess) {
                return view('tools.admin.edit')
                    ->with('toolData', $toolData)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('addAccess', $addAccess);
            } else {
                return view('error')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->guideline_e_url)
                    ->with('site_name', $this->guidelinee);
            }
        } catch (Throwable $e) {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * post tool edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editTool($id, Request $request)
    {
        $toolId = $id;
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        $rules = [
            'tool_url' => 'required',
            'tool_icon' => 'required',
            'display_text' => 'required',
            'hover_text' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            try {
                $editAccess  = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(33, Auth::User()->id);
                if (!$editAccess) {
                    return view('error')
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->guideline_e_url)
                        ->with('site_name', $this->guidelinee);
                }
                $toolData = ExternalTool::find($toolId);
                $toolData->fill(
                    $request->all()
                )->save();
                return Redirect::route('admin.tools')
                    ->with('type', 'success')
                    ->with('message', 'Tool successfully updated.');
            } catch (Throwable $e) {
                dd($e->getMessage());
                return Redirect::back()->withInput()
                    ->with('type', 'error')
                    ->with('message', 'Tool could not be updated.');
            }
        }
    }


    /*
     * tool delete action admin panel
     * @return JSON
     */
    public function deleteTool(Request $request)
    {
        $itemId                 = $request->get('id');
        $objectTool             = ExternalTool::find($itemId);
        $objectTool->is_active  = '0';
        $objectTool->is_deleted = '1';
        $objectTool->save();

        if ($objectTool->id) {
            $data['type']    = "success";
            $data['message'] = "Guideline deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * item stauts change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $itemId     = $request->get('id');
        $objectTool = ExternalTool::find($itemId);
        if ($objectTool->is_active == '0') {
            $objectTool->is_active = '1';
            $objectTool->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The tool has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectTool->is_active = '0';
            $objectTool->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The tool has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

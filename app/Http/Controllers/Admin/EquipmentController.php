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
use App\Models\Equipment;
use App\Models\Equipmentlog;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EquipmentController extends ConfigurationController
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
     * get equipment list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(21, $authUser->id);

        if ($viewAccess) {
            $equipments = Equipment::getUndeletedEquipments();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(21, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(21, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(21, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(21, $authUser->id);

            return view('equipment.admin.list')
                ->with('equipments', $equipments)
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
     * get equipment add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddEquipment()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(21, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('equipment.admin.add')
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
     * post equipment add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addEquipment(Request $request)
    {
        $languages = Language::getActiveLanguages();

        try {
            $rules = ['equipment_name' => 'required'];
            $rules['equipment_identification'] = 'required';
            $rules['equipment_dor_code'] = 'required|numeric';
            $rules['equipment_per_hour'] = 'required|numeric';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $objectEquipment                              = new Equipment();
                $objectEquipment->equipment_name              = $request->get('equipment_name');
                $objectEquipment->equipment_identification    = $request->get('equipment_identification');
                $objectEquipment->equipment_capacity_category = $request->get('equipment_capacity_category');
                $objectEquipment->equipment_dor_code          = $request->get('equipment_dor_code');
                $objectEquipment->equipment_per_hour          = $request->get('equipment_per_hour');
                $objectEquipment->equipment_per_shift         = $request->get('equipment_per_hour') * 7;
                $objectEquipment->is_active                   = $request->get('is_active');
                $objectEquipment->created_by                  = Auth::user()->id;
                $objectEquipment->updated_by                  = Auth::user()->id;
                $objectEquipment->save();

                return Redirect::route('admin.equipments')
                    ->with('type', 'success')
                    ->with('message', 'New equipment is added successfully.');
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New equipment is not added.');
        }
    }

    /**
     * get equipment edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditEquipment($id)
    {

        try {
            $officeId        = Auth::user()->office_id;
            $addAccess       = UserAccessControlModel::getAccessAddByModuleAndUserMapId(21, Auth::User()->id);
            $editAccess      = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(21, Auth::User()->id);
            $objectEquipment = Equipment::find($id);

            if ($objectEquipment && $editAccess) {
                $languages = Language::getActiveLanguages();
                $equipment = Equipment::getEquipmentDetails($id);

                return view('equipment.admin.edit')
                    ->with('equipment', $equipment)
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

    /**
     * post equipment edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editEquipment(Request $request)
    {
        $equipmentId = $request->get('id');
        $languages = Language::getActiveLanguages();
        $equipment = Equipment::getEquipmentDetails($equipmentId);
        $officeId  = Auth::user()->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(21, Auth::User()->id);

        try {
            $rules = ['equipment_name' => 'required'];
            $rules['equipment_identification'] = 'required';
            $rules['equipment_dor_code'] = 'required';
            $rules['equipment_per_hour'] = 'required';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $objectEquipment                              = Equipment::find($equipmentId);
                $objectEquipment->equipment_name              = $request->get('equipment_name');
                $objectEquipment->equipment_identification    = $request->get('equipment_identification');
                $objectEquipment->equipment_capacity_category = $request->get('equipment_capacity_category');
                $objectEquipment->equipment_dor_code          = $request->get('equipment_dor_code');
                $objectEquipment->equipment_per_hour          = $request->get('equipment_per_hour');
                $objectEquipment->equipment_per_shift         = $request->get('equipment_per_hour') * 7;
                $objectEquipment->is_active                   = $request->get('is_active');
                $objectEquipment->updated_by                  = Auth::user()->id;
                $objectEquipment->save();

                return Redirect::route('admin.equipments')
                    ->with('type', 'success')
                    ->with('message', 'Equipment is updated successfully.');
            }
        } catch (Throwable $e) {
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Equipment is not updated.');
        }
    }

    /*
     * equipment delete action admin panel
     * @return JSON
     */
    public function deleteEquipment(Request $request)
    {
        $equipmentId                 = $request->get('id');
        $objectEquipment             = Equipment::find($equipmentId);
        $objectEquipment->is_active  = '0';
        $objectEquipment->is_deleted = '1';
        $objectEquipment->save();

        if ($objectEquipment->id) {
            $data['type']    = "success";
            $data['message'] = "Equipment deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * equipment staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $equipmentId     = $request->get('id');
        $objectEquipment = Equipment::find($equipmentId);
        if ($objectEquipment->is_active == '0') {
            $objectEquipment->is_active = '1';
            $objectEquipment->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The equipment has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectEquipment->is_active = '0';
            $objectEquipment->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The equipment has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }

    /*
     * add available equipment log
     * @return JSON
     */
    public function addEquipmentLog(Request $request)
    {
        $officeId    = Auth::user()->office_id;
        $equipmentId = $request->get('equipment_id');
        $checkExist  = Equipmentlog::checkExist($officeId, $equipmentId);
        if ($checkExist) {
            $checkExist->equipment_log = $request->get('equipment_log');
            $checkExist->is_active     = 1;
            $checkExist->updated_by    = Auth::user()->id;
            $checkExist->save();
            $feedback['message'] = 'The equipment log has been updated successfully';
        } else {
            // dd(1);
            $objectEquipmentLog                = new Equipmentlog();
            $objectEquipmentLog->office_id     = $officeId;
            $objectEquipmentLog->equipment_id  = $equipmentId;
            $objectEquipmentLog->equipment_log = $request->get('equipment_log');
            $objectEquipmentLog->is_active     = 1;
            $objectEquipmentLog->created_by    = Auth::user()->id;
            $objectEquipmentLog->updated_by    = Auth::user()->id;
            $objectEquipmentLog->save();
            $feedback['message'] = 'The equipment log has been added successfully';
        }
        $equipmentLog              = Equipmentlog::findLog($officeId, $equipmentId);
        $feedback['type']          = 'success';
        $feedback['equipment_log'] = $equipmentLog;
        $feedback['icon']          = '<i class="icon-check"></i>';
        exit(json_encode($feedback));
    }

    /*
     * find available equipment log
     * @return JSON
     */
    public function findEquipmentLog(Request $request)
    {
        $officeId                  = Auth::user()->office_id;
        $equipmentId               = $request->get('equipment_id');
        $equipmentLog              = Equipmentlog::findLog($officeId, $equipmentId);
        $feedback['equipment_log'] = 0;
        if ($equipmentLog) {
            $feedback['equipment_log'] = $equipmentLog->equipment_log;
        }
        exit(json_encode($feedback));
    }

    /**
     * get equipment log detail admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function viewEquipmentDetail($id)
    {
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(21, Auth::User()->id);

        if ($viewAccess) {
            $equipmentLogs = Equipmentlog::viewEquipmentlog($id);
            $equipment     = Equipment::find($id);
            //echo Configuration::get_last_query(); exit();

            return view('equipment.admin.view-detail')
                ->with('equipment', $equipment)
                ->with('equipmentLogs', $equipmentLogs)
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
}

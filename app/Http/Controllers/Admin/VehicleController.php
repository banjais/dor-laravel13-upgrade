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
use App\Models\UserAccessControlModel;
use App\Models\Vehicle;
use App\Models\Vehicledetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class VehicleController extends ConfigurationController
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
     * get vehicle list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(27, $authUser->id);

        if ($viewAccess) {
            $vehicles = Vehicle::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderBy('created_at', 'desc')
                ->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(27, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(27, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(27, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(27, $authUser->id);

            return view('vehicles.admin.list')
                ->with('vehicles', $vehicles)
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
     * get vehicle add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddVehicle()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(27, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('vehicles.admin.add')
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
     * post vehicle add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addVehicle(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();

        try {
            foreach ($languages as $language) {
                $rules['vehicle_type_' . $language->id]    = 'required';
                $rules['vehicle_num_' . $language->id]     = 'required';
                $rules['vehicle_remarks_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $objectVehicle                 = new Vehicle();
                $objectVehicle->office_id      = $authUser->office_id;
                $objectVehicle->condition_type = $request->get('condition_type');
                $objectVehicle->is_active      = $request->get('is_active');
                $objectVehicle->created_by     = $authUser->id;
                $objectVehicle->updated_by     = $authUser->id;
                $objectVehicle->save();

                if ($objectVehicle->id) {
                    foreach ($languages as $language) {
                        $insertArray[] = [
                            'vehicle_id'     => $objectVehicle->id,
                            'language_id'    => $language->id,
                            'vehicle_type'   => $request->get('vehicle_type_' . $language->id),
                            'vehicle_number' => $request->get('vehicle_num_' . $language->id),
                            'condition'      => $request->get('vehicle_remarks_' . $language->id),
                            'created_at'     => date("Y-m-d H:i:s"),
                            'updated_at'     => date("Y-m-d H:i:s"),
                        ];
                    }
                    DB::table('vehicle_detail')->insert($insertArray);

                    return Redirect::route('admin.vehicles')
                        ->with('type', 'success')
                        ->with('message', 'New vehicle is added successfully.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New vehicle is not added.');
        }
    }

    /**
     * get vehicle edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditVehicle($id)
    {

        try {
            $officeId    = Auth::user()->office_id;
            $addAccess   = UserAccessControlModel::getAccessAddByModuleAndUserMapId(27, Auth::User()->id);
            $editAccess  = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(27, Auth::User()->id);
            $vehicleData = Vehicle::find($id);

            if ($vehicleData && $editAccess) {
                $languages = Language::getActiveLanguages();
                $vehicles  = Vehicle::getVehicleDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();
                // echo "<pre>";print_r($id);exit;
                return view('vehicles.admin.edit')
                    ->with('vehicles', $vehicles)
                    ->with('vehicleData', $vehicleData)
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
     * post vehicle edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editVehicle(Request $request)
    {
        $vehicleId = $request->get('id');
        $languages   = Language::getActiveLanguages();
        $vehicles    = Vehicle::getVehicleDetailsWithLanguages($vehicleId);
        $officeId    = Auth::user()->office_id;
        $addAccess   = UserAccessControlModel::getAccessAddByModuleAndUserMapId(27, Auth::User()->id);
        $vehicleData = Vehicle::find($vehicleId);

        try {

            foreach ($languages as $language) {
                $rules['vehicle_type_' . $language->id]    = 'required';
                $rules['vehicle_num_' . $language->id]     = 'required';
                $rules['vehicle_remarks_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $objectVehicle                 = Vehicle::find($vehicleId);
                $objectVehicle->vehicle_name   = $request->get('vehicle_name');
                $objectVehicle->condition_type = $request->get('condition_type');
                $objectVehicle->updated_by     = Auth::user()->id;
                // $objectVehicle->vehicle_received_date    = $request->get('vehicle_received_date');
                $objectVehicle->is_active = $request->get('is_active');
                $objectVehicle->save();

                if ($objectVehicle->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);

                        $objectVehicleDetail = Vehicledetail::find($detailId);
                        // print_r($objectVehicleDetail);exit;
                        if ($objectVehicleDetail) {
                            $objectVehicleDetail->vehicle_type   = trim($request->get('vehicle_type_' . $language->id));
                            $objectVehicleDetail->vehicle_number = trim($request->get('vehicle_num_' . $language->id));
                            $objectVehicleDetail->condition      = trim($request->get('vehicle_remarks_' . $language->id));

                            $objectVehicleDetail->save();
                            continue;
                        } else {
                            $insertArray[] = [
                                'vehicle_id'     => $objectVehicle->id,
                                'language_id'    => $language->id,
                                'vehicle_type'   => $request->get('vehicle_type_' . $language->id),
                                'vehicle_number' => $request->get('vehicle_num_' . $language->id),
                                'condition'      => $request->get('vehicle_remarks_' . $language->id),
                                'created_at'     => date("Y-m-d H:i:s"),
                                'updated_at'     => date("Y-m-d H:i:s"),
                            ];
                        }
                    }

                    if (count($insertArray) > 0) {
                        DB::table('vehicle_detail')->insert($insertArray);
                    }
                }
                return Redirect::route('admin.vehicles')
                    ->with('type', 'success')
                    ->with('message', 'Vehicle is updated successfully.');
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Vehicle is not updated.');
        }
    }

    /*
     * vehicle delete action admin panel
     * @return JSON
     */
    public function deleteVehicle(Request $request)
    {
        $vehicleId                 = $request->get('id');
        $objectVehicle             = Vehicle::find($vehicleId);
        $objectVehicle->is_active  = '0';
        $objectVehicle->is_deleted = '1';
        $objectVehicle->save();

        if ($objectVehicle->id) {
            $data['type']    = "success";
            $data['message'] = "Vehicle deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * vehicle staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $vehicleId     = $request->get('id');
        $objectVehicle = Vehicle::find($vehicleId);
        if ($objectVehicle->is_active == '0') {
            $objectVehicle->is_active = '1';
            $objectVehicle->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The vehicle has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectVehicle->is_active = '0';
            $objectVehicle->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The vehicle has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

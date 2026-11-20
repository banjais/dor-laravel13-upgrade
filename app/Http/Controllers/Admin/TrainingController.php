<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Sunil Adhikari
 * @copyto 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Training;
use App\Models\Trainingdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TrainingController extends ConfigurationController
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
     * get training list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(24, $authUser->id);

        if ($viewAccess) {
            $trainings = Training::getUndeletedTrainings();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(24, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(24, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(24, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(24, $authUser->id);

            return view('training.admin.list')
                ->with('trainings', $trainings)
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

    /**
     * get training add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddTraining()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(24, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('training.admin.add')
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

    /**
     * post training add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addTraining(Request $request)
    {
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules['training_url']        = 'required|unique:training,training_url';
            $rules['training_start_date'] = 'required';
            $rules['training_end_date']   = 'required';
            foreach ($languages as $language) {
                $rules['training_heading_' . $language->id]     = 'required';
                $rules['training_description_' . $language->id] = 'required';
                $rules['training_page_title_' . $language->id]  = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $imageFile        = $request->file('attachment');
                $destinationPath  = 'uploads/training/';
                $rEFileTypes      = "/^\.(jpg|jpeg|png){1}$/i";
                $maximum_filesize = 1 * 1024 * 1024;

                if ($imageFile) {
                    $filename       = $imageFile->getClientOriginalName();
                    $extension      = strrchr($filename, '.');
                    $size           = $imageFile->getSize();
                    $new_image_name = "training" . "_" . time();

                    if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                        $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                    } else if (preg_match($rEFileTypes, $extension) == false) {
                        Session::flash('attachment-error', 'Warning : Invalid Image File!');
                    } else if ($size > $maximum_filesize) {
                        Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                    }
                }
                $logo = isset($attachment) ? $new_image_name . $extension : null;

                $objectTraining                            = new Training();
                $objectTraining->training_url              = $request->get('training_url');
                $objectTraining->training_meta_tags        = $request->get('training_meta_tags');
                $objectTraining->training_meta_description = $request->get('training_meta_description');
                $objectTraining->training_attachment       = $logo;
                $objectTraining->training_start_date       = $request->get('training_start_date');
                $objectTraining->training_end_date         = $request->get('training_end_date');
                $objectTraining->number_of_traniees        = $request->get('number_of_traniees');
                $objectTraining->is_active                 = $request->get('is_active');
                $objectTraining->created_by                = Auth::user()->id;
                $objectTraining->updated_by                = Auth::user()->id;
                //var_dump($objectTraining);die();
                $objectTraining->save();

                if ($objectTraining->id) {
                    foreach ($languages as $language) {
                        $insertArray[] = [
                            'training_id'             => $objectTraining->id,
                            'language_id'             => $language->id,
                            'training_heading'        => $request->get('training_heading_' . $language->id),
                            'training_description'    => $request->get('training_description_' . $language->id),
                            'training_page_title'     => $request->get('training_page_title_' . $language->id),
                            // 'minimum_qualification'   => $request->get('minimum_qulification_' . $language->id),
                            'training_remarks'        => $request->get('training_remarks_' . $language->id),
                            'training_contact_person' => $request->get('training_contact_person_' . $language->id),
                            'training_contact_number' => $request->get('training_contact_number_' . $language->id),
                            'created_at'              => date("Y-m-d H:i:s"),
                            'updated_at'              => date("Y-m-d H:i:s"),
                            'training_start_date' => $request->get('training_start_date'),
                            'training_end_date' => $request->get('training_end_date'),
                        ];
                    }
                    //var_dump($insertArray);die();
                    DB::table('training_detail')->insert($insertArray);

                    return Redirect::route('admin.trainings')
                        ->with('type', 'success')
                        ->with('message', 'New training is added successfully.');
                }
            }
        } catch (Throwable $e) {
            // dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New training is not added.');
        }
    }

    /**
     * get training edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditTraining($id)
    {
        try {
            $officeId       = Auth::user()->office_id;
            $addAccess      = UserAccessControlModel::getAccessAddByModuleAndUserMapId(24, Auth::User()->id);
            $editAccess     = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(24, Auth::User()->id);
            $objectTraining = Training::find($id);

            if ($objectTraining && $editAccess) {
                $languages    = Language::getActiveLanguages();
                $trainingData = Training::find($id);
                $trainings    = Training::getTrainingDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('training.admin.edit')
                    ->with('trainingData', $trainingData)
                    ->with('trainings', $trainings)
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
            // dd($e->getMessage());
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * post training edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editTraining(Request $request)
    {
        $trainingId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages    = Language::getActiveLanguages();
        $trainings    = Training::getTrainingDetailsWithLanguages($trainingId);
        $officeId     = Auth::user()->office_id;
        $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(24, Auth::User()->id);
        $trainingData = Training::find($trainingId);

        try {
            $rules['training_url']        = 'required|unique:training,training_url,' . $trainingId;
            $rules['training_start_date'] = 'required';
            $rules['training_end_date']   = 'required';
            foreach ($languages as $language) {
                $rules['training_heading_' . $language->id]     = 'required';
                $rules['training_description_' . $language->id] = 'required';
                $rules['training_page_title_' . $language->id]  = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $imageFile        = $request->file('attachment');
                $destinationPath  = 'uploads/training/';
                $rEFileTypes      = "/^\.(jpg|jpeg|png){1}$/i";
                $maximum_filesize = 1 * 1024 * 1024;

                if ($imageFile) {
                    $filename       = $imageFile->getClientOriginalName();
                    $extension      = strrchr($filename, '.');
                    $size           = $imageFile->getSize();
                    $new_image_name = "training" . "_" . time();

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

                $objectTraining                            = Training::find($trainingId);
                $objectTraining->training_url              = $request->get('training_url');
                $objectTraining->training_meta_tags        = $request->get('training_meta_tags');
                $objectTraining->training_meta_description = $request->get('training_meta_description');
                $objectTraining->training_start_date       = $request->get('training_start_date');
                $objectTraining->training_end_date         = $request->get('training_end_date');

                $number_of_traniees = 0;
                if (trim($request->get('number_of_traniees'))) {
                    $number_of_traniees = $request->get('number_of_traniees');
                }

                $objectTraining->number_of_traniees        = $number_of_traniees;
                $objectTraining->is_active                 = $request->get('is_active');

                if ($logo) {
                    if (file_exists("uploads/training/" . $objectTraining->training_attachment) && $objectTraining->training_attachment != '') {
                        unlink('uploads/training/' . $objectTraining->training_attachment);
                    }
                    $objectTraining->training_attachment = $logo;
                } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                    if (file_exists("uploads/training/" . $objectTraining->training_attachment) && $objectTraining->training_attachment != '') {
                        unlink('uploads/training/' . $objectTraining->training_attachment);
                    }

                    $objectTraining->training_attachment = null;
                }

                $objectTraining->updated_by = Auth::user()->id;
                $objectTraining->save();

                if ($objectTraining->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);

                        $objectTrainingDetail = Trainingdetail::find($detailId);
                        if ($objectTrainingDetail) {
                            $objectTrainingDetail->training_heading        = trim($request->get('training_heading_' . $language->id));
                            $objectTrainingDetail->training_description    = trim($request->get('training_description_' . $language->id));
                            $objectTrainingDetail->training_page_title     = trim($request->get('training_page_title_' . $language->id));
                            $objectTrainingDetail->training_remarks        = trim($request->get('training_remarks_' . $language->id));
                            // $objectTrainingDetail->minimum_qualification   = trim($request->get('minimum_qualification_' . $language->id));
                            $objectTrainingDetail->training_contact_person = trim($request->get('training_contact_person_' . $language->id));
                            $objectTrainingDetail->training_contact_number = trim($request->get('training_contact_number_' . $language->id));
                            $objectTrainingDetail->save();
                            continue;
                        } else {
                            $insertArray[] = [
                                'training_id'             => $objectTraining->id,
                                'language_id'             => $language->id,
                                'training_heading'        => $request->get('training_heading_' . $language->id),
                                'training_description'    => $request->get('training_description_' . $language->id),
                                'training_page_title'     => $request->get('training_page_title_' . $language->id),
                                'training_remarks'        => $request->get('training_remarks_' . $language->id),
                                // 'minimum_qualification'   => $request->get('minimum_qualification_' . $language->id),
                                'training_contact_person' => $request->get('training_contact_person_' . $language->id),
                                'training_contact_number' => $request->get('training_contact_number_' . $language->id),
                                'created_at'              => date("Y-m-d H:i:s"),
                                'updated_at'              => date("Y-m-d H:i:s"),
                            ];
                        }
                    }

                    if (count($insertArray) > 0) {
                        DB::table('training_detail')->insert($insertArray);
                    }
                }
                return Redirect::route('admin.trainings')
                    ->with('type', 'success')
                    ->with('message', 'Training is updated successfully.');
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Training is not updated.');
        }
    }

    /**
     * training delete action admin panel
     * @return JSON
     */
    public function deleteTraining(Request $request)
    {
        $trainingId                 = $request->get('id');
        $objectTraining             = Training::find($trainingId);
        $objectTraining->is_active  = '0';
        $objectTraining->is_deleted = '1';
        $objectTraining->save();

        if ($objectTraining->id) {
            $data['type']    = "success";
            $data['message'] = "Training deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /**
     * training staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $trainingId     = $request->get('id');
        $objectTraining = Training::find($trainingId);
        if ($objectTraining->is_active == '0') {
            $objectTraining->is_active = '1';
            $objectTraining->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The training has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectTraining->is_active = '0';
            $objectTraining->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The training has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

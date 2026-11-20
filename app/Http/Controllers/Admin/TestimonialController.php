<?php

/**
 * Project Name: Department of Road(DOR)
 * @author	Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Testimonial;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Testimonialdetail;

class TestimonialController extends ConfigurationController
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;
    public $officeId;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name = ConfigurationController::site_name();
    }

    /*
	* get testimonial list page admin panel
	* @param void
	* @return Illuminate\View\View
	*/
    public function getIndex()
    {
        $viewAccess        = UserAccessControlModel::getAccessViewByModuleAndUserMapId(14, Auth::User()->id);

        if ($viewAccess) {
            $testimonials        = Testimonial::getUndeletedTestimonials();
            //echo Configuration::get_last_query(); exit();

            $addAccess        = UserAccessControlModel::getAccessAddByModuleAndUserMapId(14, Auth::User()->id);
            $editAccess        = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(14, Auth::User()->id);
            $publishAccess    = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(14, Auth::User()->id);
            $deleteAccess    = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(14, Auth::User()->id);

            return view('testimonial.admin.list')
                ->with('testimonials', $testimonials)
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
	* get testimonial add page admin panel
	* @param void
	* @return Illuminate\View\View
	*/
    public function getAddTestimonial()
    {
        $addAccess        = UserAccessControlModel::getAccessAddByModuleAndUserMapId(14, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('testimonial.admin.add')
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
	* post testimonial add page admin panel
	* @param void
	* @return Illuminate\View\View
	*/
    public function addTestimonial(Request $request)
    {
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $officeId = Auth::user()->office_id;

        try {
            //$rules = ['testimonial_url' => 'required'];
            foreach ($languages as $language) {
                $rules['testimonial_auther_' . $language->id] = 'required';
                $rules['testimonial_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                Session::flash('class', 'alert alert-error');
                Session::flash('message', 'Some fields with * are missing.');
                return view('testimonial.admin.add')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('messages', $messages);
            } else {

                $imageFile = $request->file('attachment');
                $destinationPath = 'uploads/testimonial/';
                $rEFileTypes = "/^\.(jpg|jpeg|png){1}$/i";
                $maximum_filesize = 1 * 1024 * 1024;

                if ($imageFile) {
                    $filename = $imageFile->getClientOriginalName();
                    $extension = strrchr($filename, '.');
                    $size = $imageFile->getSize();
                    $new_image_name = "testimonial" . "_" . time();

                    if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                        $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                    } else if (preg_match($rEFileTypes, $extension) == false) {
                        Session::flash('attachment-error', 'Warning : Invalid Image File!');
                    } else if ($size > $maximum_filesize) {
                        Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                    }
                }
                $logo = isset($attachment) ? $new_image_name . $extension : NULL;

                $objectTestimonial    = new Testimonial();
                $objectTestimonial->is_active    = $request->get('is_active');
                $objectTestimonial->testimonial_attachment    = $logo;
                $objectTestimonial->created_by    = Auth::user()->id;
                $objectTestimonial->updated_by    = Auth::user()->id;
                $objectTestimonial->save();

                if ($objectTestimonial->id) {
                    foreach ($languages as $language) {
                        $insertArray[] = [
                            'testimonial_id'    => $objectTestimonial->id,
                            'language_id'    => $language->id,
                            'testimonial_auther'    => $request->get('testimonial_auther_' . $language->id),
                            'testimonial_company'    => $request->get('testimonial_company_' . $language->id),
                            'testimonial_description'    => $request->get('testimonial_description_' . $language->id),
                            'created_at'    => date("Y-m-d H:i:s"),
                            'updated_at'    => date("Y-m-d H:i:s")
                        ];
                    }
                    DB::table('testimonial_detail')->insert($insertArray);

                    Session::flash('class', 'alert alert-success');
                    Session::flash('message', 'New testimonial is added successfully.');
                    return view('testimonial.admin.add')
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name)
                        ->with('languages', $languages);
                }
            }
        } catch (Throwable $e) {
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view('testimonial.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages);
        }
    }

    /**
     * get testimonial edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditTestimonial($id)
    {

        try {
            $officeId = Auth::user()->office_id;
            $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(14, Auth::User()->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(14, Auth::User()->id);
            $objectTestimonial = Testimonial::find($id);


            if ($objectTestimonial && $editAccess) {
                $languages = Language::getActiveLanguages();
                $testimonials = Testimonial::getTestimonialDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('testimonial.admin.edit')
                    ->with('testimonials', $testimonials)
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
     * post testimonial edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editTestimonial(Request $request)
    {
        $testimonialId    = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        $testimonials = Testimonial::getTestimonialDetailsWithLanguages($testimonialId);
        $officeId = Auth::user()->office_id;
        $addAccess    = UserAccessControlModel::getAccessAddByModuleAndUserMapId(14, Auth::User()->id);

        try {

            //$rules = ['testimonial_url' => 'required'];
            foreach ($languages as $language) {
                $rules['testimonial_auther_' . $language->id] = 'required';
                $rules['testimonial_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                Session::flash('class', 'alert alert-error');
                Session::flash('message', 'Some fields are missing');
                return view('testimonial.admin.edit')
                    ->with('testimonials', $testimonials)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('addAccess', $addAccess);
            } else {

                $imageFile = $request->file('attachment');
                $destinationPath = 'uploads/testimonial/';
                $rEFileTypes = "/^\.(jpg|jpeg|png){1}$/i";
                $maximum_filesize = 1 * 1024 * 1024;

                if ($imageFile) {
                    $filename = $imageFile->getClientOriginalName();
                    $extension = strrchr($filename, '.');
                    $size = $imageFile->getSize();
                    $new_image_name = "testimonial" . "_" . time();

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

                $logo = isset($attachment) ? $new_image_name . $extension : NULL;

                $objectTestimonial    = Testimonial::find($testimonialId);
                $objectTestimonial->is_active    = $request->get('is_active');

                if ($logo) {
                    if (file_exists("uploads/testimonial/" . $objectTestimonial->testimonial_attachment) && $objectTestimonial->testimonial_attachment != '') {
                        unlink('uploads/testimonial/' . $objectTestimonial->testimonial_attachment);
                    }
                    $objectTestimonial->testimonial_attachment    = $logo;
                } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                    if (file_exists("uploads/testimonial/" . $objectTestimonial->testimonial_attachment) && $objectTestimonial->testimonial_attachment != '') {
                        unlink('uploads/testimonial/' . $objectTestimonial->testimonial_attachment);
                    }

                    $objectTestimonial->testimonial_attachment = null;
                }

                $objectTestimonial->updated_by    = Auth::user()->id;
                $objectTestimonial->save();

                if ($objectTestimonial->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);

                        $objectTestimonialDetail = Testimonialdetail::find($detailId);
                        if (count($objectTestimonialDetail) > 0) {
                            $objectTestimonialDetail->testimonial_auther    =    trim($request->get('testimonial_auther_' . $language->id));
                            $objectTestimonialDetail->testimonial_company    =    trim($request->get('testimonial_company_' . $language->id));
                            $objectTestimonialDetail->testimonial_description    =    trim($request->get('testimonial_description_' . $language->id));
                            $objectTestimonialDetail->save();
                            continue;
                        } else {
                            $insertArray[] = [
                                'testimonial_id'    => $objectTestimonial->id,
                                'language_id'    => $language->id,
                                'testimonial_auther'    => trim($request->get('testimonial_auther_' . $language->id)),
                                'testimonial_company'    => trim($request->get('testimonial_company_' . $language->id)),
                                'testimonial_description'    => trim($request->get('testimonial_description_' . $language->id)),
                                'created_at'    => date("Y-m-d H:i:s"),
                                'updated_at'    => date("Y-m-d H:i:s")
                            ];
                        }
                    }

                    if (count($insertArray) > 0) {
                        DB::table('testimonial_detail')->insert($insertArray);
                    }
                }

                $testimonials = Testimonial::getTestimonialDetailsWithLanguages($testimonialId);
                Session::flash('class', 'alert alert-success');
                Session::flash('message', 'Testimonial is successfully updated.');
                return view('testimonial.admin.edit')
                    ->with('testimonials', $testimonials)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('addAccess', $addAccess);
            }
        } catch (Throwable $e) {
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view('testimonial.admin.edit')
                ->with('testimonials', $testimonials)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages)
                ->with('addAccess', $addAccess);
        }
    }

    /*
	* testimonial delete action admin panel
	* @return JSON
	*/
    public function deleteTestimonial(Request $request)
    {
        $testimonialId = $request->get('id');
        $objectTestimonial = Testimonial::find($testimonialId);
        $objectTestimonial->is_active = '0';
        $objectTestimonial->is_deleted = '1';
        $objectTestimonial->save();

        if ($objectTestimonial->id) {
            $data['type'] = "success";
            $data['message'] = "Testimonial deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type'] = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
	* testimonial staus change action admin panel
	* @return JSON
	*/
    public function changeStatus(Request $request)
    {
        $testimonialId = $request->get('id');
        $objectTestimonial = Testimonial::find($testimonialId);
        if ($objectTestimonial->is_active == '0') {
            $objectTestimonial->is_active = '1';
            $objectTestimonial->save();
            $feedback['type'] = 'success';
            $feedback['message'] = 'The testimonial has been activated successfully';
            $feedback['icon'] = '<i class="icon-check"></i>';
        } else {
            $objectTestimonial->is_active = '0';
            $objectTestimonial->save();
            $feedback['type'] = 'success';
            $feedback['message'] = 'The testimonial has been deactivated successfully';
            $feedback['icon'] = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

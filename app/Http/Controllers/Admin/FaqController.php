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
use App\Models\Faq;
use App\Models\Faqdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class FaqController extends ConfigurationController
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
     * get faq list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(15, $authUser->id);

        if ($viewAccess) {
            $faqs = Faq::getUndeletedFaqs();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(15, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(15, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(15, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(15, $authUser->id);

            return view('faq.admin.list')
                ->with('faqs', $faqs)
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
     * get faq add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddFaq()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(15, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('faq.admin.add')
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
     * post faq add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addFaq(Request $request)
    {
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            foreach ($languages as $language) {
                $rules['faq_question_' . $language->id] = 'required';
                $rules['faq_answer_' . $language->id]   = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $objectFaq             = new Faq();
                $objectFaq->is_active  = $request->get('is_active');
                $objectFaq->created_by = Auth::user()->id;
                $objectFaq->updated_by = Auth::user()->id;
                $objectFaq->save();

                if ($objectFaq->id) {
                    foreach ($languages as $language) {
                        $insertArray[] = [
                            'faq_id'       => $objectFaq->id,
                            'language_id'  => $language->id,
                            'faq_question' => $request->get('faq_question_' . $language->id),
                            'faq_answer'   => $request->get('faq_answer_' . $language->id),
                            'created_at'   => date("Y-m-d H:i:s"),
                            'updated_at'   => date("Y-m-d H:i:s"),
                        ];
                    }
                    DB::table('faq_detail')->insert($insertArray);

                    return Redirect::route('admin.faqs')
                        ->with('type', 'success')
                        ->with('message', 'New faq is added successfully.');
                }
            }
        } catch (Throwable $e) {
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New faq is not added.');
        }
    }

    /**
     * get faq edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditFaq($id)
    {

        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(15, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(15, Auth::User()->id);
            $objectFaq  = Faq::find($id);

            if ($objectFaq && $editAccess) {
                $languages = Language::getActiveLanguages();
                $faqs      = Faq::getFaqDetailsWithLanguages($id);

                return view('faq.admin.edit')
                    ->with('faqs', $faqs)
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
     * post faq edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editFaq(Request $request)
    {
        $faqId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        $faqs      = Faq::getFaqDetailsWithLanguages($faqId);
        $officeId  = Auth::user()->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(15, Auth::User()->id);

        try {
            foreach ($languages as $language) {
                $rules['faq_question_' . $language->id] = 'required';
                $rules['faq_answer_' . $language->id]   = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $objectFaq             = Faq::find($faqId);
                $objectFaq->is_active  = $request->get('is_active');
                $objectFaq->updated_by = Auth::user()->id;
                $objectFaq->save();

                if ($objectFaq->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);

                        $objectFaqDetail = Faqdetail::find($detailId);
                        if ($objectFaqDetail) {
                            $objectFaqDetail->faq_question = trim($request->get('faq_question_' . $language->id));
                            $objectFaqDetail->faq_answer   = trim($request->get('faq_answer_' . $language->id));
                            $objectFaqDetail->save();
                            continue;
                        } else {
                            $insertArray[] = [
                                'faq_id'       => $objectFaq->id,
                                'language_id'  => $language->id,
                                'faq_question' => trim($request->get('faq_question_' . $language->id)),
                                'faq_answer'   => trim($request->get('faq_answer_' . $language->id)),
                                'created_at'   => date("Y-m-d H:i:s"),
                                'updated_at'   => date("Y-m-d H:i:s"),
                            ];
                        }
                    }

                    if (count($insertArray) > 0) {
                        DB::table('faq_detail')->insert($insertArray);
                    }
                }
                return Redirect::route('admin.faqs')
                    ->with('type', 'success')
                    ->with('message', 'FAQ is updated successfully.');
            }
        } catch (Throwable $e) {
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'FAQ is not updated.');
        }
    }

    /*
     * faq delete action admin panel
     * @return JSON
     */
    public function deleteFaq(Request $request)
    {
        $faqId                 = $request->get('id');
        $objectFaq             = Faq::find($faqId);
        $objectFaq->is_active  = '0';
        $objectFaq->is_deleted = '1';
        $objectFaq->save();

        if ($objectFaq->id) {
            $data['type']    = "success";
            $data['message'] = "Faq deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * faq staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $faqId     = $request->get('id');
        $objectFaq = Faq::find($faqId);
        if ($objectFaq->is_active == '0') {
            $objectFaq->is_active = '1';
            $objectFaq->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The faq has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectFaq->is_active = '0';
            $objectFaq->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The faq has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

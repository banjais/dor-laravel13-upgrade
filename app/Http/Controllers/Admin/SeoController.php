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
use App\Models\Office;
use App\Models\Seo;
use App\Models\Seodetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SeoController extends ConfigurationController
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
     * get seo list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(19, $authUser->id);

        if ($viewAccess) {
            $seos = Seo::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->get();
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(19, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(19, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(19, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(19, $authUser->id);

            return view('seo.admin.list')
                ->with('seos', $seos)
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
     * get seo add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddSeo()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(19, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('seo.admin.add')
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
     * post seo add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addSeo(Request $request)
    {
        $languages = Language::getActiveLanguages();
        $officeId = Auth::user()->office_id;

        try {
            $rules = ['page_name' => 'required'];
            $rules = ['meta_tags' => 'required'];
            $rules = ['meta_description' => 'required'];
            foreach ($languages as $language) {
                $rules['page_title_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                Session::flash('class', 'alert alert-error');
                Session::flash('message', 'Some fields with * are missing.');
                return view('seo.admin.add')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('messages', $messages);
            } else {
                $offices = Office::getActiveOfficesIds();

                for ($i = 0; $i < count($offices); $i++) {
                    $objectSeo                   = new Seo();
                    $objectSeo->office_id        = $offices[$i]->id;
                    $objectSeo->page_name        = $request->get('page_name');
                    $objectSeo->meta_tags        = $request->get('meta_tags');
                    $objectSeo->meta_description = $request->get('meta_description');
                    $objectSeo->is_active        = 1;
                    $objectSeo->created_by       = Auth::user()->id;
                    $objectSeo->updated_by       = Auth::user()->id;
                    $objectSeo->save();

                    if ($objectSeo->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'seo_id'      => $objectSeo->id,
                                'language_id' => $language->id,
                                'page_title'  => $request->get('page_title_' . $language->id),
                                'created_at'  => date("Y-m-d H:i:s"),
                                'updated_at'  => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('seo_detail')->insert($insertArray);
                    }
                }
                Session::flash('class', 'alert alert-success');
                Session::flash('message', 'New seo is added successfully.');
                return view('seo.admin.add')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages);
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view('seo.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages);
        }
    }

    /**
     * get seo edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditSeo($id)
    {
        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(19, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(19, Auth::User()->id);
            $objectSeo  = Seo::find($id);

            if ($objectSeo && $editAccess) {
                $languages = Language::getActiveLanguages();
                $seos      = Seo::getSeoDetailsWithLanguages($id);

                return view('seo.admin.edit')
                    ->with('seos', $seos)
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
                ->with('rel_.te_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * post seo edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editSeo(Request $request)
    {
        $seoId = $request->get('id');
        $languages = Language::getActiveLanguages();
        $seos      = Seo::getSeoDetailsWithLanguages($seoId);
        $officeId  = Auth::user()->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(19, Auth::User()->id);

        try {
            $rules = ['page_name' => 'required'];
            $rules = ['meta_tags' => 'required'];
            $rules = ['meta_description' => 'required'];
            foreach ($languages as $language) {
                $rules['page_title_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $objectSeo                   = Seo::find($seoId);
                $objectSeo->page_name        = $request->get('page_name');
                $objectSeo->meta_tags        = $request->get('meta_tags');
                $objectSeo->meta_description = $request->get('meta_description');
                $objectSeo->is_active        = 1;
                $objectSeo->updated_by       = Auth::user()->id;
                $objectSeo->save();

                if ($objectSeo->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);

                        $objectSeoDetail = Seodetail::find($detailId);
                        if ($objectSeoDetail) {
                            $objectSeoDetail->page_title = trim($request->get('page_title_' . $language->id));
                            $objectSeoDetail->save();
                            continue;
                        } else {
                            $insertArray[] = [
                                'seo_id'      => $objectSeo->id,
                                'language_id' => $language->id,
                                'page_title'  => trim($request->get('page_title_' . $language->id)),
                                'created_at'  => date("Y-m-d H:i:s"),
                                'updated_at'  => date("Y-m-d H:i:s"),
                            ];
                        }
                    }

                    if (count($insertArray) > 0) {
                        DB::table('seo_detail')->insert($insertArray);
                    }
                }
                return Redirect::route('admin.seos')
                    ->with('type', 'success')
                    ->with('message', 'Seo is updated successfully.');
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                . with('message', 'Seo is not updated.');
        }
    }

    /*
     * seo delete action admin panel
     * @return JSON
     */
    public function deleteSeo(Request $request)
    {
        $seoId                 = $request->get('id');
        $objectSeo             = Seo::find($seoId);
        $objectSeo->is_active  = '0';
        $objectSeo->is_deleted = '1';
        $objectSeo->save();

        if ($objectSeo->id) {
            $data['type']    = "success";
            $data['message'] = "Seo deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * seo staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $seoId     = $request->get('id');
        $objectSeo = Seo::find($seoId);
        if ($objectSeo->is_active == '0') {
            $objectSeo->is_active = '1';
            $objectSeo->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The seo has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectSeo->is_active = '0';
            $objectSeo->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The seo has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

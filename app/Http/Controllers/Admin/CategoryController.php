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
use App\Models\Category;
use App\Models\Categorydetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends ConfigurationController
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
     * get category list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(11, $authUser->id);

        if ($viewAccess) {
            $categories = Category::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', 4)
                ->where('is_deleted', '0')
                ->orderBy('category_url', 'asc')
                ->get();

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(11, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(11, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(11, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(11, $authUser->id);

            return view('publication.admin.category-list')
                ->with('categories', $categories)
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
     * get category add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddCategory()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(11, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('publication.admin.category-add')
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
     * post category add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addCategory(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['category_url' => 'required|unique:publication_category,category_url'];
            foreach ($languages as $language) {
                $rules['category_heading_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $categoryUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('category_url')))), '-');
                $categoryExist = Category::checkExist($categoryUrl, $authUser->office_id);
                if ($categoryExist) {
                    $message = 'category with url <b>' . $categoryUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {

                    $objectCategory                            = new Category();
                    $objectCategory->category_url              = $categoryUrl;
                    $objectCategory->category_meta_tags        = $request->get('category_meta_tags');
                    $objectCategory->category_meta_description = $request->get('category_meta_description');
                    $objectCategory->is_active                 = $request->get('is_active');
                    $objectCategory->created_by                = $authUser->id;
                    $objectCategory->updated_by                = $authUser->id;
                    $objectCategory->office_id                 = $authUser->office_id;
                    $objectCategory->save();

                    if ($objectCategory->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'category_id'         => $objectCategory->id,
                                'language_id'         => $language->id,
                                'category_heading'    => $request->get('category_heading_' . $language->id),
                                'category_page_title' => $request->get('category_page_title_' . $language->id),
                                'created_at'          => date("Y-m-d H:i:s"),
                                'updated_at'          => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('publication_category_detail')->insert($insertArray);

                        return Redirect::route('admin.categories')
                            ->with('type', 'success')
                            ->with('message', 'New category is added successfully.');
                    }
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New category is not added.');
        }
    }

    /**
     * get category edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditCategory($id)
    {

        try {
            $officeId       = Auth::user()->office_id;
            $addAccess      = UserAccessControlModel::getAccessAddByModuleAndUserMapId(11, Auth::User()->id);
            $editAccess     = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(11, Auth::User()->id);
            $objectCategory = Category::find($id);

            if ($objectCategory->office_id == $officeId && $editAccess) {
                $languages  = Language::getActiveLanguages();
                $categories = Category::getCategoryDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('publication.admin.category-edit')
                    ->with('categories', $categories)
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
     * post category edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editCategory(Request $request)
    {
        $categoryId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages  = Language::getActiveLanguages();
        $categories = Category::getCategoryDetailsWithLanguages($categoryId);
        $officeId   = Auth::user()->office_id;
        $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(11, Auth::User()->id);

        try {

            $rules = ['category_url' => 'required|unique:publication_category,category_url,' . $categoryId];
            foreach ($languages as $language) {
                $rules['category_heading_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $categoryUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('category_url')))), '-');
                $categoryExist = Category::checkExist($categoryUrl, $officeId);

                if ($categoryExist && $categoryExist != $categoryId) {
                    $message = 'Category with url <b>' . $categoryUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {

                    $objectCategory                            = Category::find($categoryId);
                    $objectCategory->category_url              = $categoryUrl;
                    $objectCategory->category_meta_tags        = $request->get('category_meta_tags');
                    $objectCategory->category_meta_description = $request->get('category_meta_description');
                    $objectCategory->is_active                 = $request->get('is_active');
                    $objectCategory->updated_by                = Auth::user()->id;
                    $objectCategory->save();

                    if ($objectCategory->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId = $request->get('detail_id_' . $language->id);

                            $objectCategoryDetail = Categorydetail::find($detailId);
                            if ($objectCategoryDetail) {
                                $objectCategoryDetail->category_heading    = trim($request->get('category_heading_' . $language->id));
                                $objectCategoryDetail->category_page_title = trim($request->get('category_page_title_' . $language->id));
                                $objectCategoryDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'category_id'                => $objectCategory->id,
                                    'language_id'                => $language->id,
                                    'category_heading'           => trim($request->get('category_heading_' . $language->id)),
                                    // 'category_description'       => trim($request->get('category_description_' . $language->id)),
                                    'category_page_title'        => trim($request->get('category_page_title_' . $language->id)),
                                    'created_at'                 => date("Y-m-d H:i:s"),
                                    'updated_at'                 => date("Y-m-d H:i:s"),
                                ];
                            }
                        }

                        if (count($insertArray) > 0) {
                            DB::table('publication_category_detail')->insert($insertArray);
                        }
                    }
                    return Redirect::route('admin.categories')
                        ->with('type', 'success')
                        ->with('message', 'Category is updated successfully.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Category is not updated.');
        }
    }

    /*
     * category delete action admin panel
     * @return JSON
     */
    public function deleteCategory(Request $request)
    {
        $categoryId                 = $request->get('id');
        $objectCategory             = Category::find($categoryId);
        $objectCategory->is_active  = '0';
        $objectCategory->is_deleted = '1';
        $objectCategory->save();

        if ($objectCategory->id) {
            $data['type']    = "success";
            $data['message'] = "Category deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * category staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $categoryId     = $request->get('id');
        $objectCategory = Category::find($categoryId);
        if ($objectCategory->is_active == '0') {
            $objectCategory->is_active = '1';
            $objectCategory->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The category has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectCategory->is_active = '0';
            $objectCategory->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The category has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

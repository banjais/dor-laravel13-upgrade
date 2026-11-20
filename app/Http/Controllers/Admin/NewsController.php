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
use App\Models\News;
use App\Models\Newsdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class NewsController extends ConfigurationController
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
     * get news list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(2, $authUser->id);

        if ($viewAccess) {
            $q = News::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id);

            // if ($authUser->office_id != 4) {
            //     $q = $q->where(['is_deleted' => '0', 'is_active' => 1]);
            // }

            $newss = $q->orderBy('created_at', 'desc')
                ->paginate(20);

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(2, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(2, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(2, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(2, $authUser->id);

            return view('news.admin.list')
                ->with('newss', $newss)
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
     * get news add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddNews()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(2, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('news.admin.add')
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
     * post news add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addNews(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['news_url' => 'required'];
            $rules['published_date'] = 'required';
            foreach ($languages as $language) {
                $rules['news_heading_' . $language->id]     = 'required';
                $rules['news_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $newsUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('news_url')))), '-');
                $newsExist = News::checkExist($newsUrl, $authUser->office_id);
                if ($newsExist) {
                    $message = 'news with url <b>' . $newsUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/news/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|ppt|pptx|doc|docx){1}$/i";
                    $maximum_filesize = 1 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "news" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectNews                        = new News();
                    $objectNews->news_url              = $newsUrl;
                    $objectNews->news_meta_tags        = $request->get('news_meta_tags');
                    $objectNews->news_meta_description = $request->get('news_meta_description');
                    $objectNews->published_date        = $request->get('published_date');
                    $objectNews->is_active             = $request->get('is_active');
                    $objectNews->news_attachment       = $logo;
                    if ($logo) {
                        $objectNews->news_attachment = $logo;
                    }
                    $objectNews->created_by = $authUser->id;
                    $objectNews->updated_by = $authUser->id;
                    $objectNews->office_id  = $authUser->office_id;
                    $objectNews->download_count = 0;
                    $objectNews->save();

                    if ($objectNews->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'news_id'                => $objectNews->id,
                                'language_id'            => $language->id,
                                'news_heading'           => $request->get('news_heading_' . $language->id),
                                'news_description'       => $request->get('news_description_' . $language->id),
                                'news_page_title'        => $request->get('news_page_title_' . $language->id),
                                'created_at'             => date("Y-m-d H:i:s"),
                                'updated_at'             => date("Y-m-d H:i:s")
                            ];
                        }
                        DB::table('news_detail')->insert($insertArray);
                    }

                    return Redirect::route('admin.news')
                        ->with('type', 'success')
                        ->with('message', 'News is added successfully.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'News not added.');
        }
    }

    /**
     * get news edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditNews($id)
    {

        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(2, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(2, Auth::User()->id);
            $newsData   = News::find($id);

            if ($newsData->office_id == $officeId && $editAccess) {
                $languages = Language::getActiveLanguages();
                $newss     = News::getNewsDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('news.admin.edit')
                    ->with('newss', $newss)
                    ->with('newsData', $newsData)
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
     * post news edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editNews(Request $request)
    {
        $newsId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        $newss     = News::getNewsDetailsWithLanguages($newsId);
        $officeId  = Auth::user()->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(2, Auth::User()->id);
        $newsData  = News::find($newsId);

        try {
            $rules = ['news_url' => 'required'];
            $rules['published_date'] = 'required';
            foreach ($languages as $language) {
                $rules['news_heading_' . $language->id]     = 'required';
                $rules['news_description_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $newsUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('news_url')))), '-');
                $newsExist = News::checkExist($newsUrl, $officeId);

                if ($newsExist && $newsExist != $newsId) {
                    $message = 'News with url <b>' . $newsUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/news/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|ppt|pptx|doc|docx){1}$/i";
                    $maximum_filesize = 1 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "news" . "_" . time();

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

                    $objectNews                        = News::find($newsId);
                    $objectNews->news_url              = $newsUrl;
                    $objectNews->news_meta_tags        = $request->get('news_meta_tags');
                    $objectNews->news_meta_description = $request->get('news_meta_description');
                    $objectNews->published_date        = $request->get('published_date');
                    $objectNews->is_active             = $request->get('is_active');

                    if ($logo) {
                        $objectNews->news_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        $objectNews->news_attachment = null;
                    }

                    $objectNews->updated_by = Auth::user()->id;
                    $objectNews->save();

                    if ($objectNews->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId = $request->get('detail_id_' . $language->id);

                            $objectNewsDetail = Newsdetail::find($detailId);
                            if ($objectNewsDetail) {
                                $objectNewsDetail->news_heading           = trim($request->get('news_heading_' . $language->id));
                                $objectNewsDetail->news_description       = trim($request->get('news_description_' . $language->id));
                                $objectNewsDetail->news_page_title        = trim($request->get('news_page_title_' . $language->id));
                                $objectNewsDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'news_id'                => $objectNews->id,
                                    'language_id'            => $language->id,
                                    'news_heading'           => trim($request->get('news_heading_' . $language->id)),
                                    'news_description'       => trim($request->get('news_description_' . $language->id)),
                                    'news_page_title'        => trim($request->get('news_page_title_' . $language->id)),
                                    'created_at'             => date("Y-m-d H:i:s"),
                                    'updated_at'             => date("Y-m-d H:i:s"),
                                ];
                            }
                        }

                        if (count($insertArray) > 0) {
                            DB::table('news_detail')->insert($insertArray);
                        }
                    }
                    return Redirect::route('admin.news')
                        ->with('type', 'success')
                        ->with('message', 'News is successfully updated.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'News is not updated.');
        }
    }

    /*
     * news delete action admin panel
     * @return JSON
     */
    public function deleteNews(Request $request)
    {
        $newsId                 = $request->get('id');
        $objectNews             = News::find($newsId);
        $objectNews->is_active  = '0';
        $objectNews->is_deleted = '1';
        $objectNews->save();

        if ($objectNews->id) {
            $data['type']    = "success";
            $data['message'] = "News deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * news staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $newsId     = $request->get('id');
        $objectNews = News::find($newsId);
        if ($objectNews->is_active == '0') {
            $objectNews->is_active = '1';
            $objectNews->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The news has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectNews->is_active = '0';
            $objectNews->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The news has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

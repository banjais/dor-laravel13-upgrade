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
use App\Models\Publication;
use App\Models\Publicationdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PublicationController extends ConfigurationController
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
     * get publication list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(12, $authUser->id);
        // dd($viewAccess);

        if ($viewAccess) {
            $publications = Publication::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderBy('created_at', 'desc')
                ->get();

            // dd($publications);

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(12, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(12, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(12, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(12, $authUser->id);

            return view('publication.admin.publication-list')
                ->with('publications', $publications)
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
     * get publication add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddPublication()
    {
        $authUser = Auth::user();
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(12, $authUser->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            $categories = Category::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', 4)
                ->where('is_deleted', '0')
                ->get();

            return view('publication.admin.publication-add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages)
                ->with('categories', $categories);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * post publication add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addPublication(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            $rules = ['publication_url' => 'required'];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,ppt,pptx|max:133120';
            foreach ($languages as $language) {
                $rules['publication_heading_' . $language->id]     = 'required';
                $rules['publication_description_' . $language->id] = 'required';
            }

            $messages = [
                'attachment.max'   => 'Maximum attachment size is 130 MB.',
                'attachment.mimes' => 'Invalid file format. Please upload JPG, JPEG, PDF, PPT or PPTX only.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                if ($validator->errors()->has('attachment')) {
                    return Redirect::back()->withInput()
                        ->with('attachment-error', $validator->errors()->first('attachment'));
                }

                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $publicationUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('publication_url')))), '-');
                $publicationExist = Publication::checkExist($publicationUrl, $authUser->office_id);
                if ($publicationExist) {
                    $message = 'publication with url <b>' . $publicationUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/publication/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|ppt|pptx)$/i";
                    $maximum_filesize = 130 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "publication" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Invalid file format. Please upload JPG, JPEG, PDF, PPT or PPTX only.');
                        } else if ($size > $maximum_filesize) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Maximum attachment size is 130 MB.');
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectPublication                               = new Publication();
                    $objectPublication->publication_url              = $publicationUrl;
                    $objectPublication->publication_meta_tags        = $request->get('publication_meta_tags');
                    $objectPublication->publication_meta_description = $request->get('publication_meta_description');
                    $objectPublication->category_id                  = $request->get('category_id');
                    $objectPublication->published_date               = $request->get('published_date');
                    $objectPublication->is_active                    = $request->get('is_active');
                    $objectPublication->publication_attachment       = $logo;
                    $objectPublication->created_by                   = $authUser->id;
                    $objectPublication->updated_by                   = $authUser->id;
                    $objectPublication->office_id                    = $authUser->office_id;
                    $objectPublication->download_count               = 0;
                    $objectPublication->save();

                    if ($objectPublication->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'publication_id'                => $objectPublication->id,
                                'language_id'                   => $language->id,
                                'publication_heading'           => trim($request->get('publication_heading_' . $language->id)),
                                'publication_description'       => trim($request->get('publication_description_' . $language->id)),
                                'publication_page_title'        => trim($request->get('publication_page_title_' . $language->id)),
                                'created_at'                    => date("Y-m-d H:i:s"),
                                'updated_at'                    => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('publication_detail')->insert($insertArray);
                    }

                    return Redirect::route('admin.publications')
                        ->with('type', 'success')
                        ->with('message', 'New publication is added successfully.');
                }
            }
        } catch (Throwable $e) {
            report($e);
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New publication is not added.');
        }
    }

    /**
     * get publication edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditPublication($id)
    {

        try {
            $officeId        = Auth::user()->office_id;
            $addAccess       = UserAccessControlModel::getAccessAddByModuleAndUserMapId(12, Auth::User()->id);
            $editAccess      = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(12, Auth::User()->id);
            $publicationData = Publication::find($id);

            if ($publicationData->office_id == $officeId && $editAccess) {
                $languages    = Language::getActiveLanguages();
                $publications = Publication::getPublicationDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('publication.admin.publication-edit')
                    ->with('publications', $publications)
                    ->with('publicationData', $publicationData)
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
     * post publication edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editPublication(Request $request)
    {
        $publicationId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages       = Language::getActiveLanguages();
        $publications    = Publication::getPublicationDetailsWithLanguages($publicationId);
        $officeId        = Auth::user()->office_id;
        $addAccess       = UserAccessControlModel::getAccessAddByModuleAndUserMapId(12, Auth::User()->id);
        $publicationData = Publication::find($publicationId);

        try {
            $rules = ['publication_url' => 'required|unique:publication,publication_url,' . $publicationId];
            $rules['published_date'] = 'required';
            $rules['attachment'] = 'mimes:jpg,jpeg,pdf,ppt,pptx|max:133120';
            foreach ($languages as $language) {
                $rules['publication_heading_' . $language->id]     = 'required';
                $rules['publication_description_' . $language->id] = 'required';
            }

            $messages = [
                'attachment.max'   => 'Maximum attachment size is 130 MB.',
                'attachment.mimes' => 'Invalid file format. Please upload JPG, JPEG, PDF, PPT or PPTX only.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                if ($validator->errors()->has('attachment')) {
                    return Redirect::back()->withInput()
                        ->with('attachment-error', $validator->errors()->first('attachment'));
                }

                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $publicationUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('publication_url')))), '-');
                $publicationExist = Publication::checkExist($publicationUrl, $officeId);

                if ($publicationExist && $publicationExist != $publicationId) {
                    $message = 'Publication with url <b>' . $publicationUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');
                    $destinationPath  = 'uploads/publication/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|pdf|ppt|pptx)$/i";
                    $maximum_filesize = 130 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "publication" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Invalid file format. Please upload JPG, JPEG, PDF, PPT or PPTX only.');
                        } else if ($size > $maximum_filesize) {
                            return Redirect::back()->withInput()
                                ->with('attachment-error', 'Maximum attachment size is 130 MB.');
                        }
                    } else {
                        $attachment_deleted = $request->get('attachment_deleted');
                    }

                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectPublication                               = Publication::find($publicationId);
                    $objectPublication->publication_url              = $publicationUrl;
                    $objectPublication->publication_meta_tags        = $request->get('publication_meta_tags');
                    $objectPublication->publication_meta_description = $request->get('publication_meta_description');
                    $objectPublication->category_id                  = $request->get('category_id');
                    $objectPublication->published_date               = $request->get('published_date');
                    $objectPublication->is_active                    = $request->get('is_active');

                    $oldAttachment = $objectPublication->publication_attachment;

                    if ($logo) {
                        $objectPublication->publication_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        $objectPublication->publication_attachment = null;
                    }

                    $objectPublication->updated_by = Auth::user()->id;
                    $objectPublication->save();

                    if (($logo || (isset($attachment_deleted) && $attachment_deleted == '1')) && $oldAttachment && $oldAttachment !== $objectPublication->publication_attachment) {
                        $oldAttachmentPath = 'uploads/publication/' . $oldAttachment;
                        if (is_file($oldAttachmentPath)) {
                            unlink($oldAttachmentPath);
                        }
                    }

                    if ($objectPublication->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId = $request->get('detail_id_' . $language->id);

                            $objectPublicationDetail = Publicationdetail::find($detailId);
                            if ($objectPublicationDetail) {
                                $objectPublicationDetail->publication_heading           = trim($request->get('publication_heading_' . $language->id));
                                $objectPublicationDetail->publication_description       = trim($request->get('publication_description_' . $language->id));
                                $objectPublicationDetail->publication_page_title        = trim($request->get('publication_page_title_' . $language->id));
                                $objectPublicationDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'publication_id'                => $objectPublication->id,
                                    'language_id'                   => $language->id,
                                    'publication_heading'           => trim($request->get('publication_heading_' . $language->id)),
                                    'publication_description'       => trim($request->get('publication_description_' . $language->id)),
                                    'publication_page_title'        => trim($request->get('publication_page_title_' . $language->id)),
                                    'created_at'                    => date("Y-m-d H:i:s"),
                                    'updated_at'                    => date("Y-m-d H:i:s"),
                                ];
                            }
                        }

                        if (count($insertArray) > 0) {
                            DB::table('publication_detail')->insert($insertArray);
                        }
                    }
                    return Redirect::route('admin.publications')
                        ->with('type', 'success')
                        ->with('message', 'Publication is updated successfully.');
                }
            }
        } catch (Throwable $e) {
            report($e);
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Publication is not updated.');
        }
    }

    /*
     * publication delete action admin panel
     * @return JSON
     */
    public function deletePublication(Request $request)
    {
        $publicationId                 = $request->get('id');
        $objectPublication             = Publication::find($publicationId);
        $objectPublication->is_active  = '0';
        $objectPublication->is_deleted = '1';
        $objectPublication->save();

        if ($objectPublication->id) {
            $data['type']    = "success";
            $data['message'] = "Publication deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * publication staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $publicationId     = $request->get('id');
        $objectPublication = Publication::find($publicationId);
        if ($objectPublication->is_active == '0') {
            $objectPublication->is_active = '1';
            $objectPublication->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The publication has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectPublication->is_active = '0';
            $objectPublication->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The publication has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

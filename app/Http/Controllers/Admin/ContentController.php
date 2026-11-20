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
use App\Models\Content;
use App\Models\ContentDetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ContentController extends ConfigurationController
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
     * get content list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(1, $authUser->id);

        if ($viewAccess) {
            $contents = Content::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', '0')
                ->orderBy('created_at', 'desc')
                ->get();

            // dd($contents);

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(1, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(1, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(1, $authUser->id);

            return view('content.admin.list')
                ->with('contents', $contents)
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
     * get content add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddContent()
    {
        $authUser = Auth::user();
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, $authUser->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('content.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('officeId', $authUser->office_id)
                ->with('languages', $languages);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * post content add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addContent(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            // $rules = ['content_url' => 'required|unique:content,content_url']; commented by sandeep as content url can be same for all contacts and links url
            $rules = ['content_url' => 'required'];
            foreach ($languages as $language) {
                $rules['content_heading_' . $language->id]     = 'required';
                $rules['content_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $contentUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('content_url')))), '-');
                $contentExist = Content::checkExist($contentUrl, $authUser->office_id);
                if ($contentExist) {
                    $message = 'content with url <b>' . $contentUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');

                    $docFiles         = $request->file('file_attachment');
                    if ($docFiles) {
                        $docFiles         = array_filter($docFiles);
                    }

                    $docName          = $request->get('file_name');
                    $destinationPath  = 'uploads/pages/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|gif|png|pdf|xls|doc|docx){1}$/i";
                    $maximum_filesize = 100 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "pages" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 10 MB!");
                        }
                    }
                    if ($docFiles) {
                        foreach ($docFiles as $docFile) {
                            # code...

                            $filename     = $docFile->getClientOriginalName();
                            $extension    = strrchr($filename, '.');
                            $size         = $docFile->getSize();
                            $new_doc_name = "Documents" . "_" . time();

                            if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                                $docattachment = $docFile->move($destinationPath, $new_doc_name . $extension);
                            } else if (preg_match($rEFileTypes, $extension) == false) {
                                Session::flash('attachment-error', 'Warning : Invalid Image File!');
                            } else if ($size > $maximum_filesize) {
                                Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 10 MB!");
                            }
                            $docs[] = isset($docattachment) ? $new_doc_name . $extension : null;
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectContent                           = new Content();
                    $objectContent->content_url              = $contentUrl;
                    $objectContent->parent_id                = $request->get('parent_id');
                    $objectContent->content_meta_tags        = $request->get('content_meta_tags');
                    $objectContent->content_meta_description = $request->get('content_meta_description');
                    $objectContent->is_active                = $request->get('is_active');
                    if ($logo) {
                        $objectContent->content_attachment = $logo;
                    }
                    if ($request->get('show_in') != '') {
                        $objectContent->show_in = join(',', $request->get('show_in'));
                    }
                    $objectContent->created_by = $authUser->id;
                    $objectContent->updated_by = $authUser->id;
                    $objectContent->office_id  = $authUser->office_id;
                    $objectContent->order_menu = $request->get('order_menu');
                    $objectContent->save();

                    if ($objectContent->id) {
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'content_id'                => $objectContent->id,
                                'language_id'               => $language->id,
                                'content_heading'           => $request->get('content_heading_' . $language->id),
                                'content_description'       => $request->get('content_description_' . $language->id),
                                'content_page_title'        => $request->get('content_page_title_' . $language->id),
                                'created_at'                => date("Y-m-d H:i:s"),
                                'updated_at'                => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('content_detail')->insert($insertArray);
                        // insert to content documents
                        if (isset($docs) && count(array_filter($docs)) > 0) {
                            foreach ($docs as $id => $doc) {
                                $dinsertArray[] = [
                                    'content_id'          => $objectContent->id,
                                    'document_attachment' => $doc,
                                    'document_name'       => $docName[$id],
                                    'is_active'           => '1',
                                ];
                            }
                            DB::table('content_documents')->insert($dinsertArray);
                        }

                        return Redirect::route('admin.contents')
                            ->with('type', 'success')
                            ->with('message', 'New content is added successfully.');
                    }
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New content is not added.');
        }
    }

    /**
     * get content edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditContent($id)
    {
        $authUser = Auth::user();
        try {
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(1, Auth::User()->id);
            $objectContent = Content::find($id);

            if ($objectContent && $editAccess) {
                if ($objectContent->office_id == $authUser->office_id) {
                    $languages = Language::getActiveLanguages();
                    $contents  = Content::getContentDetailsWithLanguages($id);
                    return view('content.admin.edit')
                        ->with('contents', $contents)
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name)
                        ->with('officeId', $authUser->office_id)
                        ->with('languages', $languages)
                        ->with('addAccess', $addAccess);
                } else {
                    return view('error')
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name);
                }
            } else {
                return view('error')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name);
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * post content edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editContent(Request $request)
    {
        $authUser = Auth::user();
        $contentId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        $contents  = Content::getContentDetailsWithLanguages($contentId);
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);

        try {
            // $rules = ['content_url' => 'required|unique:content,content_url,'.$contentId]; commented by sandeep
            $rules = ['content_url' => 'required'];
            foreach ($languages as $language) {
                $rules['content_heading_' . $language->id]     = 'required';
                $rules['content_description_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $contentUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('content_url')))), '-');
                $contentExist = Content::checkExist($contentUrl, $authUser->office_id);

                if ($contentExist && $contentExist != $contentId) {
                    $message = 'Content with url <b>' . $contentUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile        = $request->file('attachment');

                    $docFiles         = $request->file('file_attachment');
                    // dd($docFiles);
                    if ($docFiles) {
                        $docFiles         = array_filter($docFiles);
                    }

                    $docName          = $request->get('file_name');
                    $destinationPath  = 'uploads/pages/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|gif|png|pdf){1}$/i";
                    $maximum_filesize = 100 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "pages" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 10 MB!");
                        }
                    } else {
                        $attachment_deleted = $request->get('attachment_deleted');
                    }

                    if ($docFiles) {
                        foreach ($docFiles as $docFile) {

                            $filename     = $docFile->getClientOriginalName();
                            $extension    = strrchr($filename, '.');
                            $size         = $docFile->getSize();
                            $new_doc_name = "Documents" . "_" . time();

                            if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                                $docattachment = $docFile->move($destinationPath, $new_doc_name . $extension);
                            } else if (preg_match($rEFileTypes, $extension) == false) {
                                Session::flash('attachment-error', 'Warning : Invalid Image File!');
                            } else if ($size > $maximum_filesize) {
                                Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 10 MB!");
                            }
                        }
                    }
                    $docs[] = isset($docattachment) ? $new_doc_name . $extension : null;

                    $logo   = isset($attachment) ? $new_image_name . $extension : null;

                    $objectContent                           = Content::find($contentId);
                    $objectContent->content_url              = $contentUrl;
                    $objectContent->parent_id                = $request->get('parent_id');
                    $objectContent->content_meta_tags        = $request->get('content_meta_tags');
                    $objectContent->content_meta_description = $request->get('content_meta_description');
                    $objectContent->is_active                = $request->get('is_active');
                    $objectContent->order_menu               = $request->get('order_menu');

                    if ($logo) {
                        $objectContent->content_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        $objectContent->content_attachment = null;
                    }

                    if ($request->get('show_in') != '') {
                        $objectContent->show_in = join(',', $request->get('show_in'));
                    } else {
                        $objectContent->show_in = '';
                    }
                    $objectContent->updated_by = $authUser->id;
                    $objectContent->save();

                    if ($objectContent->id) {
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId            = $request->get('detail_id_' . $language->id);
                            $objectContentDetail = ContentDetail::find($detailId);
                            if ($objectContentDetail) {
                                $objectContentDetail->content_heading           = trim($request->get('content_heading_' . $language->id));
                                $objectContentDetail->content_description       = trim($request->get('content_description_' . $language->id));
                                $objectContentDetail->content_page_title        = trim($request->get('content_page_title_' . $language->id));
                                $objectContentDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'content_id'                => $objectContent->id,
                                    'language_id'               => $language->id,
                                    'content_heading'           => trim($request->get('content_heading_' . $language->id)),
                                    'content_description'       => trim($request->get('content_description_' . $language->id)),
                                    'content_page_title'        => trim($request->get('content_page_title_' . $language->id)),
                                    'created_at'                => date("Y-m-d H:i:s"),
                                    'updated_at'                => date("Y-m-d H:i:s"),
                                ];
                            }
                        }
                        // dd();
                        if (count($insertArray) > 0) {
                            DB::table('content_detail')->insert($insertArray);
                        }
                        // print_r(count(array_filter($docs)));exit;
                        // insert to content documents
                        if (count(array_filter($docs)) > 0) {
                            foreach ($docs as $id => $doc) {
                                $dinsertArray[] = [
                                    'content_id'          => $objectContent->id,
                                    'document_attachment' => $doc,
                                    'document_name'       => $docName[$id],
                                    'is_active'           => '1',
                                ];
                            }
                            DB::table('content_documents')->insert($dinsertArray);
                        }
                    }
                    return Redirect::route('admin.contents')
                        ->with('type', 'success')
                        ->with('message', 'Content is updated successfully.');
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Content is not updated.');
        }
    }

    /*
     * content delete action admin panel
     * @return JSON
     */
    public function deleteContent(Request $request)
    {
        $contentId                 = $request->get('id');
        $objectContent             = Content::find($contentId);
        $objectContent->is_active  = '0';
        $objectContent->is_deleted = '1';
        $objectContent->save();

        if ($objectContent->id) {
            $data['type']    = "success";
            $data['message'] = "Content deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }
    /*
     * content delete action admin panel
     * @return JSON
     */
    public function deleteContentDoc(Request $request)
    {
        $contentId     = $request->get('id');
        $objectContent = DB::table('content_documents')
            ->where('id', $contentId)
            ->update(['is_active' => '0']);

        if ($objectContent) {
            $data['type']    = "success";
            $data['message'] = "Content deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * content staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $contentId     = $request->get('id');
        $objectContent = Content::find($contentId);
        if ($objectContent->is_active == '0') {
            $objectContent->is_active = '1';
            $objectContent->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The content has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectContent->is_active = '0';
            $objectContent->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The content has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

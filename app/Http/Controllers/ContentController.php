<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Throwable;

class ContentController extends Controller
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;
    public $site_link;
    public $officeId;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name = ConfigurationController::site_name();
    }

    /*
	* get content page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getContentDetail($subdomain, $url)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);

        //fix for showing same external pages links as main home page
        if ($subdomain != 'home' && $url == 'links') {
            $officeId = 4;
        }

        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $contentData = Content::getContentDetail($officeId, $url, $sessionLangId);

            $childs = Content::where('parent_id', $contentData->id)->where('is_active', '1')->count();
            $parentId        = ($childs > 0) ? $contentData->id : $contentData->parent_id;
            //$parentId		= $contentData->parent_id;
            //$parentData 	= Content::getContentDetailById($officeId, $parentId, $sessionLangId);
            $childContents     = Content::getChildContents($officeId, $parentId, $sessionLangId);
            $parentContents     = Content::getChildContents($officeId, 0, $sessionLangId);
            //echo \Helper::get_last_query(); die();

            if ($contentData) {
                $view = 'content.content';
                if ($url == 'links' && $subdomain != 'home') $view = 'content.subdomain_links';
                return view(
                    $view,
                    array(
                        'pageTitle' => $contentData->content_page_title,
                        'metaDescription' => $contentData->content_meta_tags,
                        'metaKeywords' => $contentData->content_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'contentData' => $contentData,
                        'childContents' => $childContents,
                        'parentContents' => $parentContents,
                        'url' => $url
                    )
                );
            } else {
                $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
                return view(
                    'home.404',
                    array(
                        'pageTitle' => $seoDetail->page_title,
                        'metaDescription' => $seoDetail->meta_tags,
                        'metaKeywords' => $seoDetail->meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain
                    )
                );
            }
        } catch (Throwable $e) {
            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
            return view(
                'home.404',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain
                )
            );
        }
    }

    public function getForceContent($subdomain, $id)
    {
        // dd($id);
        // print_r("success");exit;
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $contentData    = Content::getDocContentDetail($id);
            // dd($contentData);

            if (
                $contentData &&
                $officeId > 0 &&
                !empty($contentData->document_attachment) &&
                basename($contentData->document_attachment) === $contentData->document_attachment &&
                in_array(strtolower(pathinfo($contentData->document_attachment, PATHINFO_EXTENSION)), ['pdf', 'jpg', 'jpeg', 'png'], true) &&
                is_file(public_path('uploads/pages/' . $contentData->document_attachment)) &&
                is_readable(public_path('uploads/pages/' . $contentData->document_attachment))
            ) {

                $file = public_path() . '/uploads/pages/' . $contentData->document_attachment;
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $fileName = $contentData->document_name . '.' . $ext;

                $headers = array(
                    'Content-Type: application/' . $ext,
                );
                $response = Response::download($file, $fileName);
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                return  $response;
            } else {
                $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
                return view(
                    'home.404',
                    array(
                        'pageTitle' => $seoDetail->page_title,
                        'metaDescription' => $seoDetail->meta_tags,
                        'metaKeywords' => $seoDetail->meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain
                    )
                );
            }
        } catch (Throwable $e) {
            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
            return view(
                'home.404',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain
                )
            );
        }
    }
    public function getMainForceContent($subdomain, $id, $officeid)
    {
        // print_r($id);exit;
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {

            $contentData    = Content::getContentDetailUrl($id, $officeid);
            // dd($contentData);
            //echo Configuration::get_last_query(); die();

            if (
                $contentData &&
                $officeId > 0 &&
                !empty($contentData->content_attachment) &&
                basename($contentData->content_attachment) === $contentData->content_attachment &&
                in_array(strtolower(pathinfo($contentData->content_attachment, PATHINFO_EXTENSION)), ['pdf', 'jpg', 'jpeg', 'png'], true) &&
                is_file(public_path('uploads/pages/' . $contentData->content_attachment)) &&
                is_readable(public_path('uploads/pages/' . $contentData->content_attachment))
            ) {
                // print_r(public_path() . '/uploads/pages/'.$contentData->content_attachment);exit;
                $file = public_path() . '/uploads/pages/' . $contentData->content_attachment;
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $fileName = $contentData->content_heading . '.' . $ext;
                // DB::table('content')
                // 	->where('id', $contentData->id)
                // 	->increment('content_count', 1);

                return Response::download($file, $fileName);
            } else {
                $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
                return view(
                    'home.404',
                    array(
                        'pageTitle' => $seoDetail->page_title,
                        'metaDescription' => $seoDetail->meta_tags,
                        'metaKeywords' => $seoDetail->meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain
                    )
                );
            }
        } catch (Throwable $e) {
            // dd($e->getMessage(),);
            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
            return view(
                'home.404',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain
                )
            );
        }
    }
}

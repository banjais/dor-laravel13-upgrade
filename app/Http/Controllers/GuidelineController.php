<?php

namespace App\Http\Controllers;

use App\Models\Guideline;
use App\Models\Language;
use App\Models\Notice;
use App\Models\Office;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;

class GuidelineController extends Controller
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
	* get guidelines page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActiveGuidelines($subdomain)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        if ($officeId > 0) {
            $guidelines    = Guideline::getActiveGuidelines($sessionLangId);

            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('guidelines', $sessionLangId);
            return view(
                'guideline.guidelines',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'guidelines' => $guidelines
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
    }

    /*
	* get guideline page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getGuidelineDetail($subdomain, $url)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        // dd($officeId);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $theData    = Guideline::getGuidelineDetail($url, $sessionLangId);
            // dd($theData);
            if ($theData) {
                return view(
                    'guideline.guideline-detail',
                    array(
                        'pageTitle' => $theData->guideline_page_title,
                        'metaDescription' => $theData->guideline_meta_tags,
                        'metaKeywords' => $theData->guideline_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'guidelineData' => $theData
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
            // dd($e->getMessage());
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

    /*
	* get guideline page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getGuidelineDetailById($subdomain, $id)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $theData    = Guideline::getNoticeDetailById($id, $sessionLangId);

            if ($theData) {
                return view(
                    'notice.notice-detail',
                    array(
                        'pageTitle' => $theData->guideline_page_title,
                        'metaDescription' => $theData->guideline_meta_tags,
                        'metaKeywords' => $theData->guideline_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'guidelineData' => $theData
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

    /*
	* force download guideline attachment
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getForceDownload($subdomain, $url)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();

        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $theData    = Guideline::getGuidelineDetail($url, $sessionLangId);
            // dd($theData);
            if (
                $theData &&
                !empty($theData->guideline_attachment) &&
                basename($theData->guideline_attachment) === $theData->guideline_attachment &&
                is_file(public_path() . '/uploads/guideline/' . $theData->guideline_attachment) &&
                is_readable(public_path() . '/uploads/guideline/' . $theData->guideline_attachment)
            ) {
                $file = public_path() . '/uploads/guideline/' . $theData->guideline_attachment;
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                $fileName = $theData->guideline_heading . '.' . $ext;
                $fileName = str_replace("/", '-', $fileName);
                //echo $file;exit;
                DB::table('guidelines')
                    ->where('id', $theData->id)
                    ->increment('download_count', 1);

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
            report($e);
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
    /*
	* force download notice attachment
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getOfficesSearch(Request $request)
    {
        $Officearray =  array();
        $term =  $request->get('term');
        // print_r($term);exit;
        // $term =  $request->get('term');
        $officeData = Office::getActiveSearchOffices($term);
        foreach ($officeData as $value) {
            $Officearray[] = $value->office_name;
        }
        // echo "<pre>";print_r(json_encode($Officearray));exit;
        echo json_encode($Officearray);
    }
}

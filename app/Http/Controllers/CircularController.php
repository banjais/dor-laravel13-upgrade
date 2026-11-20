<?php

namespace App\Http\Controllers;

use App\Models\Circular;
use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Throwable;

class CircularController extends Controller
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
	* get circular page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActiveCirculars($subdomain, Request $request)
    {
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        if (!Auth::check()) {
            $title = \Helper::getNameOftext('circulars', $sessionLangId);
            $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $sessionLangId;

            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
            return view(
                'circular.circulars',
                array(
                    'auth' => false,
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

        $officeId    = Office::getIdfromSubdomain($subdomain);

        if ($officeId > 0) {
            $office_name = $request->get('office_name');
            $from = $request->get('from');
            $to = $request->get('to');

            $circulars    = Circular::getActiveCircularsWithoutOffices($officeId, $sessionLangId, $office_name, $from, $to);

            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('circulars', $sessionLangId);
            return view(
                'circular.circulars',
                array(
                    'auth' => true,
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'circulars' => $circulars
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
	* get circular page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActiveOfficeCirculars($subdomain, Request $request)
    {
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        if (!Auth::check()) {
            $title = \Helper::getNameOftext('circulars', $sessionLangId);
            $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $sessionLangId;

            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
            return view(
                'circular.circulars',
                array(
                    'auth' => false,
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

        // print_r($subdomain);exit;
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $office_name = $request->get('office_name');

            $from = $request->get('from');
            $to = $request->get('to');
            $office_id = Office::getofficeId($office_name);
            // print_r($office_id);exit;
            // $rules = ['office_name' => 'required'];
            // $rules = ['published_date' => 'required'];


            // $validator = Validator::make(Input::all(), $rules);

            // if($validator->fails()){
            // 	$messages = $validator->messages();
            // 	Session::flash('class', 'alert alert-error');
            // 	Session::flash('message', 'Some fields with * are missing.');
            // $circulars	= Circular::getActiveCircularsWithoutOffices($officeId, $sessionLangId);
            // $seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('notices', $sessionLangId);
            // return view('circular::circulars',
            // 			array(
            // 				'pageTitle' => $seoDetail->page_title,
            // 				'metaDescription' => $seoDetail->meta_tags,
            // 				'metaKeywords' => $seoDetail->meta_description,
            // 				'abs_site_url' => $this->abs_site_url,
            // 				'rel_site_url' => $this->rel_site_url,
            // 				'site_name' => $this->site_name,
            // 				'messages' => $messages
            // 				);
            // } else {
            $circulars    = Circular::getActiveCircularsSearch($office_id, $sessionLangId, $from, $to);
            // print_r($circulars);exit;
            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('notices', $sessionLangId);
            return view(
                'circular.circulars',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'circulars' => $circulars,
                    'office_name' => $office_name,

                )
            );
            // }
        } catch (Throwable $e) {
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view(
                'circular.circulars',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name
                )
            );
        }
    }

    /*
	* get circular page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getCircularDetail($subdomain, $url)
    {
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        // dd(Auth::check());

        if (!Auth::check()) {
            $title = \Helper::getNameOftext('circulars', $sessionLangId);
            $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $sessionLangId;

            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
            return view(
                'circular.circular-detail',
                array(
                    'auth' => false,
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

        $officeId    = Office::getIdfromSubdomain($subdomain);

        try {
            $circularData    = Circular::getCircularDetail($officeId, $url, $sessionLangId);
            if ($circularData) {
                return view(
                    'circular.circular-detail',
                    array(
                        'auth' => true,
                        'pageTitle' => $circularData->circular_page_title,
                        'metaDescription' => $circularData->circular_meta_tags,
                        'metaKeywords' => $circularData->circular_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'circularData' => $circularData
                    )
                );
            } else {
                $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
                return view(
                    'circular.circular-detail',
                    array(
                        'auth' => true,
                        'pageTitle' => '',
                        'metaDescription' => '',
                        'metaKeywords' => '',
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'circularData' => $circularData
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
	* get circular page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getCircularDetailById($subdomain, $id)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $circularData    = Circular::getCircularDetailById($officeId, $id, $sessionLangId);
            //echo Configuration::get_last_query(); die();

            if ($circularData) {
                return view(
                    'circular.circular-detail',
                    array(
                        'pageTitle' => $circularData->circular_page_title,
                        'metaDescription' => $circularData->circular_meta_tags,
                        'metaKeywords' => $circularData->circular_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'circularData' => $circularData
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
	* force download circular attachment
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
            $circularData    = Circular::getCircularDetail($officeId, $url, $sessionLangId);
            if (
                $circularData &&
                !empty($circularData->circular_attachment) &&
                basename($circularData->circular_attachment) === $circularData->circular_attachment &&
                is_file(public_path() . '/uploads/circular/' . $circularData->circular_attachment) &&
                is_readable(public_path() . '/uploads/circular/' . $circularData->circular_attachment)
            ) {
                $file = public_path() . '/uploads/circular/' . $circularData->circular_attachment;
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $fileName = $circularData->circular_heading . '.' . $ext;
                DB::table('circular')
                    ->where('id', $circularData->id)
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
	* force download circular attachment
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getOfficesSearch(Request $request)
    {
        $Officearray =  array();
        $term =  $request->get('term');
        // print_r($term);exit;
        // $term =  Input::get('term');
        $officeData = Office::getActiveSearchOffices($term);
        foreach ($officeData as $value) {
            $Officearray[] = $value->office_name;
        }
        // echo "<pre>";print_r(json_encode($Officearray));exit;
        echo json_encode($Officearray);
    }
}

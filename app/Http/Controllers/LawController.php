<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Law;
use App\Models\Office;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Throwable;

class LawController extends Controller
{
    public $abs_site_url;
	public $rel_site_url;
	public $site_name;
	public $site_link;
	public $officeId;

	public function __construct(){
		$this->abs_site_url = ConfigurationController::abs_site_url();
		$this->rel_site_url = ConfigurationController::rel_site_url();
		$this->site_name = ConfigurationController::site_name();
	}

	/*
	* get law page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
	public function getActiveLaws($subdomain)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		if($officeId > 0){
			$laws	= Law::getActiveLaws($sessionLangId);
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('laws', $sessionLangId);
			return view('laws.laws',
						array(
							'pageTitle' => $seoDetail->page_title,
							'metaDescription' => $seoDetail->meta_tags,
							'metaKeywords' => $seoDetail->meta_description,
							'abs_site_url' => $this->abs_site_url,
							'rel_site_url' => $this->rel_site_url,
							'site_name' => $this->site_name,
							'subdomain' => $subdomain,
							'laws' => $laws
						)
					);
		} else {
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
			return view('home.404',
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
	* get law page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getLawDetail($subdomain, $url)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$lawData	= Law::getLawDetail($url, $sessionLangId);
			//echo Configuration::get_last_query(); die();

			if($lawData && $officeId > 0) {
				return view('laws.law-detail',
							array(
								'pageTitle' => $lawData->law_page_title,
								'metaDescription' => $lawData->law_meta_tags,
								'metaKeywords' => $lawData->law_meta_description,
								'abs_site_url' => $this->abs_site_url,
								'rel_site_url' => $this->rel_site_url,
								'site_name' => $this->site_name,
								'subdomain' => $subdomain,
								'lawData' => $lawData
							)
				);
			} else {
				$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
				return view('home.404',
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
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
			return view('home.404',
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
	* get law page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getLawDetailById($subdomain, $id)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$lawData	= Law::getLawDetailById($officeId, $id, $sessionLangId);
			//echo Configuration::get_last_query(); die();

			if($lawData && $officeId > 0) {
				return view('laws.law-detail',
							array(
								'pageTitle' => $lawData->law_page_title,
								'metaDescription' => $lawData->law_meta_tags,
								'metaKeywords' => $lawData->law_meta_description,
								'abs_site_url' => $this->abs_site_url,
								'rel_site_url' => $this->rel_site_url,
								'site_name' => $this->site_name,
								'subdomain' => $subdomain,
								'lawData' => $lawData
							)
				);
			} else {
				$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
				return view('home.404',
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
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
			return view('home.404',
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

	public function getForceDownload($subdomain, $url){
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$lawData	= Law::getLawDetail($url, $sessionLangId);
			//echo Configuration::get_last_query(); die();


			if (
                        $lawData &&
                        $officeId > 0 &&
                        !empty($lawData->law_attachment) &&
                        basename($lawData->law_attachment) === $lawData->law_attachment &&
                        is_file(public_path() . '/uploads/law/' . $lawData->law_attachment) &&
                        is_readable(public_path() . '/uploads/law/' . $lawData->law_attachment)
                    ) {
				$file = public_path() . '/uploads/law/'.$lawData->law_attachment;
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				$fileName = $lawData->law_heading .'.'. $ext;

				DB::table('laws')
					->where('id', $lawData->id)
					->increment('download_count', 1);

				$headers = array(
		            'Content-Type: application/'.$ext,
		        );
				$response = Response::download($file, $fileName);
				if (ob_get_level() > 0) {
                    ob_end_clean();
                }
				return  $response;

			} else {
				$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
				return view('home.404',
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
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
				return view('home.404',
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

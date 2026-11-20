<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Throwable;

class TenderController extends Controller
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
	* get tender page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
	public function getActiveTenders($subdomain)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		if($officeId > 0) {
			if($subdomain == 'home'){
			$tenders	= Tender::getActiveTenders('', $sessionLangId);
			//echo Configuration::get_last_query(); exit;
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('tenders', $sessionLangId);
			return view('tender.alltenders',
						array(
							'pageTitle' => $seoDetail->page_title,
							'metaDescription' => $seoDetail->meta_tags,
							'metaKeywords' => $seoDetail->meta_description,
							'abs_site_url' => $this->abs_site_url,
							'rel_site_url' => $this->rel_site_url,
							'site_name' => $this->site_name,
							'subdomain' => $subdomain,
							'tenders' => $tenders
						)
					);
			} else {
			$tenders	= Tender::getActiveTenders($officeId, $sessionLangId);
			//echo Configuration::get_last_query(); exit;
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('tenders', $sessionLangId);
			return view('tender.tenders',
						array(
							'pageTitle' => $seoDetail->page_title,
							'metaDescription' => $seoDetail->meta_tags,
							'metaKeywords' => $seoDetail->meta_description,
							'abs_site_url' => $this->abs_site_url,
							'rel_site_url' => $this->rel_site_url,
							'site_name' => $this->site_name,
							'subdomain' => $subdomain,
							'tenders' => $tenders
						)
					);
		}
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

	public function searchActiveTenders($subdomain, Request $request)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$expiry_from = $request->get('expiry_from');
		$expiry_to = $request->get('expiry_to');

		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		if($officeId > 0) {
			if($subdomain == 'home'){
			$tenders	= Tender::getActiveTenders('', $sessionLangId, $expiry_from, $expiry_to);
			//echo \Helper::get_last_query(); exit;

			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('tenders', $sessionLangId);
			return view('tender.alltenders',
						array(
							'pageTitle' => $seoDetail->page_title,
							'metaDescription' => $seoDetail->meta_tags,
							'metaKeywords' => $seoDetail->meta_description,
							'abs_site_url' => $this->abs_site_url,
							'rel_site_url' => $this->rel_site_url,
							'site_name' => $this->site_name,
							'subdomain' => $subdomain,
							'tenders' => $tenders
						)
					);
			} else {
			$tenders	= Tender::getActiveTenders($officeId, $sessionLangId);
			//echo Configuration::get_last_query(); exit;
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('tenders', $sessionLangId);
			return view('tender.tenders',
						array(
							'pageTitle' => $seoDetail->page_title,
							'metaDescription' => $seoDetail->meta_tags,
							'metaKeywords' => $seoDetail->meta_description,
							'abs_site_url' => $this->abs_site_url,
							'rel_site_url' => $this->rel_site_url,
							'site_name' => $this->site_name,
							'subdomain' => $subdomain,
							'tenders' => $tenders
						)
					);
		}
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
	* get tender page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getTenderDetail($subdomain, $url)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$tenderData	= Tender::getTenderDetail($officeId, $url, $sessionLangId);
            // dd($tenderData);
			//echo Configuration::get_last_query(); die();
			if($tenderData) {
				return view('tender.tender-detail',
							array(
								'pageTitle' => $tenderData->tender_page_title,
								'metaDescription' => $tenderData->tender_meta_tags,
								'metaKeywords' => $tenderData->tender_meta_description,
								'abs_site_url' => $this->abs_site_url,
								'rel_site_url' => $this->rel_site_url,
								'site_name' => $this->site_name,
								'subdomain' => $subdomain,
								'tenderData' => $tenderData
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
	* get tender page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getTenderDetailById($subdomain, $id)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$tenderData	= Tender::getTenderDetailById($officeId, $id, $sessionLangId);
			// echo Configuration::get_last_query(); die();

			if($tenderData) {
				return view('tender.tender-detail',
							array(
								'pageTitle' => $tenderData->tender_page_title,
								'metaDescription' => $tenderData->tender_meta_tags,
								'metaKeywords' => $tenderData->tender_meta_description,
								'abs_site_url' => $this->abs_site_url,
								'rel_site_url' => $this->rel_site_url,
								'site_name' => $this->site_name,
								'subdomain' => $subdomain,
								'tenderData' => $tenderData
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
            dd($e->getMessage());
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
	* force download tender attachment
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getForceDownload($subdomain, $url)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$tenderData	= Tender::getTenderDetail($officeId, $url, $sessionLangId);
			if (
                            $tenderData &&
                            !empty($tenderData->tender_attachment) &&
                            basename($tenderData->tender_attachment) === $tenderData->tender_attachment &&
                            is_file(public_path() . '/uploads/tender/' . $tenderData->tender_attachment) &&
                            is_readable(public_path() . '/uploads/tender/' . $tenderData->tender_attachment)
                        ) {
				$file = public_path() . '/uploads/tender/'.$tenderData->tender_attachment;
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				$fileName = $tenderData->tender_heading .'.'. $ext;
				DB::table('tender')
					->where('id', $tenderData->id)
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

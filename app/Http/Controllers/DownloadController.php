<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;

class DownloadController extends Controller
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
	* get download page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
	public function getActiveDownloads($subdomain)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		if($officeId > 0){
			$downloads	= Download::getActiveDownloads($officeId, $sessionLangId);
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('downloads', $sessionLangId);
			return view('download.downloads',
						array(
							'pageTitle' => $seoDetail->page_title,
							'metaDescription' => $seoDetail->meta_tags,
							'metaKeywords' => $seoDetail->meta_description,
							'abs_site_url' => $this->abs_site_url,
							'rel_site_url' => $this->rel_site_url,
							'site_name' => $this->site_name,
							'subdomain' => $subdomain,
							'downloads' => $downloads
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
	* get download page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getDownloadDetail($subdomain, $url)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);
		try{
			$downloadData	= Download::getDownloadDetail($url, $sessionLangId);
			//echo Configuration::get_last_query(); die();

			if($downloadData && $officeId > 0) {
				return view('download.download-detail',
							array(
								'pageTitle' => $downloadData->download_page_title,
								'metaDescription' => $downloadData->download_meta_tags,
								'metaKeywords' => $downloadData->download_meta_description,
								'abs_site_url' => $this->abs_site_url,
								'rel_site_url' => $this->rel_site_url,
								'site_name' => $this->site_name,
								'subdomain' => $subdomain,
								'downloadData' => $downloadData
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
	* get download page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getDownloadDetailById($subdomain, $id)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$downloadData	= Download::getDownloadDetailById($officeId, $id, $sessionLangId);

			if($downloadData && $officeId >0) {
				return view('download::download-detail',
							array(
								'pageTitle' => $downloadData->download_page_title,
								'metaDescription' => $downloadData->download_meta_tags,
								'metaKeywords' => $downloadData->download_meta_description,
								'abs_site_url' => $this->abs_site_url,
								'rel_site_url' => $this->rel_site_url,
								'site_name' => $this->site_name,
								'subdomain' => $subdomain,
								'downloadData' => $downloadData
							)
				);
			} else {
				$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
				return view('home::404',
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
			return view('home::404',
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
			$downloadData	= Download::getDownloadDetail($url, $sessionLangId);
			//echo Configuration::get_last_query(); die();

			if (
                            $downloadData &&
                            $officeId > 0 &&
                            !empty($downloadData->download_attachment) &&
                            basename($downloadData->download_attachment) === $downloadData->download_attachment &&
                            is_file(public_path() . '/uploads/download/' . $downloadData->download_attachment) &&
                            is_readable(public_path() . '/uploads/download/' . $downloadData->download_attachment)
                        ) {
				$file     = public_path().'/uploads/download/'.$downloadData->download_attachment;
				$ext      = pathinfo($file, PATHINFO_EXTENSION);
				$fileName = $downloadData->download_heading.'.'.$ext;
				DB::table('download')
					->where('id', $downloadData->id)
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
				$seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
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

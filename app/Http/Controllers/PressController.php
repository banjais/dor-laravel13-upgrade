<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Office;
use App\Models\Press;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Throwable;

class PressController extends Controller
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
	* get press page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
	public function getActivePresses($subdomain)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		if($officeId >0){
			$presses	= Press::getActivePresses($officeId, $sessionLangId);
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('presses', $sessionLangId);

			return view('press.presses',
					array(
						'pageTitle' => $seoDetail->page_title,
						'metaDescription' => $seoDetail->meta_tags,
						'metaKeywords' => $seoDetail->meta_description,
						'abs_site_url' => $this->abs_site_url,
						'rel_site_url' => $this->rel_site_url,
						'site_name' => $this->site_name,
						'subdomain' => $subdomain,
						'presses' => $presses
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
	* get press page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getPressDetail($subdomain, $url)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$pressData	= Press::getPressDetail($officeId, $url, $sessionLangId);
			if($pressData) {
				return view('press.press-detail',
							array(
								'pageTitle' => $pressData->press_page_title,
								'metaDescription' => $pressData->press_meta_tags,
								'metaKeywords' => $pressData->press_meta_description,
								'abs_site_url' => $this->abs_site_url,
								'rel_site_url' => $this->rel_site_url,
								'site_name' => $this->site_name,
								'subdomain' => $subdomain,
								'pressData' => $pressData
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
	* get press page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
	public function getPressDetailById($subdomain, $id)
	{
		$officeId	= Office::getIdfromSubdomain($subdomain);
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		try{
			$pressData	= Press::getPressDetailById($officeId, $id, $sessionLangId);
			//echo Configuration::get_last_query(); die();

			if($pressData) {
				return view('press.press-detail',
							array(
								'pageTitle' => $pressData->press_page_title,
								'metaDescription' => $pressData->press_meta_tags,
								'metaKeywords' => $pressData->press_meta_description,
								'abs_site_url' => $this->abs_site_url,
								'rel_site_url' => $this->rel_site_url,
								'site_name' => $this->site_name,
								'subdomain' => $subdomain,
								'pressData' => $pressData
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
	* force download notice attachment
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
			$pressData	= Press::getPressDetail($officeId, $url, $sessionLangId);
			if (
                            $pressData &&
                            !empty($pressData->press_attachment) &&
                            basename($pressData->press_attachment) === $pressData->press_attachment &&
                            is_file(public_path() . '/uploads/press/' . $pressData->press_attachment) &&
                            is_readable(public_path() . '/uploads/press/' . $pressData->press_attachment)
                    ) {
				$file = public_path() . '/uploads/press/'.$pressData->press_attachment;
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				$fileName = $pressData->press_heading .'.'. $ext;
				DB::table('press')
					->where('id', $pressData->id)
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

<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VehicleController extends Controller
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
	* get bill page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
	public function getActiveVehicles($subdomain)
	{
		$languageId = Language::getIdFromCode();
		$sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
		Session::put('language_id', $sessionLangId);

		$officeId	= Office::getIdfromSubdomain($subdomain);

		if($officeId > 0) {
			$vehicles	= Vehicle::getActiveVehicles($officeId, $sessionLangId);
			$seoDetail	= Seo::getSeoDetailsByPageNameAndLanguage('vehicles', $sessionLangId);

			return view('vehicles.vehicles',
					array(
						'pageTitle' => $seoDetail->page_title,
						'metaDescription' => $seoDetail->meta_tags,
						'metaKeywords' => $seoDetail->meta_description,
						'abs_site_url' => $this->abs_site_url,
						'rel_site_url' => $this->rel_site_url,
						'site_name' => $this->site_name,
						'subdomain' => $subdomain,
						'vehicles' => $vehicles
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
}

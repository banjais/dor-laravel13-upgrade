<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Throwable;

class UserController extends Controller
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
	* get staff list page
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getUserLists($subdomain)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $staffs    = User::getActiveStaffs($officeId, $sessionLangId);
            // echo "<pre>";print_r($staffs);exit;
            //var_dump($staffs);die();
            //echo \Helper::get_last_query(); die();
            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('home', $sessionLangId);

            if ($subdomain) {
                return view(
                    'users.staffs',
                    array(
                        'pageTitle' => $seoDetail->page_title,
                        'metaDescription' => $seoDetail->meta_tags,
                        'metaKeywords' => $seoDetail->meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'staffs' => $staffs
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
	* get user detail page
	* @param string $subdomain
	* @param int $id
	* @return Illuminate\View\View
	*/
    public function getUserDetail($subdomain, $id)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        /*try{ */
        $staffData    = User::getUserDetail($officeId, $id, $sessionLangId);
        //echo Configuration::get_last_query(); die();
        $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('home', $sessionLangId);

        if ($staffData) {
            return view(
                'users.staff-detail',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'staffData' => $staffData
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
        /*} catch (ErrorException $e) {
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
		} */
    }
}

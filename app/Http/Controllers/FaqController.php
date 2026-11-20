<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FaqController extends Controller
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
	* get faq page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActiveFaqs($subdomain)
    {
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        $officeId    = Office::getIdfromSubdomain($subdomain);

        if ($officeId > 0) {
            $faqs    = Faq::getActiveFaqs($sessionLangId);
            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('faqs', $sessionLangId);
            return view(
                'faq.faqs',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'faqs' => $faqs
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
}

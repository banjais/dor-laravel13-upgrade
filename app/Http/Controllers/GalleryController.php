<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Gallery;
use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GalleryController extends Controller
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;
    public $officeId;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name = ConfigurationController::site_name();
    }

    /*
	* get gallery page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActiveGalleries($subdomain, $url)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        $checkexist = Album::checkExist($url, $officeId);
        // dd($checkexist);

        if ($officeId > 0 && $checkexist) {
            $galleries    = Gallery::getActiveGalleries($officeId, $url, $sessionLangId);
            // dd($galleries);
            $albumName    = Album::getAlbumNameByUrlAndLanguage($officeId, $url, $sessionLangId);
            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('gallery', $sessionLangId);
            return view(
                'album.photogallery',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'galleries' => $galleries,
                    'albumName' => $albumName
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

<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Office;
use App\Models\Seo;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Throwable;

class TrainingController extends Controller
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
        $this->site_name    = ConfigurationController::site_name();
    }

    /*
     * get training page according to office
     * @param string $subdomain
     * @return Illuminate\View\View
     */
    public function getActiveTrainings($subdomain)
    {
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        $officeId = Office::getIdfromSubdomain($subdomain);

        if ($officeId > 0) {
            $trainings = Training::getActiveTrainings($sessionLangId);
            //var_dump($trainings);die();
            //echo Configuration::get_last_query(); exit();
            $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('trainings', $sessionLangId);

            return view(
                'training.trainings',
                array(
                    'pageTitle'       => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords'    => $seoDetail->meta_description,
                    'abs_site_url'    => $this->abs_site_url,
                    'rel_site_url'    => $this->rel_site_url,
                    'site_name'       => $this->site_name,
                    'subdomain'       => $subdomain,
                    'trainings'       => $trainings,
                )
            );
        } else {
            $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
            return view(
                'home.404',
                array(
                    'pageTitle'       => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords'    => $seoDetail->meta_description,
                    'abs_site_url'    => $this->abs_site_url,
                    'rel_site_url'    => $this->rel_site_url,
                    'site_name'       => $this->site_name,
                    'subdomain'       => $subdomain,
                )
            );
        }
    }

    /*
     * get training page according to office
     * @param string $subdomain
     * @param string $url
     * @return Illuminate\View\View
     */
    public function getTrainingDetail($subdomain, $url)
    {
        $officeId      = Office::getIdfromSubdomain($subdomain);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            //die();
            $trainingData = Training::getTrainingDetail($url, $sessionLangId);
            //echo Configuration::get_last_query(); die();

            if ($trainingData) {
                return view(
                    'training.training-detail',
                    array(
                        'pageTitle'       => $trainingData->training_page_title,
                        'metaDescription' => $trainingData->training_meta_tags,
                        'metaKeywords'    => $trainingData->training_meta_description,
                        'abs_site_url'    => $this->abs_site_url,
                        'rel_site_url'    => $this->rel_site_url,
                        'site_name'       => $this->site_name,
                        'subdomain'       => $subdomain,
                        'trainingData'    => $trainingData,
                    )
                );
            } else {
                $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
                return view(
                    'home.404',
                    array(
                        'pageTitle'       => $seoDetail->page_title,
                        'metaDescription' => $seoDetail->meta_tags,
                        'metaKeywords'    => $seoDetail->meta_description,
                        'abs_site_url'    => $this->abs_site_url,
                        'rel_site_url'    => $this->rel_site_url,
                        'site_name'       => $this->site_name,
                        'subdomain'       => $subdomain,
                    )
                );
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('error', $sessionLangId);
            return view(
                'home.404',
                array(
                    'pageTitle'       => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords'    => $seoDetail->meta_description,
                    'abs_site_url'    => $this->abs_site_url,
                    'rel_site_url'    => $this->rel_site_url,
                    'site_name'       => $this->site_name,
                    'subdomain'       => $subdomain,
                )
            );
        }
    }
}

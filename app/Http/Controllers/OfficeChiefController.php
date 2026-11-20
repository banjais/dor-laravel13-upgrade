<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Office;
use App\Models\OfficeChief;
use App\Models\Seo;
use App\Models\Staff;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;

class OfficeChiefController extends Controller
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
     * get staff list page
     * @param string $subdomain
     * @return Illuminate\View\View
     */
    public function getStaffLists($subdomain)
    {
        // dd($subdomain);
        $officeId      = Office::getIdfromSubdomain($subdomain);
        // dd($officeId);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            // $staffs    = Staff::getActiveStaffs($officeId, $sessionLangId);
            // DB::enableQueryLog();
            $staffs = OfficeChief::with([
                'details' => function ($query) use ($sessionLangId) {
                    $query->where('language_id', $sessionLangId);
                }
            ])->where('office_id', $officeId)
                ->where('is_deleted', '0')
                ->where('is_active', '1')
                ->orderBy('order_position', 'asc')
                ->get();

                // dd(Helper::get_last_query());

            $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('office chiefs', $sessionLangId);

            if ($subdomain) {
                return view(
                    'office_chiefs.staffs',
                    array(
                        'pageTitle'       => $seoDetail->page_title,
                        'metaDescription' => $seoDetail->meta_tags,
                        'metaKeywords'    => $seoDetail->meta_description,
                        'abs_site_url'    => $this->abs_site_url,
                        'rel_site_url'    => $this->rel_site_url,
                        'site_name'       => $this->site_name,
                        'subdomain'       => $subdomain,
                        'staffs'          => $staffs,
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
            // dd($e->getMessage());
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

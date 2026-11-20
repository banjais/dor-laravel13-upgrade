<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginCredentialRequest;
use App\Models\Banner;
use App\Models\Circular;
use App\Models\Configuration;
use App\Models\Content;
use App\Models\Distance;
use App\Models\Download;
use App\Models\Home;
use App\Models\Language;
use App\Models\Law;
use App\Models\LoginRequest;
use App\Models\News;
use App\Models\Notice;
use App\Models\Office;
use App\Models\Publication;
use App\Models\Seo;
use App\Models\Tender;
use App\Models\Ticker;
use App\Models\Zone;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HomeController extends ConfigurationController
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name    = ConfigurationController::site_name();
    }

    public function getHome()
    {
        return redirect()->to('home');
    }
    /*
     * get home page viewer side
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex($subdomain)
    {
        // dd($subdomain);
        $officeId      = Office::getIdfromSubdomain($subdomain);
        // dd($officeId);
        // dd($officeId);
        $languageId    = Language::getIdFromCode();
        // Session::put('language_id', 1);
        // dd($languageId);
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        // dd($sessionLangId);

        // printf(date_format(Carbon::now(), 'Y-m-d'));exit;
        if ($officeId > 0) {

            try {

                $banners = Banner::getHomePageBanners($officeId, $sessionLangId);

                // $tenders = Tender::getHomePageTenders($officeId, $sessionLangId);

                // $homepagenews = News::getHomePageNews($officeId, $sessionLangId);
                $homepagenewstickers = Ticker::getHomePageNewsTicker($officeId, $sessionLangId);
                // $publications = Publication::getHomePagePublications($officeId, $sessionLangId);
                // $notices      = Notice::getHomePageNotices($officeId, $sessionLangId);
                // $downloads    = Download::getHomePageDownloads($officeId, $sessionLangId);
                // $circulars    = Circular::getMainHomePageCirculars($officeId, $languageId);
                $homeData1    = Home::getHomePageData(1, $officeId, $sessionLangId);
                // dd($homeData1);
                $homeData2    = Home::getHomePageData(4, $officeId, $sessionLangId);
                // dd($homeData2);
                $homeAbout    = Home::getHomePageGeneralAbout(4, $officeId, $sessionLangId);
                $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('home', $sessionLangId);
                $homeMap   = Office::getOfficeDetailFromId($officeId, $sessionLangId);
                $homespokeperson  = Home::getHomePageSpokeperson($officeId, $officeId, $sessionLangId);
                $homeDirectorGeneral  = Home::getHomePageDirectorGeneral($officeId, $officeId, $sessionLangId);
                // dd($homespokeperson);
                $homeInformationOfficer  = Home::getHomePageInformationOfficer(4, $officeId, $sessionLangId);
                $homeGrievanceOfficer = $subdomain == 'home'
                    ? Home::getHomePageGrievanceOfficer(4, $officeId, $sessionLangId)
                    : null;

                // dd($sidebarnews);
                // dd($homeInformationOfficer);


                if ($subdomain == 'home') {
                    return view(
                        'home.home_2021',
                        array(
                            'pageTitle'       => $seoDetail->page_title,
                            'metaDescription' => $seoDetail->meta_tags,
                            'metaKeywords'    => $seoDetail->meta_description,
                            'abs_site_url'    => $this->abs_site_url,
                            'rel_site_url'    => $this->rel_site_url,
                            'site_name'       => $this->site_name,
                            'banners'         => $banners,
                            // 'tenders'         => $tenders,
                            // 'homepagenews'    => $homepagenews,
                            // 'publications'    => $publications,
                            // 'notices'         => $notices,
                            // 'downloads'       => $downloads,
                            // 'circulars'       => $circulars,
                            'homeData1'       => $homeData1,
                            'homeData2'       => $homeData2,
                            'subdomain'       => $subdomain,
                            'homespokeperson' => $homespokeperson,
                            'homeInformationOfficer' => $homeInformationOfficer,
                            'homeDirectorGeneral' => $homeDirectorGeneral,
                            'homeGrievanceOfficer' => $homeGrievanceOfficer,
                            // 'sidebarnews' => $sidebarnews,
                            'homepagenewstickers' => $homepagenewstickers,
                            // 'roadLaws' =>$roadLaws,
                            'is_home' => 1
                        )
                    );
                } else {

                    return view(
                        'home.otherhome_2021',
                        array(
                            'pageTitle'       => $seoDetail->page_title,
                            'metaDescription' => $seoDetail->meta_tags,
                            'metaKeywords'    => $seoDetail->meta_description,
                            'abs_site_url'    => $this->abs_site_url,
                            'rel_site_url'    => $this->rel_site_url,
                            'site_name'       => $this->site_name,
                            'banners'         => $banners,
                            // 'tenders'         => $tenders,
                            // 'homepagenews'    => $homepagenews,
                            // 'publications'    => $publications,
                            // 'notices'         => $notices,
                            // 'downloads'       => $downloads,
                            'homeData1'       => $homeData1,
                            'homeData2'       => $homeData2,
                            'homeAbout'       => $homeAbout,
                            'subdomain'       => $subdomain,
                            'homeMap'         => $homeMap,
                            'is_home' => 1
                        )
                    );
                }
            } catch (Throwable $ex) {
                report($ex);
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
                        'languageId'        => $sessionLangId
                    )
                );
            }
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
                    'languageId'        => $sessionLangId
                )
            );
        }
    }

    /*
     * get home page section detail according to office
     * @param string $subdomain
     * @param string $url
     * @return Illuminate\View\View
     */

    /**
     * Display a banner detail page for the requested office.
     */
    public function getBannerDetail($subdomain, $id)
    {
        $officeId = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = Session::get('language_id') ?: $languageId;
        Session::put('language_id', $sessionLangId);

        if (!$officeId || $officeId <= 0) {
            abort(404);
        }

        $banner = Banner::getPublishedDetail($id, $officeId, $sessionLangId);

        if (!$banner) {
            abort(404);
        }

        return view('home.banner-detail', [
            'pageTitle' => $banner->banner_title,
            'metaDescription' => $banner->banner_caption,
            'metaKeywords' => $banner->banner_caption,
            'abs_site_url' => $this->abs_site_url,
            'rel_site_url' => $this->rel_site_url,
            'site_name' => $this->site_name,
            'subdomain' => $subdomain,
            'banner' => $banner,
        ]);
    }

    public function getHomeDetail($subdomain, $url)
    {
        // dd($url);
        $officeId      = Office::getIdfromSubdomain($subdomain);
        // dd($officeId);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        // dd($sessionLangId);

        try {
            //fetch specific data for specific url
            $main_home_data_urls = ['terms-and-conditions', 'privacy-policy', 'screen-reader'];
            if (in_array(Str::lower($url), $main_home_data_urls)) {
                $mainOfficeId      = Office::getIdfromSubdomain('home');
                $homeData = Home::getHomeDetail($mainOfficeId, $url, $sessionLangId);
            } else {
                $homeData = Home::getHomeDetail($officeId, $url, $sessionLangId);
            }

            $homeMap  = Office::getOfficeDetailFromId($officeId, $sessionLangId);

            // echo Configuration::get_last_query(); die();
            // dd($homeData);
            // echo Helper::get_last_query(); die();

            if ($homeData) {
                return view(
                    'home.home-detail',
                    array(
                        'pageTitle'       => $homeData->home_page_title,
                        'metaDescription' => $homeData->home_meta_tags,
                        'metaKeywords'    => $homeData->home_meta_description,
                        'abs_site_url'    => $this->abs_site_url,
                        'rel_site_url'    => $this->rel_site_url,
                        'site_name'       => ConfigurationController::site_name(),
                        'subdomain'       => $subdomain,
                        'homeData'        => $homeData,
                        'homeMap'         => $homeMap,
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
        } catch (Throwable $ex) {
            // dd($ex->getMessage());
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
     * get feedback page
     * @param string $subdomain
     * @return Illuminate\View\View
     */
    public function getFeedback($subdomain)
    {
        $officeId      = Office::getIdfromSubdomain($subdomain);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        if ($officeId > 0) {
            $captcha   = Helper::create_captcha();
            $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('feedback', $sessionLangId);
            return view(
                'home.feedback',
                array(
                    'pageTitle'       => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords'    => $seoDetail->meta_description,
                    'abs_site_url'    => $this->abs_site_url,
                    'rel_site_url'    => $this->rel_site_url,
                    'site_name' => Configuration::getValueFromName('site_name'),
                    'subdomain'       => $subdomain,
                    'captcha'         => $captcha,
                    'officeId' => $officeId
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
     * post feedback
     * @param string $subdomain
     * @return Illuminate\View\View
     */
    public function postFeedback($subdomain, Request $request)
    {
        $officeId      = Office::getIdfromSubdomain($subdomain);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        $officeData = Office::getOfficeDetailFromSubdomain($subdomain, $sessionLangId);
        $seoDetail  = Seo::getSeoDetailsByPageNameAndLanguage('feedback', $sessionLangId);

        $rules = array(
            'full_name'      => 'required',
            'email_address'  => 'required|email',
            'cc_email'       => 'nullable|email',
            'contact_number' => 'required',
            'address'        => 'required',
            'message'        => 'required',
        );

        $isContactForm = $request->input('form_context') === 'contact';

        if ($isContactForm) {
            $rules['form_context'] = 'required|in:contact';
            $rules['contact_bcc'] = 'sometimes|array';
            $rules['contact_bcc.*'] = 'in:director_general,grievance_hearing_officer';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Session::flash('class', 'alert alert-warning');
            // Session::flash('message', 'Some fields are missing');
            // $captcha = Helper::create_captcha();
            // return view(
            //     'home.feedback',
            //     array(
            //         'pageTitle'       => $seoDetail->page_title,
            //         'metaDescription' => $seoDetail->meta_tags,
            //         'metaKeywords'    => $seoDetail->meta_description,
            //         'abs_site_url'    => $this->abs_site_url,
            //         'rel_site_url'    => $this->rel_site_url,
            //         'site_name'       => $this->site_name,
            //         'subdomain'       => $subdomain,
            //         'captcha'         => $captcha,
            //     )
            // );

            return back()->withErrors($validator);
        } else {

            if (Session::get('phrase') != $request->get('captcha')) {
                Session::flash('class', 'alert alert-danger');
                Session::flash('message', 'Invalid Image verification.');
                // $captcha = Helper::create_captcha();
                return back()->withInput();
                // return view(
                //     'home.feedback',
                //     array(
                //         'pageTitle'       => $seoDetail->page_title,
                //         'metaDescription' => $seoDetail->meta_tags,
                //         'metaKeywords'    => $seoDetail->meta_description,
                //         'abs_site_url'    => $this->abs_site_url,
                //         'rel_site_url'    => $this->rel_site_url,
                //         'site_name'       => $this->site_name,
                //         'subdomain'       => $subdomain,
                //         'captcha'         => $captcha,
                //     )
                // );
            } else {
                $subject = "Feedback form submitted.";

                $home = '<table cellspacing="0" cellpadding="0" width="100%">';
                $home .= '<tr><td><p>Dear <strong>admin,</strong> <br />';
                $home .= '<br />A visitor <strong>' . $request->get('full_name') . '</strong> has given feedback with following details.</td>';
                $home .= '</table>';
                $home .= '<table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;margin-top: 20px;">';
                $home .= '<tr><td width="150">Full Name :</td><td>' . $request->get('full_name') . '</td></tr>';
                $home .= '<tr style="margin-top: 10px;"><td width="150">Email Address :</td><td>' . $request->get('email_address') . '</td></tr>';
                $home .= '<tr style="margin-top: 10px;"><td width="150">Contact Number:</td><td>' . $request->get('contact_number') . '</td></tr>';
                $home .= '<tr style="margin-top: 10px;"><td width="150">Address :</td><td>' . $request->get('address') . '</td></tr>';
                $home .= '<tr style="margin-top: 10px;"><td width="150">Message :</td><td>' . $request->get('message') . '</td></tr>';
                $home .= '</table>';
                $home .= '<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">';
                $home .= '<tr><td><p>Kind Regards,</strong> <br />';
                $home .= $this->site_name . '</td>';
                $home .= '</table>';

                // Collect valid email addresses from the current Office profile.
                $officePrimaryEmail = trim((string) ($officeData->office_email_address ?? ''));
                $officeAlternativeEmails = preg_split(
                    '/[;,]+/',
                    (string) ($officeData->office_alternative_email ?? '')
                );

                $validAlternativeEmails = [];
                foreach ($officeAlternativeEmails as $alternativeEmail) {
                    $alternativeEmail = trim($alternativeEmail);
                    if ($alternativeEmail !== '' && filter_var($alternativeEmail, FILTER_VALIDATE_EMAIL)) {
                        $validAlternativeEmails[] = $alternativeEmail;
                    }
                }

                // Remove duplicate email addresses without considering letter case.
                $uniqueEmails = function (array $emails) {
                    $result = [];
                    $seen = [];

                    foreach ($emails as $emailAddress) {
                        $emailAddress = trim((string) $emailAddress);
                        $key = strtolower($emailAddress);

                        if (
                            $emailAddress !== '' &&
                            filter_var($emailAddress, FILTER_VALIDATE_EMAIL) &&
                            !isset($seen[$key])
                        ) {
                            $seen[$key] = true;
                            $result[] = $emailAddress;
                        }
                    }

                    return $result;
                };

                $is_email_test_mode = config('general.email.is_test_mode');

                if ($isContactForm) {
                    // Contact Form: only the current Office primary email is the To recipient.
                    $toEmail = (
                        $officePrimaryEmail !== '' &&
                        filter_var($officePrimaryEmail, FILTER_VALIDATE_EMAIL)
                    ) ? $officePrimaryEmail : null;

                    if ($toEmail === null) {
                        Log::warning('Contact Form email routing failed.', [
                            'office_id' => $officeId,
                            'office_subdomain' => $subdomain,
                            'reason' => $officePrimaryEmail === ''
                                ? 'primary_email_missing'
                                : 'primary_email_invalid',
                        ]);
                    }

                    // Current Office secondary email(s) and visitor's optional address go to CC.
                    $userCcEmail = trim((string) $request->input('cc_email', ''));
                    $contactCc = $uniqueEmails(array_merge(
                        $validAlternativeEmails,
                        [$userCcEmail]
                    ));

                    // Resolve role-based BCC recipients from server-side settings.
                    $selectedBcc = (array) $request->input('contact_bcc', []);
                    $bccCandidates = [];

                    if (in_array('director_general', $selectedBcc, true)) {
                        $headOffice = Office::getOfficeDetailFromSubdomain('home', 2);
                        $bccCandidates[] = $headOffice->office_email_address ?? '';
                        $headSecondary = preg_split(
                            '/[;,]+/',
                            (string) ($headOffice->office_alternative_email ?? '')
                        );
                        $bccCandidates = array_merge($bccCandidates, $headSecondary);
                    }

                    if (in_array('grievance_hearing_officer', $selectedBcc, true)) {
                        $grmsEnabled = Configuration::getValueFromName('contact_grs_bcc_enabled');
                        $grmsEmail = trim((string) Configuration::getValueFromName('contact_grs_bcc_email'));

                        if (
                            ($grmsEnabled === null || (string) $grmsEnabled === '1') &&
                            $grmsEmail !== '' &&
                            filter_var($grmsEmail, FILTER_VALIDATE_EMAIL)
                        ) {
                            $bccCandidates[] = $grmsEmail;
                        }
                    }

                    $contactBcc = $uniqueEmails($bccCandidates);

                    // Test mode must not expose real Office, visitor, or BCC recipients.
                    if ($is_email_test_mode) {
                        $toEmail = config('general.email.test_email_to');
                        $contactCc = [];
                        $contactBcc = [];
                    }

                    $email = !empty($toEmail)
                        ? Helper::sendEmail(
                            $toEmail,
                            $subject,
                            $home,
                            [],
                            '',
                            $contactCc,
                            null,
                            null,
                            null,
                            null,
                            true,
                            $contactBcc
                        )
                        : false;
                } else {
                    // Feedback Form: preserve the existing To/CC behavior.
                    $officeEmails = [];

                    if ($officePrimaryEmail !== '' && filter_var($officePrimaryEmail, FILTER_VALIDATE_EMAIL)) {
                        $officeEmails[] = $officePrimaryEmail;
                    }

                    $officeEmails = array_merge($officeEmails, $validAlternativeEmails);
                    $uniqueOfficeEmails = $uniqueEmails($officeEmails);

                    $userCcEmail = trim((string) $request->input('cc_email', ''));
                    $feedbackCc = $userCcEmail !== '' ? [$userCcEmail] : [];

                    $toEmail = $is_email_test_mode
                        ? config('general.email.test_email_to')
                        : $uniqueOfficeEmails;

                    $email = !empty($toEmail)
                        ? Helper::sendEmail($toEmail, $subject, $home, [], '', $feedbackCc, null, null, null, null, true)
                        : false;
                }

                if ($email) {
                    Session::flash('class', 'alert alert-success');
                    Session::flash('message', 'Your feedback is sent successfully.');
                } else {
                    Session::flash('class', 'alert alert-danger');
                    Session::flash('message', 'Feedback could not be sent. Please contact the concerned office.');
                }

                return back()->withInput($request->only(['full_name', 'email_address', 'contact_number', 'address', 'message', 'cc_email']));
                // $captcha = Helper::create_captcha();
                // return view(
                //     'home.feedback',
                //     array(
                //         'pageTitle'       => $seoDetail->page_title,
                //         'metaDescription' => $seoDetail->meta_tags,
                //         'metaKeywords'    => $seoDetail->meta_description,
                //         'abs_site_url'    => $this->abs_site_url,
                //         'rel_site_url'    => $this->rel_site_url,
                //         'site_name'       => $this->site_name,
                //         'subdomain'       => $subdomain,
                //         'captcha'         => $captcha,
                //     )
                // );
            }
        }
    }

    /*
     * get contact page
     * @param string $subdomain
     * @return Illuminate\View\View
     */
    public function getContact($subdomain)
    {
        $officeId      = Office::getIdfromSubdomain($subdomain);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        if ($officeId > 0) {
            $officeData  = Office::getOfficeDetailFromSubdomain($subdomain, $sessionLangId);
            $contactData = Content::getContentDetail($officeId, 'contact', $sessionLangId);
            // dd($contactData);
            //echo Configuration::get_last_query(); die();
            $captcha   = Helper::create_captcha();
            $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('contact', $sessionLangId);
            return view(
                'home.contact',
                array(
                    'pageTitle'       => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords'    => $seoDetail->meta_description,
                    'abs_site_url'    => $this->abs_site_url,
                    'rel_site_url'    => $this->rel_site_url,
                    'site_name' => Configuration::getValueFromName('site_name'),
                    'subdomain'       => $subdomain,
                    'officeData'      => $officeData,
                    'contactData'     => $contactData,
                    'officeId'        => $officeId,
                    'captcha'         => $captcha,
                    'languageId' => $sessionLangId

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
     * get search page
     * @param string $subdomain
     * @return Illuminate\View\View
     */
    public function getSearch($subdomain)
    {
        $officeId      = Office::getIdfromSubdomain($subdomain);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        if ($officeId > 0) {
            $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('home', $sessionLangId);
            return view(
                'home.search',
                array(
                    'pageTitle'       => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords'    => $seoDetail->meta_description,
                    'abs_site_url'    => $this->abs_site_url,
                    'rel_site_url'    => $this->rel_site_url,
                    'site_name' => Configuration::getValueFromName('site_name'),
                    'subdomain'       => $subdomain,
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
     * get zone page(list of offices zonewise)
     * @param string $subdomain
     * @return Illuminate\View\View
     */
    public function getZone($subdomain, $url)
    {
        $officeId      = Office::getIdfromSubdomain($subdomain);
        $zoneId        = Zone::getIdfromUrl($url);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        if ($zoneId > 0) {

            try {
                $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('home', $sessionLangId);
                $offices   = Office::getOfficeListZonewise($sessionLangId, $zoneId);
                $zoneName  = Zone::getNameZone($sessionLangId, $zoneId);
                //echo Configuration::get_last_query(); exit();

                return view(
                    'home.zone',
                    array(
                        'pageTitle'       => $seoDetail->page_title,
                        'metaDescription' => $seoDetail->meta_tags,
                        'metaKeywords'    => $seoDetail->meta_description,
                        'abs_site_url'    => $this->abs_site_url,
                        'rel_site_url'    => $this->rel_site_url,
                        'site_name'       => $this->site_name,
                        'subdomain'       => $subdomain,
                        'offices'         => $offices,
                        'zoneName'        => $zoneName,
                        'zoneId'          => $zoneId,
                    )
                );
            } catch (Throwable $ex) {
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
     * get zone page(list of offices zonewise)
     * @param string $subdomain
     * @return Illuminate\View\View
     */
    public function getDistanceIndex($subdomain)
    {
        // print_r('rrtr');exit;
        $officeId      = Office::getIdfromSubdomain($subdomain);
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $seoDetail = Seo::getSeoDetailsByPageNameAndLanguage('home', $sessionLangId);
            // print_r('rrtr');exit;
            return view(
                'home.distance',
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
        } catch (Throwable $ex) {
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

    public function calcDistance(Request $request)
    {
        // dd(100);
        $errors = array(); // array to hold validation errors
        $data   = array(); // array to pass back data

        $distance = new Distance();
        $test     = $distance->RouteDijkstra();

        $start = $request->get('start');
        $via   = $request->get('through');
        $end   = $request->get('end');

        if (trim($via) != "") {

            $distanceRoute   = $distance->findDistanceRoute($start, $via, $test);
            $RouteFromOrigin = $distanceRoute['RouteFromOrigin'];
            $Distance        = $distanceRoute['Distance'];
            $distance->RouteDijkstra();
            $distanceRoute = $distance->findDistanceRoute($via, $end, $test);
            $RouteFromOrigin .= " <b> --->&gt; </b> " . $distanceRoute['RouteFromOrigin'];
            $Distance += $distanceRoute['Distance'];
        } else {
            $distanceRoute   = $distance->findDistanceRoute($start, $end, $test);
            $RouteFromOrigin = $distanceRoute['RouteFromOrigin'];
            $Distance        = $distanceRoute['Distance'];
        }

        // if there are items in our errors array, return those errors
        $data['Distance']        = $Distance;
        $data['RouteFromOrigin'] = $RouteFromOrigin;

        // return all our data to an AJAX call
        echo json_encode($data);
        # code...
    }
    /*
     * get home page section detail according to office
     * @param string $subdomain
     * @param string $url
     * @return Illuminate\View\View
     */
    public function getOffice($subdomain, $url)
    {
        $officeId = Office::getIdfromSubdomain($url);

        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        $reginalDetail = Office::getNamefromSubdomain($url, $sessionLangId);

        try {
            $officeData = Office::suboffice_Regional($officeId);

            if ($officeData) {
                //print '<pre>';print_r($reginalDetail);exit;
                return view(
                    'home.office-detail',
                    array(
                        'pageTitle'    => $reginalDetail,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name'    => $this->site_name,
                        'subdomain'    => $subdomain,
                        'officeData'   => $officeData,
                    )
                );
            } else {
                print_r('test');
                exit;
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
        } catch (Throwable $ex) {
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
     * get home page section detail according to office
     * @param string $subdomain
     * @param string $url
     * @return Illuminate\View\View
     */

    public function test()
    {
        $officeData = Office::suboffice_harakpur('4', '2');
        echo "<pre>";
        print_r($officeData);
    }
    public function getHomeOffice($subdomain)
    {
        $officeId = Office::getIdfromSubdomain($subdomain);
        // print '<pre>';print_r($subdomain);exit;
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        $reginalDetail = Office::getNamefromSubdomain($subdomain, $sessionLangId);

        try {
            if ($subdomain == 'home') {
                $officeData = Office::suboffice_AllRegional($officeId, $sessionLangId);
                $officeHarakpur = Office::suboffice_harakpur($officeId, $sessionLangId);
            } else {
                $officeData = Office::suboffice_Regional($officeId, $sessionLangId);
            }

            if ($officeData) {
                //print '<pre>';print_r($reginalDetail);exit;
                return view(
                    'home.office-detail',
                    array(
                        'pageTitle'    => $reginalDetail,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => Configuration::getValueFromName('site_name'),
                        'subdomain'    => $subdomain,
                        'officeData'   => $officeData,
                        'officeHarakpur' => $officeHarakpur,
                    )
                );
            } else {
                // print_r('test');exit;
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
        } catch (Throwable $ex) {
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

    public function getNewsTickerDetail($id)
    {
        $subdomain = 'home';
        $officeId    = Office::getIdfromSubdomain('home');
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $contentData = Ticker::getNewsTickertDetail($officeId, $id, $sessionLangId);

            //var_dump($contentData);exit;

            if ($contentData) {
                return view(
                    'home.news_ticker_content',
                    array(
                        'pageTitle' => $contentData->ticker_title,
                        'metaDescription' => 'sdf',
                        'metaKeywords' => 'sfsdf',
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => Configuration::getValueFromName('site_name'),
                        'subdomain' => $subdomain,
                        'contentData' => $contentData,
                        //'childContents' => $childContents,
                        //'parentContents' => $parentContents
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
        } catch (Throwable $ex) {
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

    //get login credentials request page
    public function getLoginCredentialPage()
    {
        $subdomain = 'home';
        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        $officeData = Office::getOfficeDetailFromSubdomain($subdomain, $sessionLangId);
        $seoDetail  = Seo::getSeoDetailsByPageNameAndLanguage('login credentials', $sessionLangId);

        $captcha   = Helper::create_captcha();

        $d = [
            'pageTitle'       => $seoDetail ? $seoDetail->page_title : '',
            'metaDescription' => $seoDetail ? $seoDetail->meta_tags : '',
            'metaKeywords'    => $seoDetail ? $seoDetail->meta_description : '',
            'abs_site_url'    => $this->abs_site_url,
            'rel_site_url'    => $this->rel_site_url,
            'site_name'       => Configuration::getValueFromName('site_name'),
            'subdomain'       => $subdomain,
            'officeData'      => $officeData,
            'captcha' => $captcha
        ];

        return view('home.login_request', $d);
    }

    public function postLoginCredentialPage(LoginCredentialRequest $request)
    {
        if (Session::get('phrase') != $request->get('captcha')) {
            return redirect()->back()->withInput($request->all())->withErrors(['captcha' => 'Invalid captcha image verification.']);
        } else {
            LoginRequest::create([
                'full_name' => $request->full_name,
                'designation' => $request->designation,
                'email' => $request->email,
                'office_name' => $request->office_name,
                'office_address' => $request->office_address,
                'office_phone' => $request->office_phone,
                'is_email_login' => $request->is_email_login ? 1 : 0,
                'is_web_login' => $request->is_web_login ? 1 : 0,
                'is_circular_login' => $request->is_circular_login ? 1 : 0,
            ]);

            //send email to admin
            $subject = "Login credential requested ";

            $home = '<table cellspacing="0" cellpadding="0" width="100%">';
            $home .= '<tr><td><p>Dear <strong>admin,</strong> <br />';
            $home .= '<br />A visitor <strong>' . $request->get('full_name') . '</strong> has requested login credentials with following details.</td>';
            $home .= '</table>';
            $home .= '<table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;margin-top: 20px;">';
            $home .= '<tr><td width="150">Full Name :</td><td>' . $request->get('full_name') . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Designation :</td><td>' . $request->get('designation') . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Email Address :</td><td>' . $request->get('email') . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Office Name :</td><td>' . $request->get('office_name') . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Office Address :</td><td>' . $request->get('office_address') . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Office Phone:</td><td>' . $request->get('office_phone') . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Email Login :</td><td>' . ($request->is_email_login ? 'Yes' : 'No') . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Web Login :</td><td>' . ($request->is_web_login ? 'Yes' : 'No') . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Circular Login :</td><td>' . ($request->is_circular_login ? 'Yes' : 'No') . '</td></tr>';
            $home .= '</table>';
            $home .= '<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">';
            $home .= '<tr><td><p>Kind Regards,</strong> <br />';
            $home .= $this->site_name . '</td>';
            $home .= '</table>';

            $adminEmail = Configuration::getValueFromName('admin_email');
            $officeData = Office::getOfficeDetailFromSubdomain('home', 2);
            $toEmail = ($officeData->office_email_address) ? $officeData->office_email_address : $adminEmail;

            //see if email sending is in test mode
            $is_email_test_mode = config('general.email.is_test_mode');
            if ($is_email_test_mode) $toEmail = config('general.email.test_email_to');

            // dd($toEmail);

            $email = Helper::sendEmail($toEmail, $subject, $home);

            Session::flash('class', 'alert alert-success');
            Session::flash('message', 'Your login request has been sent successfully. We will do the needful as soon as possible. Thank you.');
            return redirect()->back();
        }
    }

    public function siteMap($subdomain = 'home')
    {
        $officeId = Office::getIdfromSubdomain($subdomain);
        if (!$officeId) {
            abort(404);
        }

        $languageId    = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        $officeData = Office::getOfficeDetailFromSubdomain($subdomain, $sessionLangId);
        $seoDetail  = Seo::getSeoDetailsByPageNameAndLanguage('login credentials', $sessionLangId);

        $captcha   = Helper::create_captcha();

        $d = [
            'pageTitle'       => $seoDetail ? $seoDetail->page_title : '',
            'metaDescription' => $seoDetail ? $seoDetail->meta_tags : '',
            'metaKeywords'    => $seoDetail ? $seoDetail->meta_description : '',
            'abs_site_url'    => $this->abs_site_url,
            'rel_site_url'    => $this->rel_site_url,
            'site_name'       => Configuration::getValueFromName('site_name'),
            'subdomain'       => $subdomain,
            'officeData'      => $officeData,
            'captcha' => $captcha
        ];

        return view('home.sitemap', $d);
    }
}

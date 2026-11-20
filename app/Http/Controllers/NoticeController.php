<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Notice;
use App\Models\Office;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;

class NoticeController extends Controller
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
	* get notice page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActiveNotices($subdomain, Request $request)
    {
        // dd($subdomain);
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        // dd($sessionLangId);
        Session::put('language_id', $sessionLangId);

        if ($officeId > 0) {
            $office_name = $request->get('office_name');
            $from = $request->get('from');
            $to = $request->get('to');

            if ($subdomain === 'home') {
                $term = trim(preg_replace('/\\s+/u', ' ', (string) $request->get('term', $office_name)));

                $selectedOfficeId = null;
                if ($request->has('office_id')) {
                    $requestedOfficeId = filter_var($request->get('office_id'), FILTER_VALIDATE_INT);

                    $selectedOfficeId = ($requestedOfficeId && DB::table('office')
                        ->where('id', $requestedOfficeId)
                        ->where('is_active', '1')
                        ->where('is_deleted', '0')
                        ->exists()) ? $requestedOfficeId : -1;
                }

                $notices = Notice::getActiveNoticeSearchResults(
                    $term,
                    $selectedOfficeId,
                    $sessionLangId,
                    $from,
                    $to
                );

                $didYouMean = ($term !== '' && $notices->total() === 0)
                    ? Notice::getNoticeSearchSpellingSuggestion($term)
                    : null;
            } else {
                $term = trim(preg_replace('/\\s+/u', ' ', (string) $request->get('term', '')));
                $notices = Notice::getActiveNoticeSearchResults(
                    $term,
                    $officeId,
                    $sessionLangId,
                    $from,
                    $to
                );
            }

            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('notices', $sessionLangId);
            return view(
                'notice.notices',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'notices' => $notices,
                    'didYouMean' => $didYouMean ?? null
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
    /*
	* get notice page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActiveOfficeNotices($subdomain, Request $request)
    {
        // print_r($subdomain);exit;
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $office_name = $request->get('office_name');

            $from = $request->get('from');
            $to = $request->get('to');
            if ($subdomain === 'home') {
                $office_id = Office::getofficeId($office_name);
                $notices = Notice::getActiveNoticesSearch($office_id, $sessionLangId, $from, $to);
            } else {
                $term = trim(preg_replace('/\\s+/u', ' ', (string) $request->get('term', '')));
                $notices = Notice::getActiveNoticeSearchResults(
                    $term,
                    $officeId,
                    $sessionLangId,
                    $from,
                    $to
                );
            }
            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('notices', $sessionLangId);
            return view(
                'notice.notices',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'notices' => $notices,
                    'office_name' => $office_name,

                )
            );
            // }
        } catch (Throwable $e) {
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view(
                'notice.notices',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name
                )
            );
        }
    }

    /*
	* get notice page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getNoticeDetail($subdomain, $url)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        // dd($officeId);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $noticeData    = Notice::getNoticeDetail($officeId, $url, $sessionLangId);
            // dd($noticeData);
            if ($noticeData) {
                return view(
                    'notice.notice-detail',
                    array(
                        'pageTitle' => $noticeData->notice_page_title,
                        'metaDescription' => $noticeData->notice_meta_tags,
                        'metaKeywords' => $noticeData->notice_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'noticeData' => $noticeData
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
            dd($e->getMessage());
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
	* get notice page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getNoticeDetailById($subdomain, $id)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $noticeData    = Notice::getNoticeDetailById($officeId, $id, $sessionLangId);
            //echo Configuration::get_last_query(); die();

            if ($noticeData) {
                return view(
                    'notice.notice-detail',
                    array(
                        'pageTitle' => $noticeData->notice_page_title,
                        'metaDescription' => $noticeData->notice_meta_tags,
                        'metaKeywords' => $noticeData->notice_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'noticeData' => $noticeData
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
	* force download notice attachment
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getForceDownload($subdomain, $url)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();

        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $noticeData    = Notice::getNoticeDetail($officeId, $url, $sessionLangId);
            if (
                $noticeData &&
                !empty($noticeData->notice_attachment) &&
                basename($noticeData->notice_attachment) === $noticeData->notice_attachment &&
                is_file(public_path() . '/uploads/notice/' . $noticeData->notice_attachment) &&
                is_readable(public_path() . '/uploads/notice/' . $noticeData->notice_attachment)
            ) {
                $file = public_path() . '/uploads/notice/' . $noticeData->notice_attachment;
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                $fileName = $noticeData->notice_heading . '.' . $ext;
                $fileName = str_replace("/", '-', $fileName);
                //echo $file;exit;
                DB::table('notice')
                    ->where('id', $noticeData->id)
                    ->increment('download_count', 1);

                $headers = array(
                    'Content-Type: application/' . $ext,
                );
                $response = Response::download($file, $fileName);
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                return  $response;
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
	* force download notice attachment
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/

    /**
     * Search active offices and notices for Notice page suggestions.
     */
    public function getNoticeSearch($subdomain, Request $request)
    {
        $officeId = Office::getIdfromSubdomain($subdomain);
        if (!$officeId) {
            return response()->json([]);
        }

        $term = trim((string) $request->get('term', ''));
        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $languageId = Session::get('language_id') ?: Language::getIdFromCode();
        $suggestionOfficeId = $subdomain === 'home' ? null : $officeId;

        $results = Notice::getActiveNoticeSearchSuggestions(
            $term,
            $languageId,
            10,
            $suggestionOfficeId
        );

        $suggestions = [];

        foreach ($results['offices'] as $office) {
            if ($subdomain !== 'home') {
                break;
            }

            $suggestions[] = [
                'type' => 'office',
                'label' => $office['office_name'] . ' (कार्यालय)',
                'value' => $office['office_name'],
                'office_id' => $office['id'],
                'url' => '/' . $subdomain . '/notices?office_id=' . $office['id'],
            ];
        }

        foreach ($results['notices'] as $notice) {
            $targetSubdomain = $subdomain === 'home'
                ? $notice->office_subdomain
                : $subdomain;

            $suggestions[] = [
                'type' => 'notice',
                'label' => $notice->notice_heading . ' — ' . $notice->office_name,
                'value' => $notice->notice_heading,
                'office_id' => $notice->office_id,
                'url' => '/' . $targetSubdomain . '/notice/' . $notice->notice_url,
            ];
        }

        $spellingSuggestion = $subdomain === 'home'
            ? Notice::getNoticeSearchSpellingSuggestion($term)
            : null;

        if ($spellingSuggestion !== null) {
            $suggestions[] = [
                'type' => 'suggestion',
                'label' => 'Did you mean: ' . $spellingSuggestion . '?',
                'value' => $spellingSuggestion,
                'url' => '/' . $subdomain . '/notices?term=' . urlencode($spellingSuggestion),
            ];
        }

        return response()->json($suggestions);
    }

    public function getOfficesSearch(Request $request)
    {
        $Officearray =  array();
        $term =  $request->get('term');
        // print_r($term);exit;
        // $term =  $request->get('term');
        $officeData = Office::getActiveSearchOffices($term);
        foreach ($officeData as $value) {
            $Officearray[] = $value->office_name;
        }
        // echo "<pre>";print_r(json_encode($Officearray));exit;
        echo json_encode($Officearray);
    }
}

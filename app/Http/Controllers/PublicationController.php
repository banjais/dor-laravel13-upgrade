<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Office;
use App\Models\Publication;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Throwable;

class PublicationController extends Controller
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
	* get category list page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActiveCategories($subdomain)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        if ($officeId > 0) {
            $categories = Category::getActiveCategories($officeId, $sessionLangId);
            $publicationCount = (int) $officeId !== 4
                ? Publication::getActivePublicationCount($officeId)
                : null;

            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('publications', $sessionLangId);

            return view(
                'publication.categories',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'languageId' => $sessionLangId,
                    'categories' => $categories,
                    'publicationCount' => $publicationCount
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
	* get publication page according to office
	* @param string $subdomain
	* @return Illuminate\View\View
	*/
    public function getActivePublications($subdomain, $url)
    {
        // dd($url);
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);
        $categoryId = Category::getIdfromUrl($officeId, $url);
        // dd($officeId);

        if ($officeId > 0 && $categoryId > 0) {
            $publications    = Publication::getActivePublications($officeId, $sessionLangId, $categoryId);
            // dd($publications);
            //echo Helper::get_last_query(); die();
            $publicationCategory = Category::getCategoryName($officeId, $sessionLangId, $url);

            $seoDetail    = Seo::getSeoDetailsByPageNameAndLanguage('publications', $sessionLangId);
            return view(
                'publication.publications',
                array(
                    'pageTitle' => $seoDetail->page_title,
                    'metaDescription' => $seoDetail->meta_tags,
                    'metaKeywords' => $seoDetail->meta_description,
                    'abs_site_url' => $this->abs_site_url,
                    'rel_site_url' => $this->rel_site_url,
                    'site_name' => $this->site_name,
                    'subdomain' => $subdomain,
                    'publications' => $publications,
                    'publicationCategory' => $publicationCategory,
                    'category_url' => $url
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
	* get publication page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getPublicationDetail($subdomain, $categoryUrl, $url)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $publicationData = Publication::getPublicationDetail($officeId, $url, $sessionLangId);
            $publicationCategory = Category::getCategoryName($officeId, $sessionLangId, $categoryUrl);
            //$publicationCategoryUrl = Category::getCategoryName($officeId, $sessionLangId, $categoryUrl);
            //echo Helper::get_last_query(); die();

            if ($publicationData) {
                return view(
                    'publication.publication-detail',
                    array(
                        'pageTitle' => $publicationData->publication_page_title,
                        'metaDescription' => $publicationData->publication_meta_tags,
                        'metaKeywords' => $publicationData->publication_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'categoryUrl' => $categoryUrl,
                        'publicationData' => $publicationData,
                        'publicationCategory' => $publicationCategory
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
	* get publication page according to office
	* @param string $subdomain
	* @param string $url
	* @return Illuminate\View\View
	*/
    public function getPublicationDetailById($subdomain, $id)
    {
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $publicationData = Publication::getPublicationDetailById($officeId, $id, $sessionLangId);

            if ($publicationData) {
                return view(
                    'publication.publication-detail',
                    array(
                        'pageTitle' => $publicationData->publication_page_title,
                        'metaDescription' => $publicationData->publication_meta_tags,
                        'metaKeywords' => $publicationData->publication_meta_description,
                        'abs_site_url' => $this->abs_site_url,
                        'rel_site_url' => $this->rel_site_url,
                        'site_name' => $this->site_name,
                        'subdomain' => $subdomain,
                        'publicationData' => $publicationData
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

    /**
     * Search publication categories and documents from the Main Office homepage.
     */
    public function getPublicationSearch(Request $request, $subdomain)
    {
        $officeId = Office::getIdfromSubdomain($subdomain);

        if (!$officeId) {
            return Response::json([]);
        }

        $term = trim((string) $request->get('term', ''));

        if (mb_strlen($term) < 1) {
            return Response::json([]);
        }

        $like = '%' . $term . '%';
        $normalizedTerm = str_replace(['-', '_'], ' ', $term);
        $normalizedLike = '%' . $normalizedTerm . '%';
        $neId = Language::getIdFromCode('ne');
        $enId = Language::getIdFromCode('en');
        $sessionLangId = Session::get('language_id') ?: $neId;

        $preferred = (int) $sessionLangId === (int) $enId ? 'en' : 'ne';
        $fallback = $preferred === 'en' ? 'ne' : 'en';

        $categories = DB::table('publication_category as c')
            ->join('office as o', 'o.id', '=', 'c.office_id')
            ->where('c.office_id', $officeId)
            ->leftJoin('publication_category_detail as cn', function ($join) use ($neId) {
                $join->on('cn.category_id', '=', 'c.id')
                    ->where('cn.language_id', '=', $neId);
            })
            ->leftJoin('publication_category_detail as ce', function ($join) use ($enId) {
                $join->on('ce.category_id', '=', 'c.id')
                    ->where('ce.language_id', '=', $enId);
            })
            ->where('c.is_active', '1')
            ->where('c.is_deleted', '0')
            ->where('o.is_active', '1')
            ->where('o.is_deleted', '0')
            ->where(function ($query) use ($like, $normalizedLike) {
                $query->where('cn.category_heading', 'like', $like)
                    ->orWhere('ce.category_heading', 'like', $like)
                    ->orWhere('cn.category_page_title', 'like', $like)
                    ->orWhere('ce.category_page_title', 'like', $like)
                    ->orWhere('c.category_url', 'like', $like)
                    ->orWhereRaw("REPLACE(REPLACE(pndc_c.category_url, '-', ' '), '_', ' ') LIKE ?", [$normalizedLike]);
            })
            ->select(
                'c.category_url',
                'o.office_subdomain',
                'cn.category_heading as ne_heading',
                'ce.category_heading as en_heading'
            )
            ->limit(10)
            ->get();

        $results = [];

        foreach ($categories as $category) {
            $heading = $preferred === 'en'
                ? ($category->en_heading ?: $category->ne_heading)
                : ($category->ne_heading ?: $category->en_heading);

            if (!$heading) {
                continue;
            }

            $results[] = [
                'label' => $heading,
                'value' => $heading,
                'url' => '/' . $category->office_subdomain
                    . '/publication/' . $category->category_url,
                'type' => 'category',
            ];
        }

        $publications = DB::table('publication as p')
            ->join('publication_category as c', function ($join) {
                $join->on('c.id', '=', 'p.category_id')
                    ->on('c.office_id', '=', 'p.office_id');
            })
            ->join('office as o', 'o.id', '=', 'p.office_id')
            ->where('p.office_id', $officeId)
            ->leftJoin('publication_detail as pn', function ($join) use ($neId) {
                $join->on('pn.publication_id', '=', 'p.id')
                    ->where('pn.language_id', '=', $neId);
            })
            ->leftJoin('publication_detail as pe', function ($join) use ($enId) {
                $join->on('pe.publication_id', '=', 'p.id')
                    ->where('pe.language_id', '=', $enId);
            })
            ->leftJoin('publication_category_detail as cn', function ($join) use ($neId) {
                $join->on('cn.category_id', '=', 'c.id')
                    ->where('cn.language_id', '=', $neId);
            })
            ->leftJoin('publication_category_detail as ce', function ($join) use ($enId) {
                $join->on('ce.category_id', '=', 'c.id')
                    ->where('ce.language_id', '=', $enId);
            })
            ->where('p.is_active', '1')
            ->where('p.is_deleted', '0')
            ->where('c.is_active', '1')
            ->where('c.is_deleted', '0')
            ->where('o.is_active', '1')
            ->where('o.is_deleted', '0')
            ->where(function ($query) use ($like, $normalizedLike) {
                $query->where('pn.publication_heading', 'like', $like)
                    ->orWhere('pe.publication_heading', 'like', $like)
                    ->orWhere('pn.publication_page_title', 'like', $like)
                    ->orWhere('pe.publication_page_title', 'like', $like)
                    ->orWhere('pn.publication_description', 'like', $like)
                    ->orWhere('pe.publication_description', 'like', $like)
                    ->orWhere('pn.publication_short_description', 'like', $like)
                    ->orWhere('pe.publication_short_description', 'like', $like)
                    ->orWhere('p.publication_attachment', 'like', $like)
                    ->orWhere('p.publication_url', 'like', $like)
                    ->orWhere('c.category_url', 'like', $like)
                    ->orWhere('cn.category_heading', 'like', $like)
                    ->orWhere('ce.category_heading', 'like', $like)
                    ->orWhere('cn.category_page_title', 'like', $like)
                    ->orWhere('ce.category_page_title', 'like', $like)
                    ->orWhereRaw("CONCAT(pndc_c.category_url, '/', pndc_p.publication_url) LIKE ?", [$like])
                    ->orWhereRaw("REPLACE(REPLACE(pndc_p.publication_url, '-', ' '), '_', ' ') LIKE ?", [$normalizedLike])
                    ->orWhereRaw("REPLACE(REPLACE(pndc_c.category_url, '-', ' '), '_', ' ') LIKE ?", [$normalizedLike]);
            })
            ->select(
                'p.publication_url',
                'c.category_url',
                'o.office_subdomain',
                'pn.publication_heading as ne_heading',
                'pe.publication_heading as en_heading',
                'pn.publication_page_title as ne_page_title',
                'pe.publication_page_title as en_page_title',
                'cn.category_heading as ne_category',
                'ce.category_heading as en_category'
            )
            ->orderByRaw(
                'CASE
                    WHEN pndc_pn.publication_heading LIKE ?
                      OR pndc_pe.publication_heading LIKE ?
                      OR pndc_pn.publication_page_title LIKE ?
                      OR pndc_pe.publication_page_title LIKE ?
                    THEN 0 ELSE 1 END',
                [$like, $like, $like, $like]
            )
            ->orderBy('p.published_date', 'desc')
            ->limit(max(0, 20 - count($results)))
            ->get();

        foreach ($publications as $publication) {
            $heading = $preferred === 'en'
                ? ($publication->en_heading ?: $publication->ne_heading)
                : ($publication->ne_heading ?: $publication->en_heading);

            if (!$heading) {
                $heading = $preferred === 'en'
                    ? ($publication->en_page_title ?: $publication->ne_page_title)
                    : ($publication->ne_page_title ?: $publication->en_page_title);
            }

            if (!$heading) {
                continue;
            }

            $categoryHeading = $preferred === 'en'
                ? ($publication->en_category ?: $publication->ne_category)
                : ($publication->ne_category ?: $publication->en_category);

            $label = $categoryHeading
                ? $heading . ' — ' . $categoryHeading
                : $heading;

            $results[] = [
                'label' => $label,
                'value' => $heading,
                'url' => '/' . $publication->office_subdomain
                    . '/publication/' . $publication->category_url
                    . '/' . $publication->publication_url,
                'type' => 'publication',
            ];
        }

        // Show one safe spelling suggestion only when exact search has no results.
        if (empty($results)
            && preg_match('/^[\p{L}\p{M}]{3,30}$/u', $term)
        ) {
            $candidateTexts = [];

            $publicationTitles = DB::table('publication_detail as pd')
                ->join('publication as p', 'p.id', '=', 'pd.publication_id')
                ->where('p.office_id', $officeId)
                ->where('p.is_active', '1')
                ->where('p.is_deleted', '0')
                ->whereIn('pd.language_id', [$neId, $enId])
                ->select('pd.publication_heading', 'pd.publication_page_title')
                ->get();

            $categoryTitles = DB::table('publication_category_detail as cd')
                ->join('publication_category as c', 'c.id', '=', 'cd.category_id')
                ->where('c.office_id', $officeId)
                ->where('c.is_active', '1')
                ->where('c.is_deleted', '0')
                ->whereIn('cd.language_id', [$neId, $enId])
                ->select('cd.category_heading', 'cd.category_page_title')
                ->get();

            foreach ($publicationTitles as $record) {
                $candidateTexts[] = $record->publication_heading;
                $candidateTexts[] = $record->publication_page_title;
            }

            foreach ($categoryTitles as $record) {
                $candidateTexts[] = $record->category_heading;
                $candidateTexts[] = $record->category_page_title;
            }

            $candidateWords = [];

            foreach ($candidateTexts as $candidateText) {
                $words = preg_split(
                    '/[^\p{L}\p{M}]+/u',
                    mb_strtolower((string) $candidateText, 'UTF-8'),
                    -1,
                    PREG_SPLIT_NO_EMPTY
                );

                foreach ($words as $word) {
                    if (mb_strlen($word, 'UTF-8') >= 3) {
                        $candidateWords[$word] = true;
                    }
                }
            }

            $searchWord = mb_strtolower($term, 'UTF-8');
            $searchChars = preg_split('//u', $searchWord, -1, PREG_SPLIT_NO_EMPTY);

            $unicodeDistance = function ($left, $right) {
                $a = preg_split('//u', $left, -1, PREG_SPLIT_NO_EMPTY);
                $b = preg_split('//u', $right, -1, PREG_SPLIT_NO_EMPTY);

                // Treat two adjacent swapped characters as one typo.
                if (count($a) === count($b)) {
                    $mismatches = [];

                    foreach ($a as $i => $char) {
                        if ($char !== $b[$i]) {
                            $mismatches[] = $i;
                        }
                    }

                    if (count($mismatches) === 2) {
                        $first = $mismatches[0];
                        $second = $mismatches[1];

                        if ($second === $first + 1
                            && $a[$first] === $b[$second]
                            && $a[$second] === $b[$first]
                        ) {
                            return 1;
                        }
                    }
                }

                $previous = range(0, count($b));

                foreach ($a as $i => $charA) {
                    $current = [$i + 1];

                    foreach ($b as $j => $charB) {
                        $current[$j + 1] = min(
                            $current[$j] + 1,
                            $previous[$j + 1] + 1,
                            $previous[$j] + ($charA === $charB ? 0 : 1)
                        );
                    }

                    $previous = $current;
                }

                return $previous[count($b)];
            };

            $bestDistance = 2;
            $bestMatches = [];

            foreach (array_keys($candidateWords) as $candidateWord) {
                $candidateLength = mb_strlen($candidateWord, 'UTF-8');

                if (abs($candidateLength - count($searchChars)) > 1) {
                    continue;
                }

                $distance = $unicodeDistance($searchWord, $candidateWord);

                if ($distance !== 1) {
                    continue;
                }

                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $bestMatches = [$candidateWord => true];
                } elseif ($distance === $bestDistance) {
                    $bestMatches[$candidateWord] = true;
                }
            }

            if (count($bestMatches) === 1) {
                $correctedTerm = array_key_first($bestMatches);

                $results[] = [
                    'type' => 'suggestion',
                    'label' => $preferred === 'en'
                        ? 'Did you mean: ' . $correctedTerm . '?'
                        : 'के तपाईंले ' . $correctedTerm . ' खोज्नुभएको हो?',
                    'value' => $correctedTerm,
                ];
            }
        }

        return Response::json($results);
    }

    public function getForceDownload($subdomain, $categoryUrl, $url)
    {
        // dd(public_path());
        // dd($subdomain);
        $officeId    = Office::getIdfromSubdomain($subdomain);
        $languageId = Language::getIdFromCode();
        $sessionLangId = (Session::get('language_id')) ? Session::get('language_id') : $languageId;
        Session::put('language_id', $sessionLangId);

        try {
            $publicationData = Publication::getPublicationDetail($officeId, $url, $sessionLangId);

            if (
                $publicationData &&
                !empty($publicationData->publication_attachment) &&
                basename($publicationData->publication_attachment) === $publicationData->publication_attachment &&
                is_file(public_path() . '/uploads/publication/' . $publicationData->publication_attachment) &&
                is_readable(public_path() . '/uploads/publication/' . $publicationData->publication_attachment)
            ) {
                $file = public_path() . '/uploads/publication/' . $publicationData->publication_attachment;
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $safeHeading = str_replace(array('/', '\\'), '-', $publicationData->publication_heading);
                $fileName = trim($safeHeading) . '.' . $ext;

                DB::table('publication')
                    ->where('id', $publicationData->id)
                    ->increment('download_count', 1);

                $mimeType = mime_content_type($file) ?: 'application/octet-stream';
                $inlineExtensions = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp');

                if (in_array(strtolower($ext), $inlineExtensions, true)) {
                    return Response::file($file, array(
                        'Content-Type' => $mimeType,
                    ));
                }

                return Response::download($file, $fileName, array(
                    'Content-Type' => $mimeType,
                ));
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
}

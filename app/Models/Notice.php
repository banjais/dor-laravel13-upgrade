<?php

namespace App\Models;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notice extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'notice';

    public function office()
    {
        return $this->belongsTo('Office', 'office_id');
    }

    public function details()
    {
        return $this->hasMany(Noticedetail::class, 'notice_id');
    }

    /**
     * get all undeleted notices
     * @return object
     */
    public static function getUndeletedNotices()
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.notice_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.notice_heading
						from pndc_notice t1
						join pndc_notice_detail t2 on t1.id=t2.notice_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted notices office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedNoticesByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.notice_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.notice_heading
						from pndc_notice t1
						join pndc_notice_detail t2 on t1.id=t2.notice_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' and t1.office_id='$officeId' ";

        return DB::select($query);
    }

    /**
     * check whether url exist or not for individual office
     * @param  string $url
     * @param  int $officeId
     * @return int
     */
    public static function checkExist($url, $officeId)
    {
        return Notice::where('is_deleted', '0')
            ->where('notice_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get notice detail with languages
     * @param  int $id
     * @return object
     */
    public static function getNoticeDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_notice t2 where t2.id='$id') as notice_id,
					(select notice_heading from pndc_notice t2 join pndc_notice_detail t3
							on t2.id=t3.notice_id where t3.language_id=t1.id and t2.id='$id') as notice_heading,
					(select notice_description from pndc_notice t2 join pndc_notice_detail t3
							on t2.id=t3.notice_id where t3.language_id=t1.id and t2.id='$id') as notice_description,
					(select notice_page_title from pndc_notice t2 join pndc_notice_detail t3
							on t2.id=t3.notice_id where t3.language_id=t1.id and t2.id='$id') as notice_page_title,
					(select t3.id from pndc_notice t2 join pndc_notice_detail t3
							on t2.id=t3.notice_id where t3.language_id=t1.id and t2.id='$id') as notice_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active notices
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveNotices($officeId, $langId)
    {
        $paginate = Configuration::getValueFromName('per_page_notices');
        return Notice::with('office')->join('notice_detail', 'notice.id', '=', 'notice_detail.notice_id')
            ->select(
                'notice.id',
                'notice_url',
                'notice_attachment',
                'notice_detail.notice_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('notice.office_id', $officeId)
            ->where('notice_detail.language_id', $langId)
            ->where('notice.is_active', '1')
            ->where('notice.is_deleted', '0')
            ->paginate($paginate);
    }
    /*----------------------------------------------------*/

    /**
     * get all active notices
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveNoticesSearch($officeId, $langId, $expiry_from = '', $expiry_to = '')
    {
        $limit   = Configuration::getValueFromName('per_page_notices');
        $notices = Notice::join('notice_detail', 'notice.id', '=', 'notice_detail.notice_id')
            ->select(
                'notice.id',
                'notice_url',
                'notice_attachment',
                'notice_detail.notice_heading',
                'published_date',
                'office_id',
                DB::raw('
						date_format(published_date, "%d %b, %Y") as en_published_date
					')
            )
            ->where('notice_detail.language_id', $langId)
            ->where('notice.is_active', '1')
            ->where('notice.is_deleted', '0');

        ($officeId > 0) ? $notices->where('notice.office_id', $officeId) : '';
        if ($expiry_to > 0 && $expiry_from > 0 && $expiry_to >= $expiry_from) {
            $notices->whereBetween('published_date', array($expiry_from, $expiry_to));
        }

        return $notices->orderby('notice.published_date', 'desc')->paginate($limit);
    }
    /*----------------------------------------------------*/

    /**
     * get all active notices
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveNoticesWithoutOffices($officeId, $langId, $office_name = null, $from = null, $to = null)
    {
        $paginate = Configuration::getValueFromName('per_page_notices');
        $q = Notice::join('notice_detail', 'notice.id', '=', 'notice_detail.notice_id')
            ->join('office', 'office.id', '=', 'notice.office_id')
            ->join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.office_subdomain',
                'notice.id',
                'notice_url',
                'notice_attachment',
                'notice_detail.notice_heading',
                'published_date',
                'notice.office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('notice.office_id', $officeId)
            ->where('notice_detail.language_id', $langId)
            ->where('office_detail.language_id', $langId)
            ->where('notice.is_active', '1')
            ->where('notice.is_deleted', '0');

        if ($office_name) {
            $q->where('office_detail.office_name', $office_name);
        }
        if ($from) {
            $q->where('notice.published_date', '>=', $from);
        }
        if ($to) {
            $q->where('notice.published_date', '<=', $to);
        }
        // dd();
        return $q->orderby('notice.published_date', 'desc')
            ->paginate($paginate);
    }

    /*----------------------------------------------------*/

    /**
     * get all active notices
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveNoticesWithoutOfficestest($officeId, $langId)
    {
        $paginate = Configuration::getValueFromName('per_page_notices');
        return Notice::join('notice_detail', 'notice.id', '=', 'notice_detail.notice_id')
            ->join('office', 'office.id', '=', 'notice.office_id')
            ->select(
                'office.office_subdomain',
                'notice.id',
                'notice_url',
                'notice_attachment',
                'notice_detail.notice_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									'),
                DB::raw('select office_subdomain from pndc_office where id=notice.office_id as offices_url')


            )
            // ->where('notice.office_id', $officeId)
            ->where('notice_detail.language_id', $langId)
            ->where('notice.is_active', '1')
            ->where('notice.is_deleted', '0')
            ->orderby('notice.published_date', 'desc')
            ->paginate($paginate);
    }

    /**
     * get active notices home page
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getMainHomePageNotices($officeId, $langId)
    {
        $limit = Configuration::getValueFromName('home_page_news');
        return Notice::join('notice_detail', 'notice.id', '=', 'notice_detail.notice_id')
            ->select(
                'notice.id',
                'notice.notice_url',
                'notice_attachment',
                'notice_detail.notice_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            // ->where('notice.office_id', $officeId)
            ->where('notice_detail.language_id', $langId)
            ->where('notice.is_active', '1')
            ->where('notice.is_deleted', '0')
            ->orderby('notice.created_at', 'DESC')
            ->take(3)
            ->get();
    }
    /**
     * get active notices home page
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageNotices($officeId, $langId, $limit = null)
    {
        if (!$limit)
            $limit = Configuration::getValueFromName('home_page_news');
        $q = Notice::join('notice_detail', 'notice.id', '=', 'notice_detail.notice_id')
            ->select(
                'notice.id',
                'notice.notice_url',
                'notice_attachment',
                'notice_detail.notice_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )

            ->where('notice_detail.language_id', $langId);

        if ($officeId != 4) {
            $q = $q->where('notice.office_id', $officeId);
        }

        $q = $q->where('notice.is_active', '1')
            ->where('notice.is_deleted', '0')
            ->orderby('notice.published_date', 'DESC');

        return $q->take($limit)
            ->get();
    }

    /**
     * get notice detail by url
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getNoticeDetail($officeId, $url, $langId)
    {
        $notice =  Notice::join('notice_detail', 'notice.id', '=', 'notice_detail.notice_id')
            ->select(
                'notice.id',
                'notice.notice_url',
                'notice.notice_meta_tags',
                'notice.notice_meta_description',
                'notice_attachment',
                'notice_detail.notice_heading',
                'notice_detail.notice_page_title',
                'notice_detail.notice_description',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('notice.office_id', $officeId)
            ->where('notice.notice_url', $url)
            ->where('notice_detail.language_id', $langId)
            ->where('notice.is_active', '1')
            ->where('notice.is_deleted', '0')
            ->first();
        // dd($notice);
        return $notice;
    }

    /**
     * get notice detail by id
     * @param int $officeId
     * @param int $id
     * @param int $langId
     * @return object
     */
    public static function getNoticeDetailById($officeId, $id, $langId)
    {
        return Notice::join('notice_detail', 'notice.id', '=', 'notice_detail.notice_id')
            ->select(
                'notice.id',
                'notice.notice_url',
                'notice.notice_meta_tags',
                'notice.notice_meta_description',
                'notice_attachment',
                'notice_detail.notice_heading',
                'notice_detail.notice_page_title',
                'notice_detail.notice_description',
                'published_date'
            )
            ->where('notice.office_id', $officeId)
            ->where('notice.id', $id)
            ->where('notice_detail.language_id', $langId)
            ->where('notice.is_active', '1')
            ->where('notice.is_deleted', '0')
            ->first();
    }

    /**
     * get content list hierarchy wise
     * @param  int $parentId
     * @param  int $level
     * @param  int $selected
     * @return string
     */
    public static function group_list_for_newsletterEntry()
    {

        $offices      = Office::getUndeletedOffices();
        $office_types = array(
            '2' => 'Regional',
            '3' => 'Division',
            '4' => 'Heavy Equipment Division',
            '5' => 'Mechanical office',
        );
        // echo "<pre>";print_r($offices);exit;
        $returnList = '<select name="email_list[]" data-placeholder="Select offices" class="span12" tabindex="-1" id="enableClickableOptGroups" multiple="multiple" required>';
        foreach ($office_types as $key => $type) {
            $returnList .= '<optgroup label="' . $type . '" class="optgroup">';
            foreach ($offices as $office) {
                $types = explode(',', $office->office_type);
                if (in_array($key, $types)) {
                    $returnList .= '<option value="' . $office->office_email_address . '" >' . $office->office_name . '</option>';
                }
            }
            $returnList .= '</optgroup>';
        }
        $returnList .= '</select>';

        return $returnList;
    }

    /**
     * Get paginated active notices matching office name or notice content.
     */
    /**
     * Get paginated active notices matching office name or notice content.
     * Searches the normalized Notice Search Index.
     */
    public static function getActiveNoticeSearchResults($term, $officeId, $langId, $from = null, $to = null)
    {
        $paginate = Configuration::getValueFromName('per_page_notices');
        $term = trim(preg_replace('/\s+/u', ' ', (string) $term));

        $normalizedTerm = $term;

        if (!mb_check_encoding($normalizedTerm, 'UTF-8')) {
            $normalizedTerm = iconv('UTF-8', 'UTF-8//IGNORE', $normalizedTerm) ?: '';
        }

        if (class_exists(\Normalizer::class)) {
            $normalizedTerm = \Normalizer::normalize($normalizedTerm, \Normalizer::FORM_C) ?: $normalizedTerm;
        }

        $normalizedTerm = mb_strtolower($normalizedTerm, 'UTF-8');
        $normalizedTerm = preg_replace('/[^\p{L}\p{M}\p{N}]+/u', ' ', $normalizedTerm);
        $normalizedTerm = trim(preg_replace('/\s+/u', ' ', $normalizedTerm));

        $neId = Language::getIdFromCode('ne');
        $enId = Language::getIdFromCode('en');
        $prefix = DB::getTablePrefix();

        $q = DB::table('notice as n')
            ->join('office as o', 'o.id', '=', 'n.office_id')
            ->leftJoin(DB::raw($prefix . 'notice_detail as ' . $prefix . 'nd_ne'), function ($join) use ($neId) {
                $join->on('nd_ne.notice_id', '=', 'n.id')
                    ->where('nd_ne.language_id', '=', $neId);
            })
            ->leftJoin(DB::raw($prefix . 'notice_detail as ' . $prefix . 'nd_en'), function ($join) use ($enId) {
                $join->on('nd_en.notice_id', '=', 'n.id')
                    ->where('nd_en.language_id', '=', $enId);
            })
            ->leftJoin(DB::raw($prefix . 'office_detail as ' . $prefix . 'od_ne'), function ($join) use ($neId) {
                $join->on('od_ne.office_id', '=', 'o.id')
                    ->where('od_ne.language_id', '=', $neId);
            })
            ->leftJoin(DB::raw($prefix . 'office_detail as ' . $prefix . 'od_en'), function ($join) use ($enId) {
                $join->on('od_en.office_id', '=', 'o.id')
                    ->where('od_en.language_id', '=', $enId);
            })
            ->where('n.is_active', '1')
            ->where('n.is_deleted', '0')
            ->where('o.is_active', '1')
            ->where('o.is_deleted', '0');

        if ($officeId) {
            $q->where('n.office_id', $officeId);
        }

        if ($term !== '') {
            if ($normalizedTerm === '') {
                $q->whereRaw('1 = 0');
            } else {
                $like = '%' . $normalizedTerm . '%';

                $q->whereExists(function ($subquery) use ($like) {
                    $subquery->select(DB::raw('1'))
                        ->from('notice_search_indexes as si')
                        ->whereColumn('si.notice_id', 'n.id')
                        ->where('si.normalized_text', 'like', $like);
                });
            }
        }

        if ($from) {
            $q->where('n.published_date', '>=', $from);
        }

        if ($to) {
            $q->where('n.published_date', '<=', $to);
        }

        $preferredNotice = $langId == $enId
            ? $prefix . 'nd_en.notice_heading, ' . $prefix . 'nd_ne.notice_heading'
            : $prefix . 'nd_ne.notice_heading, ' . $prefix . 'nd_en.notice_heading';

        $preferredOffice = $langId == $enId
            ? $prefix . 'od_en.office_name, ' . $prefix . 'od_ne.office_name'
            : $prefix . 'od_ne.office_name, ' . $prefix . 'od_en.office_name';

        return $q->select(
                'n.id',
                'n.notice_url',
                'n.notice_attachment',
                'n.published_date',
                'n.office_id',
                'o.office_subdomain',
                DB::raw('COALESCE(' . $preferredNotice . ') as notice_heading'),
                DB::raw('COALESCE(' . $preferredOffice . ') as office_name'),
                DB::raw('date_format(' . $prefix . 'n.published_date, "%d %b, %Y") as en_published_date')
            )
            ->distinct()
            ->orderBy('n.published_date', 'desc')
            ->paginate($paginate);
    }

    /**
     * Search active offices and notices for the public Notice search.
     * Notice suggestions use the normalized Notice Search Index.
     */
    public static function getActiveNoticeSearchSuggestions($term, $langId, $limit = 10, $officeId = null)
    {
        $term = trim(preg_replace('/\s+/u', ' ', (string) $term));

        if (mb_strlen($term) < 1) {
            return ['offices' => collect(), 'notices' => collect()];
        }

        $normalizedTerm = $term;

        if (!mb_check_encoding($normalizedTerm, 'UTF-8')) {
            $normalizedTerm = iconv('UTF-8', 'UTF-8//IGNORE', $normalizedTerm) ?: '';
        }

        if (class_exists(\Normalizer::class)) {
            $normalizedTerm = \Normalizer::normalize($normalizedTerm, \Normalizer::FORM_C) ?: $normalizedTerm;
        }

        $normalizedTerm = mb_strtolower($normalizedTerm, 'UTF-8');
        $normalizedTerm = preg_replace('/[^\p{L}\p{M}\p{N}]+/u', ' ', $normalizedTerm);
        $normalizedTerm = trim(preg_replace('/\s+/u', ' ', $normalizedTerm));

        if ($normalizedTerm === '') {
            return ['offices' => collect(), 'notices' => collect()];
        }

        $like = '%' . $normalizedTerm . '%';
        $officeLike = '%' . str_replace(' ', '%', $normalizedTerm) . '%';
        $neId = Language::getIdFromCode('ne');
        $enId = Language::getIdFromCode('en');
        $prefix = DB::getTablePrefix();

        $offices = DB::table('office as o')
            ->join('office_detail as od', 'od.office_id', '=', 'o.id')
            ->where('o.is_active', '1')
            ->where('o.is_deleted', '0')
            ->whereIn('od.language_id', [$neId, $enId])
            ->where('od.office_name', 'like', $officeLike)
            ->select('o.id', 'o.office_subdomain', 'od.office_name', 'od.language_id')
            ->orderBy('od.office_name')
            ->limit(30)
            ->get()
            ->groupBy('id')
            ->map(function ($records) use ($langId, $enId) {
                $preferred = $records->firstWhere('language_id', $langId);
                $fallback = $records->firstWhere('language_id', $langId == $enId
                    ? Language::getIdFromCode('ne')
                    : $enId);
                $record = $preferred ?: $fallback ?: $records->first();

                return [
                    'id' => $record->id,
                    'office_subdomain' => $record->office_subdomain,
                    'office_name' => $record->office_name,
                ];
            })
            ->take(10)
            ->values();

        // Other Office pages must not expose global office suggestions.
        if ($officeId !== null && (int) $officeId !== 4) {
            $offices = collect();
        }

        $preferredNotice = $langId == $enId
            ? $prefix . 'nd_en.notice_heading, ' . $prefix . 'nd_ne.notice_heading'
            : $prefix . 'nd_ne.notice_heading, ' . $prefix . 'nd_en.notice_heading';

        $preferredOffice = $langId == $enId
            ? $prefix . 'od_en.office_name, ' . $prefix . 'od_ne.office_name'
            : $prefix . 'od_ne.office_name, ' . $prefix . 'od_en.office_name';

        $notices = DB::table('notice as n')
            ->join('office as o', 'o.id', '=', 'n.office_id')
            ->leftJoin(DB::raw($prefix . 'notice_detail as ' . $prefix . 'nd_ne'), function ($join) use ($neId) {
                $join->on('nd_ne.notice_id', '=', 'n.id')
                    ->where('nd_ne.language_id', '=', $neId);
            })
            ->leftJoin(DB::raw($prefix . 'notice_detail as ' . $prefix . 'nd_en'), function ($join) use ($enId) {
                $join->on('nd_en.notice_id', '=', 'n.id')
                    ->where('nd_en.language_id', '=', $enId);
            })
            ->leftJoin(DB::raw($prefix . 'office_detail as ' . $prefix . 'od_ne'), function ($join) use ($neId) {
                $join->on('od_ne.office_id', '=', 'o.id')
                    ->where('od_ne.language_id', '=', $neId);
            })
            ->leftJoin(DB::raw($prefix . 'office_detail as ' . $prefix . 'od_en'), function ($join) use ($enId) {
                $join->on('od_en.office_id', '=', 'o.id')
                    ->where('od_en.language_id', '=', $enId);
            })
            ->where('n.is_active', '1')
            ->where('n.is_deleted', '0')
            ->where('o.is_active', '1')
            ->where('o.is_deleted', '0')
            ->when($officeId !== null && (int) $officeId !== 4,
                function ($query) use ($officeId) {
                    $query->where('n.office_id', $officeId);
                })
            ->whereExists(function ($subquery) use ($like) {
                $subquery->select(DB::raw('1'))
                    ->from('notice_search_indexes as si')
                    ->whereColumn('si.notice_id', 'n.id')
                    ->where('si.normalized_text', 'like', $like);
            })
            ->select(
                'n.id',
                'n.notice_url',
                'n.office_id',
                'o.office_subdomain',
                'n.published_date',
                DB::raw('COALESCE(' . $preferredNotice . ') as notice_heading'),
                DB::raw('COALESCE(' . $preferredOffice . ') as office_name')
            )
            ->orderBy('n.published_date', 'desc')
            ->distinct()
            ->limit($limit)
            ->get();

        return ['offices' => $offices, 'notices' => $notices];
    }



    /**
     * Suggest a close English spelling from indexed notice terms.
     * Only single ASCII English words are considered.
     */
    public static function getNoticeSearchSpellingSuggestion($term)
    {
        $word = trim((string) $term);

        // Nepali spelling suggestion: allow one Unicode-character edit.
        if (preg_match('/^[\x{0900}-\x{097F}]{2,20}$/u', $word)) {
            $languageId = 1;

            if (DB::table('notice_search_terms')
                ->where('language_id', $languageId)
                ->where('term', $word)
                ->exists()) {
                return null;
            }

            $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
            $firstChar = $chars[0];
            $length = count($chars);

            $candidates = DB::table('notice_search_terms')
                ->where('language_id', $languageId)
                ->where('term', 'like', $firstChar . '%')
                ->whereRaw('CHAR_LENGTH(term) BETWEEN ? AND ?', [
                    max(2, $length - 1),
                    $length + 1
                ])
                ->select('term')
                ->distinct()
                ->limit(2000)
                ->pluck('term');

            $distance = static function ($a, $b) {
                $a = preg_split('//u', $a, -1, PREG_SPLIT_NO_EMPTY);
                $b = preg_split('//u', $b, -1, PREG_SPLIT_NO_EMPTY);

                $previous = range(0, count($b));

                foreach ($a as $i => $charA) {
                    $current = [$i + 1];

                    foreach ($b as $j => $charB) {
                        $cost = ($charA === $charB) ? 0 : 1;
                        $current[$j + 1] = min(
                            $current[$j] + 1,
                            $previous[$j + 1] + 1,
                            $previous[$j] + $cost
                        );
                    }

                    $previous = $current;
                }

                return $previous[count($b)];
            };

            $best = null;
            $bestDistance = 2;

            foreach ($candidates as $candidate) {
                if (!preg_match('/^[\x{0900}-\x{097F}]{2,20}$/u', $candidate)) {
                    continue;
                }

                $candidateDistance = $distance($word, $candidate);

                if ($candidateDistance < $bestDistance) {
                    $bestDistance = $candidateDistance;
                    $best = $candidate;
                }
            }

            return $bestDistance === 1 ? $best : null;
        }

        $word = strtolower($word);

        if (!preg_match('/^[a-z]{4,12}$/', $word)) {
            return null;
        }

        if (DB::table('notice_search_terms')
            ->where('language_id', 2)
            ->where('term', $word)
            ->exists()) {
            return null;
        }

        $candidates = DB::table('notice_search_terms')
            ->where('language_id', 2)
            ->where('term', 'like', substr($word, 0, 2) . '%')
            ->whereRaw('CHAR_LENGTH(term) BETWEEN ? AND ?', [
                max(4, strlen($word) - 1),
                strlen($word) + 1
            ])
            ->select('term')
            ->distinct()
            ->limit(1500)
            ->pluck('term');

        $best = null;
        $bestDistance = 2;

        foreach ($candidates as $candidate) {
            if (!preg_match('/^[a-z]{4,12}$/', $candidate)) {
                continue;
            }

            $distance = levenshtein($word, $candidate);

            // Treat one adjacent transposition as a single typo.
            if ($distance === 2 && strlen($word) === strlen($candidate)) {
                for ($i = 0; $i < strlen($word) - 1; $i++) {
                    if (
                        $word[$i] === $candidate[$i + 1] &&
                        $word[$i + 1] === $candidate[$i] &&
                        substr($word, 0, $i) === substr($candidate, 0, $i) &&
                        substr($word, $i + 2) === substr($candidate, $i + 2)
                    ) {
                        $distance = 1;
                        break;
                    }
                }
            }

            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $candidate;
            }
        }

        return $bestDistance === 1 ? $best : null;
    }

}

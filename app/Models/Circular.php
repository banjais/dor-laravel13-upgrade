<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Circular extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'circular';

    public function details()
    {
        return $this->hasMany(Circulardetail::class, 'circular_id');
    }


    /**
     * get all undeleted circulars
     * @return object
     */
    public static function getUndeletedCirculars()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,t1.circular_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.circular_heading
						from pndc_circular t1
						join pndc_circular_detail t2 on t1.id=t2.circular_id
						where t1.is_deleted='0' and t2.language_id = '$languageId'
						orderby t1.published_date desc";

        return DB::select($query);
    }

    /**
     * get all undeleted circulars office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedCircularsByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,t1.circular_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.circular_heading
						from pndc_circular t1
						join pndc_circular_detail t2 on t1.id=t2.circular_id
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
        return Circular::where('is_deleted', '0')
            ->where('circular_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get circular detail with languages
     * @param  int $id
     * @return object
     */
    public static function getCircularDetailsWithLanguages($id)
    {
        $query = "SELECT
                    t1.id AS language_id,
                    t1.language_name,
                    t2.id AS circular_id,
                    t3.circular_heading,
                    t3.circular_description,
                    t3.circular_page_title,
                    t3.id AS circular_detail_id
                FROM pndc_language t1
                CROSS JOIN pndc_circular t2
                LEFT JOIN pndc_circular_detail t3
                    ON t3.circular_id = t2.id
                    AND t3.language_id = t1.id
                WHERE t1.is_active = '1'
                    AND t2.id = ?";

        return DB::select($query, [$id]);
    }

    /*----------------------------------------------------*/

    /**
     * get all active circulars
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveCirculars($officeId, $langId)
    {
        $paginate = Configuration::getValueFromName('per_page_circulars');
        return Circular::join('circular_detail', 'circular.id', '=', 'circular_detail.circular_id')
            ->select(
                'circular.id',
                'circular_url',
                'circular_attachment',
                'circular_detail.circular_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('circular.office_id', $officeId)
            ->where('circular_detail.language_id', $langId)
            ->where('circular.is_active', '1')
            ->where('circular.is_deleted', '0')
            ->orderby('published_date', 'desc')
            ->paginate($paginate);
    }
    /*----------------------------------------------------*/

    /**
     * get all active circulars
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveCircularsSearch($officeId, $langId, $expiry_from = '', $expiry_to = '')
    {
        $limit = Configuration::getValueFromName('per_page_circulars');
        $circulars = Circular::join('circular_detail', 'circular.id', '=', 'circular_detail.circular_id')
            ->select(
                'circular.id',
                'circular_url',
                'circular_attachment',
                'circular_detail.circular_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('circular_detail.language_id', $langId)
            ->where('circular.is_active', '1')
            ->where('circular.is_deleted', '0');


        ($officeId > 0) ? $circulars->where('circular.office_id', $officeId) : '';
        if ($expiry_to > 0 && $expiry_from > 0 && $expiry_to >= $expiry_from) {
            $circulars->whereBetween('published_date', array($expiry_from, $expiry_to));
        }

        return    $circulars->orderby('circular.published_date', 'desc')
            ->paginate($limit);
    }
    /*----------------------------------------------------*/

    /**
     * get all active circulars
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveCircularsWithoutOffices($officeId, $langId, $office_name = null, $from = null, $to = null)
    {
        $paginate = Configuration::getValueFromName('per_page_circulars');
        $q = Circular::join('circular_detail', 'circular.id', '=', 'circular_detail.circular_id')
            ->join('office', 'office.id', '=', 'circular.office_id')
            ->join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'circular.id',
                'circular_url',
                'circular_attachment',
                'circular_detail.circular_heading',
                'published_date',
                'circular.office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('circular.office_id', $officeId)
            ->where('circular_detail.language_id', $langId)
            ->where('circular.is_active', '1')
            ->where('circular.is_deleted', '0');

        if ($office_name) {
            $q->where('office_detail.office_name', $office_name);
        }
        if ($from) {
            $q->where('circular.published_date', '>=', $from);
        }
        if ($to) {
            $q->where('circular.published_date', '<=', $to);
        }

        return $q->orderby('circular.published_date', 'desc')
            ->paginate($paginate);
    }

    /**
     * get active circulars home page
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getMainHomePageCirculars($officeId, $langId)
    {
        $limit = Configuration::getValueFromName('home_page_news');
        return Circular::join('circular_detail', 'circular.id', '=', 'circular_detail.circular_id')
            ->select(
                'circular.id',
                'circular.circular_url',
                'circular_attachment',
                'circular_detail.circular_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            // ->where('circular.office_id', $officeId)
            ->where('circular_detail.language_id', $langId)
            ->where('circular.is_active', '1')
            ->where('circular.is_deleted', '0')
            ->orderby('circular.published_date', 'DESC')
            ->take(3)
            ->get();
    }
    /**
     * get active circulars home page
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageCirculars($officeId, $langId)
    {
        $limit = Configuration::getValueFromName('home_page_news');
        return Circular::join('circular_detail', 'circular.id', '=', 'circular_detail.circular_id')
            ->select(
                'circular.id',
                'circular.circular_url',
                'circular_attachment',
                'circular_detail.circular_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('circular.office_id', $officeId)
            ->where('circular_detail.language_id', $langId)
            ->where('circular.is_active', '1')
            ->where('circular.is_deleted', '0')
            ->orderby('circular.created_at', 'DESC')
            ->take(3)
            ->get();
    }

    /**
     * get circular detail by url
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getCircularDetail($officeId, $url, $langId)
    {
        return Circular::join('circular_detail', 'circular.id', '=', 'circular_detail.circular_id')
            ->select(
                'circular.id',
                'circular.circular_url',
                'circular.circular_meta_tags',
                'circular.circular_meta_description',
                'circular_attachment',
                'circular_detail.circular_heading',
                'circular_detail.circular_page_title',
                'circular_detail.circular_description',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('circular.circular_url', $url)
            ->where('circular_detail.language_id', $langId)
            ->where('circular.is_active', '1')
            ->where('circular.is_deleted', '0')
            ->first();
    }

    /**
     * get circular detail by id
     * @param int $officeId
     * @param int $id
     * @param int $langId
     * @return object
     */
    public static function getCircularDetailById($officeId, $id, $langId)
    {
        return Circular::join('circular_detail', 'circular.id', '=', 'circular_detail.circular_id')
            ->select(
                'circular.id',
                'circular.circular_url',
                'circular.circular_meta_tags',
                'circular.circular_meta_description',
                'circular_attachment',
                'circular_detail.circular_heading',
                'circular_detail.circular_page_title',
                'circular_detail.circular_description',
                'published_date'
            )
            ->where('circular.office_id', $officeId)
            ->where('circular.id', $id)
            ->where('circular_detail.language_id', $langId)
            ->where('circular.is_active', '1')
            ->where('circular.is_deleted', '0')
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

        $offices = Office::getUndeletedOffices();
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
}

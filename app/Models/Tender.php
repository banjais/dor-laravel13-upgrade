<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tender extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'tender';

    public function details()
    {
        return $this->hasMany(Tenderdetail::class, 'tender_id');
    }

    /**
     * get all undeleted tenders
     * @return object
     */
    public static function getUndeletedTenders()
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.tender_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.tender_heading
						from pndc_tender t1
						join pndc_tender_detail t2 on t1.id=t2.tender_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted tenders office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedTendersByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.tender_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.tender_heading
						from pndc_tender t1
						join pndc_tender_detail t2 on t1.id=t2.tender_id
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
        return Tender::where('is_deleted', '0')
            ->where('tender_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get tender detail with languages
     * @param  int $id
     * @return object
     */
    public static function getTenderDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select tender_heading from pndc_tender t2 join pndc_tender_detail t3
							on t2.id=t3.tender_id where t3.language_id=t1.id and t2.id='$id') as tender_heading,
					(select tender_description from pndc_tender t2 join pndc_tender_detail t3
							on t2.id=t3.tender_id where t3.language_id=t1.id and t2.id='$id') as tender_description,
					(select tender_page_title from pndc_tender t2 join pndc_tender_detail t3
							on t2.id=t3.tender_id where t3.language_id=t1.id and t2.id='$id') as tender_page_title,
					(select t3.id from pndc_tender t2 join pndc_tender_detail t3
							on t2.id=t3.tender_id where t3.language_id=t1.id and t2.id='$id') as tender_detail_id
					from pndc_language t1
					where t1.is_active = '1' ";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active tenders
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveTenders($officeId, $langId, $expiry_from = '', $expiry_to = '')
    {
        $limit   = Configuration::getValueFromName('per_page_projects');
        $tenders = Tender::join('tender_detail', 'tender.id', '=', 'tender_detail.tender_id')
            ->join('office', 'office.id', '=', 'tender.office_id')
            ->join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'tender.id',
                'tender.tender_url',
                'tender_attachment',
                'tender.expiry_date',
                'tender_detail.tender_heading',
                'tender.published_date',
                'office_detail.office_name',
                'office.office_subdomain',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date,
									date_format(expiry_date, "%d %b, %Y") as en_expiry_date,
									if(curdate() < date(expiry_date), 0,1 ) as expire_status
									')
            )
            ->where('tender_detail.language_id', $langId)
            ->where('office_detail.language_id', $langId)
            ->where('tender.is_active', '1')
            ->where('tender.is_deleted', '0');

        ($officeId > 0) ? $tenders->where('office.id', $officeId) : '';
        //($expiry_from > 0) ? $tenders->where('tender.expiry_date', $expiry_from) : '';
        //($expiry_to > 0) ? $tenders->where('office.id', $officeId) : '';

        //->whereBetween('votes', array(1, 100));

        if ($expiry_to > 0 && $expiry_from > 0 && $expiry_to >= $expiry_from) {
            $tenders->whereBetween('expiry_date', array($expiry_from, $expiry_to));
        }

        return $tenders->orderby('tender.expiry_date', 'desc')
            ->paginate($limit);
    }

    /**
     * get active tenders home page
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageTenders($officeId, $langId)
    {
        $limit = Configuration::getValueFromName('home_page_news');
        return Tender::join('tender_detail', 'tender.id', '=', 'tender_detail.tender_id')
            ->select(
                'tender.id',
                'tender.tender_url',
                'tender_attachment',
                'tender_detail.tender_heading',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('tender.office_id', $officeId)
            ->where('tender_detail.language_id', $langId)
            ->where('tender.is_active', '1')
            ->where('tender.is_deleted', '0')
            ->take($limit)
            ->get();
    }

    /**
     * get tender detail by url
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getTenderDetail($officeId, $url, $langId)
    {

        return Tender::join('tender_detail', 'tender.id', '=', 'tender_detail.tender_id')
            ->select(
                'tender.id',
                'tender.tender_url',
                'tender.tender_meta_tags',
                'tender.tender_meta_description',
                'tender_attachment',
                'tender_detail.tender_heading',
                'tender_detail.tender_page_title',
                'tender_detail.tender_description',
                'published_date',
                'expiry_date'
                // DB::raw('
                // 					date_format(published_date, "%d %b, %Y") as en_published_date,
                // 					date_format(expiry_date, "%d %b, %Y") as en_expiry_date,
                // 					if(curdate() < date(expiry_date), 0,1 ) as expire_status
                // 				')
            )
            ->where('tender.office_id', $officeId)
            ->where('tender.tender_url', $url)
            ->where('tender_detail.language_id', $langId)
            ->where('tender.is_active', '1')
            ->where('tender.is_deleted', '0')
            ->first();
    }

    /**
     * get tender detail by id
     * @param int $officeId
     * @param int $id
     * @param int $langId
     * @return object
     */
    public static function getTenderDetailById($officeId, $id, $langId)
    {
        return Tender::join('tender_detail', 'tender.id', '=', 'tender_detail.tender_id')
            ->select(
                'tender.id',
                'tender.tender_url',
                'tender.tender_meta_tags',
                'tender.tender_meta_description',
                'tender_attachment',
                'tender_detail.tender_heading',
                'tender_detail.tender_page_title',
                'tender_detail.tender_description',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date,
									date_format(expiry_date, "%d %b, %Y") as en_expiry_date,
									if(curdate() < date(expiry_date), 0,1 ) as expire_status
								')
            )
            ->where('tender.office_id', $officeId)
            ->where('tender.id', $id)
            ->where('tender_detail.language_id', $langId)
            ->where('tender.is_active', '1')
            ->where('tender.is_deleted', '0')
            ->first();
    }
}

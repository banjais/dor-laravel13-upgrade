<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Home extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by', 'office_id', 'home_url', 'show_in', 'home_identity_name','data_2_title');

    protected $table = 'home';

    public function details()
    {
        return $this->hasMany(Homedetail::class);
    }

    public static function getAllTicker()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_at,
						t1.is_active,t2.ticker_des,t2.ticker_title
						from pndc_ticker t1
						join pndc_ticker_detail t2 on t1.id=t2.ticker_id
						where t1.is_delete='0' and t2.language_id = '$languageId'
						order by t1.created_at desc
						";

        return DB::select($query);
    }

    public static function findTickerById($id)
    {
        $languageId = Language::getIdFromCode('en');



        $query    = "SELECT * FROM pndc_ticker WHERE  is_delete='0'  and id='$id'  LIMIT 1";

        return DB::select($query);
    }

    public static function getTickerDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_ticker t2 where t2.id='$id') as ticker_id,



					(select is_active from pndc_ticker t2 where t2.id='$id') as is_active,


					(select t3.ticker_des from pndc_ticker t2 join pndc_ticker_detail t3
							on t2.id=t3.ticker_id where t3.language_id=t1.id and t2.id='$id') as ticker_description,
					(select t3.ticker_title from pndc_ticker t2 join pndc_ticker_detail t3
							on t2.id=t3.ticker_id where t3.language_id=t1.id and t2.id='$id') as ticker_title,
					(select t3.id from pndc_ticker t2 join pndc_ticker_detail t3
							on t2.id=t3.ticker_id where t3.language_id=t1.id and t2.id='$id') as ticker_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }



    /**
     * get all undeleted homes
     * @return object
     */
    public static function getUndeletedHomes()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,t1.home_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.home_heading
						from pndc_home t1
						join pndc_home_detail t2 on t1.id=t2.home_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted homes office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedHomesByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "select t1.*, date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,t2.home_heading
					from pndc_home t1 left join pndc_home_detail t2 on t1.id=t2.home_id
					where t1.office_id = '$officeId'and t2.language_id = '$languageId'";
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
        return Home::where('is_deleted', '0')
            ->where('home_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')->first();
    }

    /**
     * get show in of undeleted home contents
     * @return array
     */
    public static function getShowInOfUndeleted()
    {
        $homeOffice = Office::where('office_subdomain', 'home')->first();
        return Home::where('is_deleted', '0')
            ->where('office_id', '<>', $homeOffice->id)
            ->groupby('show_in')
            ->orderby('id')
            ->pluck('show_in', 'home_url');
    }

    /**
     * get home detail with languages
     * @param  int $id
     * @return object
     */
    public static function getHomeDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_home t2 where t2.id='$id') as home_id,
					(select t2.show_in from pndc_home t2 where t2.id='$id') as show_in,
					(select home_url from pndc_home t2 where t2.id='$id') as home_url,
					(select home_meta_tags from pndc_home t2 where t2.id='$id') as home_meta_tags,
					(select home_meta_description from pndc_home t2 where t2.id='$id') as home_meta_description,
					(select home_attachment from pndc_home t2 where t2.id='$id') as home_attachment,
					(select is_active from pndc_home t2 where t2.id='$id') as is_active,
					(select home_heading from pndc_home t2 join pndc_home_detail t3
							on t2.id=t3.home_id where t3.language_id=t1.id and t2.id='$id') as home_heading,
					(select officer_name from pndc_home t2 join pndc_home_detail t3
					                on t2.id=t3.home_id where t3.language_id=t1.id and t2.id='$id') as officer_name,
					(select home_description from pndc_home t2 join pndc_home_detail t3
							on t2.id=t3.home_id where t3.language_id=t1.id and t2.id='$id') as home_description,
					(select home_page_title from pndc_home t2 join pndc_home_detail t3
							on t2.id=t3.home_id where t3.language_id=t1.id and t2.id='$id') as home_page_title,
					(select t3.id from pndc_home t2 join pndc_home_detail t3
							on t2.id=t3.home_id where t3.language_id=t1.id and t2.id='$id') as home_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*------------------------------------------------------*/
    /**
     * get home detail
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getHomeDetail($officeId, $url, $langId)
    {
        // dd($officeId);
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.home_url',
                'home.home_meta_tags',
                'home.home_meta_description',
                'home_attachment',
                'home_detail.home_heading',
                'home_detail.home_page_title',
                'home_detail.home_description'
            )
            ->where('home.office_id', $officeId)
            ->where('home.home_url', $url)
            ->where('home_detail.language_id', $langId)
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        return $result;
    }

    /**
     * get home page setions data
     * @param int $showIn
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageData($showIn, $officeId, $langId)
    {
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.office_id',
                'home.home_url',
                'home.home_attachment',
                'home_detail.home_heading',
                'home_detail.home_description',
                'home_detail.home_page_title',
                'home.home_meta_tags',
                'home.home_meta_description',
                'home.data_1',
                'home.data_2',
                'home.data_3',
                'home.data_4'
            )
            ->where('home.office_id', $officeId)
            ->where('home.show_in', $showIn)
            ->where('home_detail.language_id', $langId)
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        return $result;
    }
    /**
     * get home page setions data
     * @param int $showIn
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageSpokeperson($showIn, $officeId, $langId)
    {
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.office_id',
                'home.home_url',
                'home.home_attachment',
                'home_detail.home_heading',
                'home_detail.home_description',
                'home_detail.home_page_title',
                'home.home_meta_tags',
                'home.home_meta_description',
                'home.data_1',
                'home.data_2',
                'home.data_3',
                'home.data_4'
            )
            ->where('home.office_id', $officeId)
            ->where('home.show_in', $showIn)
            ->where('home_detail.language_id', $langId)
            ->where('home.home_url', 'dor-spoke-person')
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        // dd($result);
        return $result;
    }

    /**
     * get home page setions data
     * @param int $showIn
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageInformationOfficer($showIn, $officeId, $langId)
    {
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.office_id',
                'home.home_url',
                'home.home_attachment',
                'home_detail.home_heading',
                'home_detail.home_description',
                'home_detail.home_page_title',
                'home.home_meta_tags',
                'home.home_meta_description',
                'home.data_1',
                'home.data_2',
                'home.data_3',
                'home.data_4'
            )
            ->where('home.office_id', $officeId)
            ->where('home.show_in', $showIn)
            ->where('home_detail.language_id', $langId)
            ->where('home.home_url', 'information-officer')
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        // dd($result);
        return $result;
    }

    public static function getHomePageDirectorGeneral($showIn, $officeId, $langId)
    {
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.office_id',
                'home.home_url',
                'home.home_attachment',
                'home_detail.home_heading',
                'home_detail.home_description',
                'home_detail.home_page_title',
                'home.home_meta_tags',
                'home.home_meta_description',
                'home.data_1',
                'home.data_2',
                'home.data_3',
                'home.data_4'
            )
            ->where('home.office_id', $officeId)
            // ->where('home.show_in', $showIn)
            ->where('home_detail.language_id', $langId)
            ->where('home.home_url', 'message-from-director-general')
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        // dd($result);
        return $result;
    }

    /**
     * get home page setions data
     * @param int $showIn
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageGrievanceOfficer($showIn, $officeId, $langId)
    {
        return Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.office_id',
                'home.home_url',
                'home.home_attachment',
                'home_detail.home_heading',
                'home_detail.home_description',
                'home_detail.home_page_title',
                'home.home_meta_tags',
                'home.home_meta_description',
                'home.data_1',
                'home.data_2',
                'home.data_3',
                'home.data_4'
            )
            ->where('home.office_id', $officeId)
            ->where('home.show_in', $showIn)
            ->where('home_detail.language_id', $langId)
            ->where('home.home_url', 'grievance-hearing-officer')
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
    }

    public static function getHomePageWorkingarea($showIn, $officeId, $langId)
    {
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.office_id',
                'home.home_url',
                'home.home_attachment',
                'home_detail.home_heading',
                'home_detail.home_description'
            )
            ->where('home.office_id', $officeId)
            ->where('home.show_in', $showIn)
            ->where('home_detail.language_id', $langId)
            ->where('home.home_url', 'working-area')
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        return $result;
    }
    /**
     * get home page setions data
     * @param int $showIn
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageGeneralInfo($showIn, $officeId, $langId)
    {
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.office_id',
                'home.home_url',
                'home.home_attachment',
                'home_detail.home_heading',
                'home_detail.home_description'
            )
            ->where('home.office_id', $officeId)
            ->where('home.show_in', $showIn)
            ->where('home_detail.language_id', $langId)
            ->where('home.home_url', 'general-info')
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        return $result;
    }

    /**
     * get home page setions data
     * @param int $showIn
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageGeneralAbout($showIn, $officeId, $langId)
    {
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->select(
                'home.id',
                'home.office_id',
                'home.home_url',
                'home.home_attachment',
                'home_detail.home_heading',
                'home_detail.home_description'
            )
            ->where('home.office_id', $officeId)
            ->where('home.show_in', $showIn)
            ->where('home_detail.language_id', $langId)
            ->where('home.home_url', 'about-us')
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        return $result;
    }

    /**
     * get home page setions data
     * @param int $showIn
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomePageAdmin($slug, $officeId)
    {
        $result = Home::where('home.office_id', $officeId)
            ->where('home.home_url', $slug)
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        return $result;
    }

    /**
     * get home page setions data
     * @param int $showIn
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getHomeDataFrontend($slug, $officeId, $languageId = 1)
    {
        $result = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
            ->where('home.office_id', $officeId)
            ->where('home.home_url', $slug)
            ->where('home_detail.language_id', $languageId)
            ->where('home.is_active', '1')
            ->where('home.is_deleted', '0')
            ->first();
        return $result;
    }

    /**
     * fetch contentlist at header
     */
    public static function headerContentAllList($parentId = 0, $singleMenu = 0, $singleMenuId = '')
    {
        $contents = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.content_url',
                'content.content_attachment',
                'content.parent_id',
                'content_detail.content_heading'
            )
            ->where('content.parent_id', $parentId)
            ->where('content.is_active', '1');

        $result = $contents->get();
        return $result;
    }
    /**
     * fetch contentlist at header
     */
    public static function headerContentMenus($parentId = 0, $singleMenu = 0, $singleMenuId = '')
    {
        $contents = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.content_url',
                'content.parent_id',
                'content_detail.content_heading'
            )
            ->where('content.parent_id', $parentId)
            ->where('content.show_in', 'LIKE', '%1%')
            ->where('content.is_active', '1');
        if ($singleMenu && $singleMenuId) {
            $contents->where('id', $singleMenuId);
        }
        $contents->take(10);
        $result = $contents->get();
        return $result;
    }
}

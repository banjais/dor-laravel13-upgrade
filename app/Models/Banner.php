<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Banner extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'banner';

    public function details()
    {
    	return $this->hasMany(Bannerdetail::class,'banner_id');
    }

    /**
     * get all undeleted banners
     * @return object
     */
    public static function getUndeletedBanners()
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.banner_title
						from pndc_banner t1
						join pndc_banner_detail t2 on t1.id=t2.banner_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted banners office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedBannersByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t1.banner_attachment,t2.banner_title
						from pndc_banner t1
						join pndc_banner_detail t2 on t1.id=t2.banner_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' and t1.office_id='$officeId' ";

        return DB::select($query);
    }

    /**
     * get banner detail with languages
     * @param  int $id
     * @return object
     */
    public static function getBannerDetailsWithLanguages($id)
    {
        return DB::table('language as l')
            ->crossJoin('banner as b')
            ->leftJoin('banner_detail as bd', function ($join) {
                $join->on('bd.banner_id', '=', 'b.id')
                    ->on('bd.language_id', '=', 'l.id');
            })
            ->where('b.id', $id)
            ->where('l.is_active', '1')
            ->where('l.is_deleted', '0')
            ->select(
                'l.id as language_id',
                'l.language_name',
                'b.id as banner_id',
                'b.banner_attachment',
                'b.is_active',
                'bd.id as banner_detail_id',
                'bd.banner_title',
                'bd.banner_caption',
                'bd.banner_description'
            )
            ->get();
    }

    /*--------------------------------------------*/
    /**
     * get home page banners
     * @param  int $id
     * @return object
     */
   /* public static function getHomePageBanners($officeId)
    {
        $limit = Configuration::getValueFromName('home_page_banners');

        return Banner::where('is_active', 1)
            ->where('is_deleted', 0)
            ->where('office_id', $officeId)
            ->take('5')
            ->orderby('id', 'desc')
            ->get();
    } */

	public static function getHomePageBanners($officeId, $langId){
		//$limit = Configuration::getValueFromName('home_page_news');
		return Banner::join('banner_detail', 'banner.id', '=', 'banner_detail.banner_id')
					->select(
								'banner.*',
								'banner_detail.banner_title', 'banner_detail.banner_caption', 'banner_detail.banner_description'


							)
					->where('banner.office_id', $officeId)
					->where('banner_detail.language_id', $langId)
					->where('banner.is_active', '1')
					->where('banner.is_deleted', '0')
					->take(999)
					->get();
	}

    /**
     * Fetch a published banner detail for its owning office and language.
     */
    public static function getPublishedDetail($bannerId, $officeId, $langId)
    {
        return DB::table('banner')
            ->join('banner_detail', 'banner.id', '=', 'banner_detail.banner_id')
            ->where('banner.id', $bannerId)
            ->where('banner.office_id', $officeId)
            ->where('banner.is_active', '1')
            ->where('banner.is_deleted', '0')
            ->where('banner_detail.language_id', $langId)
            ->select(
                'banner.id',
                'banner.banner_attachment',
                'banner_detail.banner_title',
                'banner_detail.banner_caption',
                'banner_detail.banner_description'
            )
            ->first();
    }

}

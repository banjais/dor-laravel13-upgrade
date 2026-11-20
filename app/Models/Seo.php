<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Seo extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('office_id','page_name','is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'seo';

    public function details()
    {
        return $this->hasMany(Seodetail::class, 'seo_id');
    }

    /**
     * get all undeleted seos
     * @return object
     */
    public static function getUndeletedSeos()
    {
        $languageId = Language::getIdFromCode('en');
        $officeId   = Auth::user()->office_id;

        return DB::table('seo')
            ->select('id', 'page_name', 'is_active', DB::raw('DATE_FORMAT(updated_at, "%d %M, %Y") as updated_on'))
            ->where('is_deleted', '0')
            ->where('office_id', $officeId)
            ->get();

        // return DB::select($query);
    }

    /**
     * get page names of undeleted seos
     * @return array
     */
    public static function getPageNamesOfUndeletedSeos()
    {
        return DB::table('seo')
            ->where('is_deleted', '0')
            ->groupby('page_name')
            ->orderby('id')
            ->select('page_name')->get();
    }

    /**
     * get seo detail with languages
     * @param  int $id
     * @return object
     */
    public static function getSeoDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_seo t2 where t2.id='$id') as seo_id,
					(select page_name from pndc_seo t2 where t2.id='$id') as page_name,
					(select meta_tags from pndc_seo t2 where t2.id='$id') as meta_tags,
					(select meta_description from pndc_seo t2 where t2.id='$id') as meta_description,
					(select is_active from pndc_seo t2 where t2.id='$id') as is_active,
					(select page_title from pndc_seo t2 join pndc_seo_detail t3
							on t2.id=t3.seo_id where t3.language_id=t1.id and t2.id='$id') as page_title,
					(select t3.id from pndc_seo t2 join pndc_seo_detail t3
							on t2.id=t3.seo_id where t3.language_id=t1.id and t2.id='$id') as seo_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /**
     * get seo detail with languages
     * @param  string $pageName
     * @return object
     */
    public static function getSeoDetailsByPageNameWithLanguages($pageName)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_seo t2 where t2.page_name='$pageName') as seo_id,
					(select meta_tags from pndc_seo t2 where t2.page_name='$pageName') as meta_tags,
					(select meta_description from pndc_seo t2 where t2.page_name='$pageName') as meta_description,
					(select is_active from pndc_seo t2 where t2.page_name='$pageName') as is_active,
					(select page_title from pndc_seo t2 join pndc_seo_detail t3
							on t2.id=t3.seo_id where t3.language_id=t1.id and t2.page_name='$pageName') as page_title,
					(select t3.id from pndc_seo t2 join pndc_seo_detail t3
							on t2.id=t3.seo_id where t3.language_id=t1.id and t2.page_name='$pageName') as seo_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /**
     * get seo detail of the page language wise
     * @param  string $pageName
     * @param  int $langId
     * @return object
     */
    public static function getSeoDetailsByPageNameAndLanguage($pageName, $langId)
    {
        return DB::table('seo')
            ->join('seo_detail', 'seo.id', '=', 'seo_detail.seo_id')
            ->select('seo.id', 'seo.page_name', 'seo.meta_tags', 'seo.meta_description',
                'seo_detail.page_title')
            ->where('seo.page_name', $pageName)
            ->where('seo_detail.language_id', $langId)
            ->where('seo.is_active', '1')
            ->first();
    }

    /**
     * get seo detail of the page language wise
     * @param  string $pageName
     * @param  int $langId
     * @param  int $officeId
     * @return object
     */
    public static function getSeoDetailsByOffice($pageName, $langId, $officeId)
    {
        return DB::table('seo')
            ->join('seo_detail', 'seo.id', '=', 'seo_detail.seo_id')
            ->select('seo.id', 'seo.page_name', 'seo.meta_tags', 'seo.meta_description',
                'seo_detail.page_title')
            ->where('seo.page_name', $pageName)
            ->where('seo_detail.language_id', $langId)
            ->where('seo.office_id', $officeId)
            ->where('seo.is_active', '1')
            ->first();
    }
}

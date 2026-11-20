<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'publication_category';

    public function details()
    {
        return $this->hasMany(Categorydetail::class, 'category_id');
    }

    public function publications()
    {
        return $this->hasMany(Publication::class, 'category_id');
    }

    /**
     * get all undeleted Categories
     * @return object
     */
    public static function getUndeletedCategories()
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.category_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.category_heading
						from pndc_publication_category t1
						join pndc_publication_category_detail t2 on t1.id=t2.category_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted Categories office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedCategoriesByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.category_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.category_heading
						from pndc_publication_category t1
						join pndc_publication_category_detail t2 on t1.id=t2.category_id
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
        return DB::table('publication_category')
            ->where('is_deleted', '0')
            ->where('category_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get category detail with languages
     * @param  int $id
     * @return object
     */
    public static function getCategoryDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_publication_category t2 where t2.id='$id') as category_id,
					(select category_url from pndc_publication_category t2 where t2.id='$id') as category_url,
					(select category_meta_tags from pndc_publication_category t2 where t2.id='$id') as category_meta_tags,
					(select category_meta_description from pndc_publication_category t2 where t2.id='$id') as category_meta_description,
					(select is_active from pndc_publication_category t2 where t2.id='$id') as is_active,
					(select category_heading from pndc_publication_category t2 join pndc_publication_category_detail t3
							on t2.id=t3.category_id where t3.language_id=t1.id and t2.id='$id') as category_heading,
					(select category_page_title from pndc_publication_category t2 join pndc_publication_category_detail t3
							on t2.id=t3.category_id where t3.language_id=t1.id and t2.id='$id') as category_page_title,
					(select t3.id from pndc_publication_category t2 join pndc_publication_category_detail t3
							on t2.id=t3.category_id where t3.language_id=t1.id and t2.id='$id') as category_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /**
     * get categories language wise
     * @param  int $languageId
     * @return object
     */
    public static function getCategoriesByLanguage($languageId, $officeId=4)
    {
        $query = "Select t1.id,
						t2.category_heading
						from pndc_publication_category t1
						join pndc_publication_category_detail t2 on t1.id=t2.category_id
						where t1.is_deleted='0' and t1.is_active='1' and t2.language_id = '$languageId' and t1.office_id='$officeId' ";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active categories
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveCategories($officeId, $langId)
    {
        $paginate = Configuration::getValueFromName('per_page_notices');
        return DB::table('publication_category')
            ->join('publication_category_detail', 'publication_category.id', '=', 'publication_category_detail.category_id')
            ->select(
                'publication_category.id',
                'category_url',
                'category_meta_tags',
                'category_meta_description',
                'category_heading',
                'category_page_title'
            )
            ->where('publication_category.office_id', $officeId)
            ->where('publication_category_detail.language_id', $langId)
            ->where('publication_category.is_active', '1')
            ->where('publication_category.is_deleted', '0')
            ->orderBy('category_url', 'asc')
            ->paginate($paginate);
    }

    /**
     * get category detail by url
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getPublicationDetail($officeId, $url, $langId)
    {
        return DB::table('publication_category')
            ->join('publication_detail', 'publication.id', '=', 'publication_detail.publication_id')
            ->select(
                'publication.id',
                'publication.publication_url',
                'publication.publication_meta_tags',
                'publication.publication_meta_description',
                'publication_attachment',
                'publication_detail.publication_heading',
                'publication_detail.publication_page_title',
                'publication_detail.publication_description',
                'publication_detail.publication_date'
            )
            ->where('publication.office_id', $officeId)
            ->where('publication.publication_url', $url)
            ->where('publication_detail.language_id', $langId)
            ->where('publication.is_active', '1')
            ->where('publication.is_deleted', '0')
            ->first();
    }

    /**
     * get id using url
     * @param string $url
     * @return int
     */
    public static function getIdfromUrl($officeId, $url)
    {
        // DB::enableQueryLog();
        $data = DB::table('publication_category')
            ->where('category_url', $url)
            ->where('office_id', $officeId)
            ->where('is_active', '1')
            ->where('is_deleted', '0')
            ->pluck('id')
            ->first();
        // dd(Helper::get_last_query());
        return $data;
    }

    /**
     * get category name
     * @param int $officeId
     * @param int $langId
     * @param string $url
     * @param string $fieldname
     * @return string
     */
    public static function getCategoryName($officeId, $langId, $url, $fieldname = 'category_heading')
    {
        return DB::table('publication_category')
            ->join('publication_category_detail', 'publication_category.id', '=', 'publication_category_detail.category_id')
            ->where('publication_category.office_id', $officeId)
            ->where('publication_category_detail.language_id', $langId)
            ->where('publication_category.category_url', $url)
            ->where('publication_category.is_active', '1')
            ->where('publication_category.is_deleted', '0')
            ->pluck('category_heading')
            ->first();
    }
}

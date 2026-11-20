<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Publication extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'publication';

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function details()
    {
        return $this->hasMany(Publicationdetail::class, 'publication_id');
    }

    /**
     * get all undeleted publications
     * @return object
     */
    public static function getUndeletedPublications()
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.publication_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.publication_heading
						from pndc_publication t1
						join pndc_publication_detail t2 on t1.id=t2.publication_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted publications office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedPublicationsByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.publication_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.publication_heading
						from pndc_publication t1
						join pndc_publication_detail t2 on t1.id=t2.publication_id
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
        return $publications = DB::table('publication')
            ->where('is_deleted', '0')
            ->where('publication_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get publication detail with languages
     * @param  int $id
     * @return object
     */
    public static function getPublicationDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_publication t2 where t2.id='$id') as publication_id,
					(select publication_heading from pndc_publication t2 join pndc_publication_detail t3
							on t2.id=t3.publication_id where t3.language_id=t1.id and t2.id='$id') as publication_heading,
					(select publication_description from pndc_publication t2 join pndc_publication_detail t3
							on t2.id=t3.publication_id where t3.language_id=t1.id and t2.id='$id') as publication_description,
					(select publication_page_title from pndc_publication t2 join pndc_publication_detail t3
							on t2.id=t3.publication_id where t3.language_id=t1.id and t2.id='$id') as publication_page_title,
					(select t3.id from pndc_publication t2 join pndc_publication_detail t3
							on t2.id=t3.publication_id where t3.language_id=t1.id and t2.id='$id') as publication_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active publications
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActivePublications($officeId, $langId, $catId, $paginate = null)
    {
        if (!$paginate)
            $paginate = Configuration::getValueFromName('per_page_notices');
        return DB::table('publication_category')
            ->leftjoin('publication_category_detail', 'publication_category.id', '=', 'publication_category_detail.category_id')
            ->join('publication', 'publication_category.id', '=', 'publication.category_id')
            ->join('publication_detail', 'publication.id', '=', 'publication_detail.publication_id')
            ->select(
                'publication.id',
                'publication.office_id',
                'publication.category_id',
                'publication_url',
                'category_url',
                'publication_heading',
                'publication_attachment',
                'published_date',
                'category_page_title',
                'category_meta_tags',
                'category_meta_description',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
								')
            )
            ->when((int) $officeId !== 4, function ($query) use ($officeId) {
                $query->where('publication.office_id', $officeId)
                    ->where('publication_category.office_id', $officeId);
            })
            ->where('publication_detail.language_id', $langId)
            ->where('publication_category_detail.language_id', $langId)
            ->where('publication.category_id', $catId)
            ->where('publication.is_active', '1')
            ->where('publication.is_deleted', '0')
            // ->orderBy('publication_detail.publication_heading', 'asc')
            ->orderBy('publication.created_at','desc')
            ->paginate($paginate);
    }

    public static function getLatestActivePublications($officeId, $langId, $paginate = null)
    {
        if (!$paginate)
            $paginate = Configuration::getValueFromName('per_page_notices');
        return DB::table('publication_category')
            ->leftjoin('publication_category_detail', 'publication_category.id', '=', 'publication_category_detail.category_id')
            ->join('publication', 'publication_category.id', '=', 'publication.category_id')
            ->join('publication_detail', 'publication.id', '=', 'publication_detail.publication_id')
            ->select(
                'publication.id',
                'publication.category_id',
                'publication_url',
                'category_url',
                'publication_heading',
                'publication_attachment',
                'published_date',
                'category_page_title',
                'category_meta_tags',
                'category_meta_description',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
								')
            )
            ->where('publication.office_id', $officeId)
            ->where('publication_detail.language_id', $langId)
            ->where('publication_category_detail.language_id', $langId)
            ->where('publication.is_active', '1')
            ->where('publication.is_deleted', '0')
            ->orderBy('publication_detail.publication_heading', 'asc')
            ->paginate($paginate);
    }

    /**
     * get all active publications
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    /**
     * Count active, undeleted publications belonging to one office.
     */
    public static function getActivePublicationCount($officeId)
    {
        return DB::table('publication as p')
            ->join('publication_category as c', function ($join) {
                $join->on('c.id', '=', 'p.category_id')
                    ->on('c.office_id', '=', 'p.office_id');
            })
            ->where('p.office_id', $officeId)
            ->where('p.is_active', '1')
            ->where('p.is_deleted', '0')
            ->where('c.is_active', '1')
            ->where('c.is_deleted', '0')
            ->count();
    }

    public static function getHomePagePublications($officeId, $langId, $limit=null)
    {
        if(!$limit)
            $limit = Configuration::getValueFromName('home_page_news');

        $categories = Category::with(['details' => function ($query) use ($langId) {
            $query->where('language_id', $langId);
        }])->where('is_active', '1');

        if ($officeId != 4) {
            $categories->where('office_id', $officeId)
                ->where('is_deleted', '0');
        }

        $categories = $categories->take($limit)->get();

        return $categories;

        // return Category::leftjoin('publication_category_detail', 'publication_category.id', '=', 'publication_category_detail.category_id')
        //     ->select(
        //         'publication_category.id', 'category_url',
        //         'category_heading', 'publication_category.created_at as published_date'

        //     )
        //     ->where('publication_category.is_deleted', 0)
        //     ->where('publication_category.is_active', 1)
        //     ->take($limit)
        //     ->get();

    }

    /**
     * get publication detail by url
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getPublicationDetail($officeId, $url, $langId)
    {
        return DB::table('publication')
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
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
								')
            )
            ->where('publication.office_id', $officeId)
            ->where('publication.publication_url', $url)
            ->where('publication_detail.language_id', $langId)
            ->where('publication.is_active', '1')
            ->where('publication.is_deleted', '0')
            ->first();
    }

    /**
     * get publication detail by id
     * @param int $officeId
     * @param int $id
     * @param int $langId
     * @return object
     */
    public static function getPublicationDetailById($officeId, $id, $langId)
    {
        return DB::table('publication')
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
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
								')
            )
            ->where('publication.office_id', $officeId)
            ->where('publication.id', $id)
            ->where('publication_detail.language_id', $langId)
            ->where('publication.is_active', '1')
            ->where('publication.is_deleted', '0')
            ->first();
    }
}

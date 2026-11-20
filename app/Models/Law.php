<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Law extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');
    protected $table = 'laws';


    /**
     * get all undeleted laws
     * @return object
     */
    public static function getUndeletedLaws()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,t1.law_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.law_heading
						from pndc_laws t1
						join pndc_law_detail t2 on t1.id=t2.law_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' and office_id='" . Auth::user()->office_id . "' ";

        return DB::select($query);
    }

    /**
     * check whether url exist or not for individual office
     * @param  string $url
     * @param  int $officeId
     * @return int
     */
    public static function checkExist($url)
    {
        return Law::where('is_deleted', 0)
            ->where('law_url', $url)
            ->pluck('id')
            ->first();
    }

    /**
     * get law detail with languages
     * @param  int $id
     * @return object
     */
    public static function getLawDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_laws t2 where t2.id='$id') as law_id,
					(select law_heading from pndc_laws t2 join pndc_law_detail t3
							on t2.id=t3.law_id where t3.language_id=t1.id and t2.id='$id') as law_heading,
					(select law_description from pndc_laws t2 join pndc_law_detail t3
							on t2.id=t3.law_id where t3.language_id=t1.id and t2.id='$id') as law_description,
					(select law_page_title from pndc_laws t2 join pndc_law_detail t3
							on t2.id=t3.law_id where t3.language_id=t1.id and t2.id='$id') as law_page_title,
					(select t3.id from pndc_laws t2 join pndc_law_detail t3
							on t2.id=t3.law_id where t3.language_id=t1.id and t2.id='$id') as law_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }


    /*----------------------------------------------------*/

    /**
     * get all active laws
     * @param int $langId
     * @return object
     */
    public static function getActiveLaws($langId, $paginate = null)
    {
        if (!$paginate)
            $paginate = Configuration::getValueFromName('per_page_notices');
        return Law::join('law_detail', 'laws.id', '=', 'law_detail.law_id')
            ->select(
                'laws.id',
                'laws.law_url',
                'law_attachment',
                'law_detail.law_heading',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
								')
            )
            ->where('law_detail.language_id', $langId)
            ->where('laws.is_active', '1')
            ->where('laws.is_deleted', '0')
            ->orderBy('law_detail.law_heading')
            ->paginate($paginate);
    }


    /**
     * get law detail by url
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getLawDetail($url, $langId)
    {
        return Law::join('law_detail', 'laws.id', '=', 'law_detail.law_id')
            ->select(
                'laws.id',
                'law_url',
                'law_meta_tags',
                'law_meta_description',
                'law_attachment',
                'laws.created_at',
                'law_heading',
                'law_page_title',
                'law_description',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
								')
            )
            ->where('laws.law_url', $url)
            ->where('law_detail.language_id', $langId)
            ->where('laws.is_active', '1')
            ->where('laws.is_deleted', '0')
            ->first();
    }

    /**
     * get law detail by id
     * @param int $id
     * @param int $langId
     * @return object
     */
    public static function getLawDetailById($id, $langId)
    {
        return Law::join('law_detail', 'laws.id', '=', 'law_detail.law_id')
            ->select(
                'laws.id',
                'law_url',
                'law_meta_tags',
                'law_meta_description',
                'law_attachment',
                'law_heading',
                'law_page_title',
                'law_description',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
								')
            )
            ->where('laws.id', $id)
            ->where('law_detail.language_id', $langId)
            ->where('laws.is_active', '1')
            ->where('laws.is_deleted', '0')
            ->first();
    }
}

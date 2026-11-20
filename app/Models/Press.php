<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Press extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'press';

    public function details()
    {
        return $this->hasMany(Pressdetail::class, 'press_id');
    }

    /**
     * get all undeleted Presses
     * @return object
     */
    public static function getUndeletedPresses()
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.press_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.press_heading
						from pndc_press t1
						join pndc_press_detail t2 on t1.id=t2.press_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted Presses office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedPressesByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.press_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.press_heading
						from pndc_press t1
						join pndc_press_detail t2 on t1.id=t2.press_id
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
        return Press::where('is_deleted', '0')
            ->where('press_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')->first();
    }

    /**
     * get press detail with languages
     * @param  int $id
     * @return object
     */
    public static function getPressDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_press t2 where t2.id='$id') as press_id,
					(select press_heading from pndc_press t2 join pndc_press_detail t3
							on t2.id=t3.press_id where t3.language_id=t1.id and t2.id='$id') as press_heading,
					(select press_description from pndc_press t2 join pndc_press_detail t3
							on t2.id=t3.press_id where t3.language_id=t1.id and t2.id='$id') as press_description,
					(select press_page_title from pndc_press t2 join pndc_press_detail t3
							on t2.id=t3.press_id where t3.language_id=t1.id and t2.id='$id') as press_page_title,
					(select t3.id from pndc_press t2 join pndc_press_detail t3
							on t2.id=t3.press_id where t3.language_id=t1.id and t2.id='$id') as press_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active presss
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActivePresses($officeId, $langId)
    {
        $paginate = Configuration::getValueFromName('per_page_notices');
        return Press::join('press_detail', 'press.id', '=', 'press_detail.press_id')
            ->select(
                'press.id',
                'press_url',
                'press_attachment',
                'press_detail.press_heading',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('press.office_id', $officeId)
            ->where('press_detail.language_id', $langId)
            ->where('press.is_active', '1')
            ->where('press.is_deleted', '0')
            ->paginate($paginate);
    }

    /**
     * get press detail by url
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getPressDetail($officeId, $url, $langId)
    {
        return Press::join('press_detail', 'press.id', '=', 'press_detail.press_id')
            ->select(
                'press.id',
                'press_url',
                'press_meta_tags',
                'press_meta_description',
                'press_attachment',
                'press.created_at',
                'press_heading',
                'press_page_title',
                'press_description',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('press.office_id', $officeId)
            ->where('press.press_url', $url)
            ->where('press_detail.language_id', $langId)
            ->where('press.is_active', '1')
            ->where('press.is_deleted', '0')
            ->first();
    }

    /**
     * get press detail by id
     * @param int $officeId
     * @param int $id
     * @param int $langId
     * @return object
     */
    public static function getPressDetailById($officeId, $id, $langId)
    {
        return Press::join('press_detail', 'press.id', '=', 'press_detail.press_id')
            ->select(
                'press.id',
                'press.press_url',
                'press.press_meta_tags',
                'press.press_meta_description',
                'press_attachment',
                'press_detail.press_heading',
                'press_detail.press_page_title',
                'press_detail.press_description',
                'published_date',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('press.office_id', $officeId)
            ->where('press.id', $id)
            ->where('press_detail.language_id', $langId)
            ->where('press.is_active', '1')
            ->where('press.is_deleted', '0')
            ->first();
    }
}

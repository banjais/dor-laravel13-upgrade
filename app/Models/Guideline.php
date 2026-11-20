<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Guideline extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'guidelines';

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function details()
    {
        return $this->hasMany(GuidelineDetail::class, 'guideline_id');
    }

    /**
     * check whether url exist or not for individual office
     * @param  string $url
     * @param  int $officeId
     * @return int
     */
    public static function checkExist($url, $officeId)
    {
        return Guideline::where('is_deleted', '0')
            ->where('guideline_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get guideline detail with languages
     * @param  int $id
     * @return object
     */
    public static function getGuidelineDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_guidelines t2 where t2.id='$id') as notice_id,
					(select guideline_heading from pndc_guidelines t2 join pndc_guideline_detail t3
							on t2.id=t3.guideline_id where t3.language_id=t1.id and t2.id='$id') as guideline_heading,
					(select guideline_description from pndc_guidelines t2 join pndc_guideline_detail t3
							on t2.id=t3.guideline_id where t3.language_id=t1.id and t2.id='$id') as guideline_description,
					(select guideline_page_title from pndc_guidelines t2 join pndc_guideline_detail t3
							on t2.id=t3.guideline_id where t3.language_id=t1.id and t2.id='$id') as guideline_page_title,
					(select t3.id from pndc_guidelines t2 join pndc_guideline_detail t3
							on t2.id=t3.guideline_id where t3.language_id=t1.id and t2.id='$id') as guideline_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /**
     * get active guidelines home page
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveGuidelines($langId, $limit = null)
    {
        if (!$limit)
            $limit = Configuration::getValueFromName('home_page_news');
        $all = Guideline::join('guideline_detail', 'guidelines.id', '=', 'guideline_detail.guideline_id')
            ->select(
                'guidelines.id',
                'guidelines.guideline_url',
                'guideline_attachment',
                'guideline_detail.guideline_heading',
                'published_date',
                'office_id',
                DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
            )
            ->where('guideline_detail.language_id', $langId)
            ->where('guidelines.is_active', '1')
            ->where('guidelines.is_deleted', '0')
            ->orderby('guidelines.published_date', 'DESC')
            ->paginate($limit);
        return $all;
    }

    /**
     * get guideline detail by url
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getGuidelineDetail($url, $langId)
    {
        // dd($langId);
        return Guideline::join('guideline_detail', 'guidelines.id', '=', 'guideline_detail.guideline_id')
            ->select(
                'guidelines.id',
                'guidelines.guideline_url',
                'guideline_attachment',
                'guideline_detail.guideline_heading',
                'guideline_detail.guideline_description',
                'published_date',
                'office_id',
                DB::raw('
                                date_format(published_date, "%d %b, %Y") as en_published_date
                                ')
            )
            ->where('guidelines.guideline_url', $url)
            ->where('guideline_detail.language_id', $langId)
            ->where('guidelines.is_active', '1')
            ->where('guidelines.is_deleted', '0')
            ->first();

    }

    /**
     * get guideline detail by id
     * @param int $officeId
     * @param int $id
     * @param int $langId
     * @return object
     */
    public static function getGuidelineDetailById($id, $langId)
    {
        return Guideline::join('guideline_detail', 'guidelines.id', '=', 'guideline_detail.guideline_id')
            ->select(
                'guidelines.id',
                'guidelines.guideline_url',
                'guideline_attachment',
                'guideline_detail.guideline_heading',
                'published_date',
                'office_id',
                DB::raw('
                                date_format(published_date, "%d %b, %Y") as en_published_date
                                ')
            )
            ->where('guidelines.id', $id)
            ->where('guideline_detail.language_id', $langId)
            ->where('guidelines.is_active', '1')
            ->where('guidelines.is_deleted', '0')
            ->first();
    }
}

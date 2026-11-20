<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Faq extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');
    protected $table = 'faq';


    /**
     * get all undeleted faqs
     * @return object
     */
    public static function getUndeletedFaqs()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.faq_question
						from pndc_faq t1
						join pndc_faq_detail t2 on t1.id=t2.faq_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }


    /**
     * get faq detail with languages
     * @param  int $id
     * @return object
     */
    public static function getFaqDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_faq t2 where t2.id='$id') as faq_id,
					(select is_active from pndc_faq t2 where t2.id='$id') as is_active,
					(select faq_question from pndc_faq t2 join pndc_faq_detail t3
							on t2.id=t3.faq_id where t3.language_id=t1.id and t2.id='$id') as faq_question,
					(select faq_answer from pndc_faq t2 join pndc_faq_detail t3
							on t2.id=t3.faq_id where t3.language_id=t1.id and t2.id='$id') as faq_answer,
					(select t3.id from pndc_faq t2 join pndc_faq_detail t3
							on t2.id=t3.faq_id where t3.language_id=t1.id and t2.id='$id') as faq_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active faqs
     * @param int $langId
     * @return object
     */
    public static function getActiveFaqs($langId)
    {
        return Faq::join('faq_detail', 'faq.id', '=', 'faq_detail.faq_id')
            ->select(
                'faq.id',
                'faq_detail.faq_question',
                'faq_detail.faq_answer'
            )
            ->where('faq_detail.language_id', $langId)
            ->where('faq.is_active', '1')
            ->where('faq.is_deleted', '0')
            ->get();
    }
}

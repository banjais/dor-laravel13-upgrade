<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Training extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');
    protected $table = 'training';


    /**
     * get all undeleted trainings
     * @return object
     */
    public static function getUndeletedTrainings()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.training_heading,t1.training_start_date,t1.training_end_date
						from pndc_training t1
						join pndc_training_detail t2 on t1.id=t2.training_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }


    /**
     * get training detail with languages
     * @param  int $id
     * @return object
     */
    public static function getTrainingDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select training_heading from pndc_training t2 join pndc_training_detail t3
							on t2.id=t3.training_id where t3.language_id=t1.id and t2.id='$id') as training_heading,
					(select training_contact_person from pndc_training t2 join pndc_training_detail t3
							on t2.id=t3.training_id where t3.language_id=t1.id and t2.id='$id') as training_contact_person,
					(select training_contact_number from pndc_training t2 join pndc_training_detail t3
							on t2.id=t3.training_id where t3.language_id=t1.id and t2.id='$id') as training_contact_number,
					(select training_remarks from pndc_training t2 join pndc_training_detail t3
							on t2.id=t3.training_id where t3.language_id=t1.id and t2.id='$id') as training_remarks,
					(select training_page_title from pndc_training t2 join pndc_training_detail t3
							on t2.id=t3.training_id where t3.language_id=t1.id and t2.id='$id') as training_page_title,
					(select training_description from pndc_training t2 join pndc_training_detail t3
							on t2.id=t3.training_id where t3.language_id=t1.id and t2.id='$id') as training_description,
					(select number_of_traniees from pndc_training t2 join pndc_training_detail t3
							on t2.id=t3.training_id where t3.language_id=t1.id and t2.id='$id') as number_of_traniees,
					(select t3.id from pndc_training t2 join pndc_training_detail t3
							on t2.id=t3.training_id where t3.language_id=t1.id and t2.id='$id') as training_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active trainings
     * @param int $langId
     * @return object
     */
    public static function getActiveTrainings($langId)
    {
        // dd();
        $paginate = Configuration::getValueFromName('per_page_trainings');
        return Training::join('training_detail', 'training.id', '=', 'training_detail.training_id')
            ->select(
                'training.id',
                'training_url',
                'training.training_start_date',
                'training.training_end_date',
                'training_detail.training_heading',
                'training_detail.training_contact_person',
                'training_detail.training_contact_number',
                'training_detail.training_remarks',
                'number_of_traniees',
                DB::raw('
									date_format(pndc_training.training_start_date, "%d %b, %Y") as en_training_start_date,
									date_format(pndc_training.training_end_date, "%d %b, %Y") as en_training_end_date
									')
            )
            ->where('training_detail.language_id', $langId)
            ->where('training.is_active', '1')
            ->where('training.is_deleted', '0')
            ->paginate($paginate);
    }

    /**
     * get training detail
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getTrainingDetail($url, $langId)
    {
        $result = Training::join('training_detail', 'training.id', '=', 'training_detail.training_id')
            ->select(
                'training.id',
                'training_url',
                'training_meta_tags',
                'training_meta_description',
                'training_heading',
                'training.training_start_date',
                'training.training_end_date',
                'training_detail.training_description',
                'training_contact_person',
                // 'minimum_qualification',
                'number_of_traniees',
                'training_contact_number',
                'training_page_title',
                'training_remarks',
                DB::raw('
									date_format(pndc_training.training_start_date, "%d %b, %Y") as en_training_start_date,
									date_format(pndc_training.training_end_date, "%d %b, %Y") as en_training_end_date
									')
            )
            ->where('training.training_url', $url)
            ->where('training_detail.language_id', $langId)
            ->where('training.is_active', '1')
            ->where('training.is_deleted', '0')
            ->first();

        return $result;
    }
}

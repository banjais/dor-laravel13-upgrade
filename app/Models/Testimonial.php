<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Testimonial extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');
    protected $table = 'testimonial';


    /**
     * get all undeleted testimonials
     * @return object
     */
    public static function getUndeletedTestimonials()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.testimonial_auther
						from pndc_testimonial t1
						join pndc_testimonial_detail t2 on t1.id=t2.testimonial_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }


    /**
     * get testimonial detail with languages
     * @param  int $id
     * @return object
     */
    public static function getTestimonialDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_testimonial t2 where t2.id='$id') as testimonial_id,
					(select testimonial_attachment from pndc_testimonial t2 where t2.id='$id') as testimonial_attachment,
					(select is_active from pndc_testimonial t2 where t2.id='$id') as is_active,
					(select testimonial_auther from pndc_testimonial t2 join pndc_testimonial_detail t3
							on t2.id=t3.testimonial_id where t3.language_id=t1.id and t2.id='$id') as testimonial_auther,
					(select testimonial_description from pndc_testimonial t2 join pndc_testimonial_detail t3
							on t2.id=t3.testimonial_id where t3.language_id=t1.id and t2.id='$id') as testimonial_description,
					(select testimonial_company from pndc_testimonial t2 join pndc_testimonial_detail t3
							on t2.id=t3.testimonial_id where t3.language_id=t1.id and t2.id='$id') as testimonial_company,
					(select t3.id from pndc_testimonial t2 join pndc_testimonial_detail t3
							on t2.id=t3.testimonial_id where t3.language_id=t1.id and t2.id='$id') as testimonial_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active testimonials
     * @param int $langId
     * @return object
     */
    public static function getActiveTestimonials($langId)
    {
        return Testimonial::join('testimonial_detail', 'testimonial.id', '=', 'testimonial_detail.testimonial_id')
            ->select(
                'testimonial.id',
                'testimonial_attachment',
                'testimonial_detail.testimonial_auther',
                'testimonial_detail.testimonial_company',
                'testimonial_description'
            )
            ->where('testimonial_detail.language_id', $langId)
            ->where('testimonial.is_active', '1')
            ->where('testimonial.is_deleted', '0')
            ->get();
    }
}

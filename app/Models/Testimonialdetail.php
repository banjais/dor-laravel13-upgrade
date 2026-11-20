<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonialdetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'testimonial_detail';

    protected $fillable = array('testimonial_heading', 'testimonial_description', 'testimonial_page_title');
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Circulardetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'circular_detail';

    protected $fillable = array('circular_heading', 'circular_description', 'circular_page_title');
}

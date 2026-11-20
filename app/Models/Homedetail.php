<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homedetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('home_heading', 'officer_name', 'home_description', 'home_page_title');

    protected $table = 'home_detail';
}

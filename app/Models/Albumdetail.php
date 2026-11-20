<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Albumdetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'album_detail';

    protected $fillable = array('album_heading', 'album_description', 'album_page_title');
}

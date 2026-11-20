<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentDetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('content_heading', 'content_description', 'content_page_title');

    protected $table = 'content_detail';
}

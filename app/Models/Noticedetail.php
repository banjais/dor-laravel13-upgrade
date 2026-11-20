<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticedetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notice_detail';

    protected $fillable = array('notice_heading', 'notice_description', 'notice_page_title');
}

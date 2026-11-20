<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsdetail extends Model
{
    protected $fillable = array('news_heading', 'news_description', 'news_page_title');
    protected $table = 'news_detail';
}

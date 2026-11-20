<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bannerdetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'banner_detail';

	protected $fillable = array('banner_heading','banner_description','banner_page_title');
}

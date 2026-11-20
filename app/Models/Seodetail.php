<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seodetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'seo_detail';

	protected $fillable = array('page_title');
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorydetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'publication_category_detail';

	protected $fillable = array('category_heading','category_description','category_page_title');
}

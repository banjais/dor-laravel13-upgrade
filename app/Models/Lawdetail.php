<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lawdetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'law_detail';

	protected $fillable = array('law_heading','law_description','law_page_title');
}

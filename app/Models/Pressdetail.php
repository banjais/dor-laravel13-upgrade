<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pressdetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'press_detail';

	protected $fillable = array('press_heading','press_description','press_page_title');
}

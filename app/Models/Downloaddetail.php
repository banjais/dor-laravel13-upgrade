<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Downloaddetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'download_detail';

	protected $fillable = array('download_heading','download_description','download_page_title');
}

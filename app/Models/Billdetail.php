<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billdetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'bill_detail';

	protected $fillable = array('bill_heading','bill_description','bill_page_title');
}

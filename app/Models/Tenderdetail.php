<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenderdetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'tender_detail';

	protected $fillable = array('tender_heading','tender_description','tender_page_title');
}

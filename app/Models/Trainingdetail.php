<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainingdetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'training_detail';

	protected $fillable = array('training_heading','training_description','training_page_title');
}

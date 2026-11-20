<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeChiefDetail extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	*/
	protected $table = 'office_chief_details';

	protected $fillable = array('full_name');

    public function office_chief()
    {
        return $this->belongsTo(OfficeChief::class);
    }

    public function language()
    {
    	return $this->belongsTo(Language::class, 'language_id');
    }
}

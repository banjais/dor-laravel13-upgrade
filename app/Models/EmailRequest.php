<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailRequest extends Model
{
    protected $table = 'emailrequests';

	public function office()
    {
    	return $this->belongsTo(Office::class, 'office_id');
    }

    public function designation()
    {
    	return $this->belongsTo(StaffDesignation::class, 'designation_id');
    }
}

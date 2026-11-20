<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDesignation extends Model
{
    protected $table = 'staffdesignations';

	public function staffs()
    {
    	return $this->hasMany(Staff::class, 'designation_id');
    }
}

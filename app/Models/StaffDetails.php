<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDetails extends Model
{
    protected $fillable=array('full_name','permanent_address','temporary_address','mobile_number','contact_number','designation','label','staff_group','staff_subgroup','remarks','language_id','staff_id');

	protected $table = 'staff_details';

	public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function language()
    {
    	return $this->belongsTo(Language::class, 'language_id');
    }
}

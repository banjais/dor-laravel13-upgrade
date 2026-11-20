<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = array('staff_full_name', 'staff_attachment', 'staff_permanent_address', 'is_active', 'is_deleted', 'created_by', 'updated_by', 'staff_temporary_address', 'staff_mobile_number', 'staff_contact_number', 'date_of_birth', 'staff_gender', 'office_id', 'staff_designation', 'staff_status', 'staff_office_join_date', 'staff_office_leave_date', 'staff_email_address');

    protected $table = 'staffs';

    public function details()
    {
        return $this->hasMany(StaffDetails::class, 'staff_id');
    }

    public function designation()
    {
        return $this->belongsTo(StaffDesignation::class, 'designation_id');
    }

    public function level()
    {
        return $this->belongsTo(StaffLevel::class, 'level_id');
    }

    public function service()
    {
        return $this->belongsTo(StaffService::class, 'service_id');
    }

    public function group()
    {
        return $this->belongsTo(StaffGroup::class, 'group_id');
    }

    public function subgroup()
    {
        return $this->belongsTo(StaffSubgroup::class, 'subgroup_id');
    }
}

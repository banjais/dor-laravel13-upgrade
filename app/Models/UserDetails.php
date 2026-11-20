<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    protected $fillable = array('user_full_name', 'user_permanent_address', 'user_temporary_address', 'user_mobile_number', 'user_contact_number', 'user_designation', 'user_label', 'user_group', 'user_subgroup', 'user_remark', 'user_staff_id', 'user_office_join_date', 'user_office_leave_date', 'language_id', 'user_id');
    protected $table = 'user_details';
}

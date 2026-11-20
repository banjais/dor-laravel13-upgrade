<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeDetail extends Model
{
    protected $fillable = array('office_name', 'office_address', 'office_phone_number1', 'office_phone_number2', 'language_id', 'office_id');
    protected $table = 'office_detail';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginRequest extends Model
{
    protected $table = 'login_requests';
    protected $fillable = ['full_name','designation','email','office_name','office_address','office_phone','is_email_login','is_web_login','is_circular_login'];
}

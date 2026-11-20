<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserTypeModel extends Model
{
    protected $fillable = array('user_type_name', 'is_active', 'is_deleted', 'created_by', 'updated_by');
    protected $table = 'usertype';


    public static function getUndeletedUserTypes()
    {
        $query = "Select * from pndc_usertype where is_deleted='0'";
        return DB::select($query);
    }
    public static function isUsertypeUniqueForAdd($usertypename)
    {
        $query = "Select id as val from pndc_usertype where user_type_name='$usertypename' and is_deleted='0'";
        $record = DB::select($query);
        if (count($record) > 0) {
            return false;
        }
        return true;
    }
    public static function isUserTypeUniqueForUpdate($usertypename, $id)
    {
        $query = "Select id as val from pndc_usertype where user_type_name='$usertypename' and id<>$id and is_deleted='0'";
        $record = DB::select($query);
        if (count($record) > 0) {
            return false;
        }
        return true;
    }

    public static function getPublishedUserTypes()
    {
        $query = "Select * from pndc_usertype where id='1'";
        if (Auth::user()->user_type_id == 2) {
            $query = "Select * from pndc_usertype where is_active='1' and is_deleted='0'";
        }
        return DB::select($query);
    }
}

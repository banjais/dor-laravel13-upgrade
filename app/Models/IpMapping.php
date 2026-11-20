<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IpMapping extends Model
{
    protected $fillable = array('user_id', 'user_type_id', 'office_id', 'username', 'agent', 'login_ts', 'logout_ts', 'ip_address');
    protected $table = 'ip_mapping';

    /**
     * get all undeleted logs
     * @return object
     */
    public static function getUndeletedLogs()
    {
        // $query    = "Select * from pndc_ip_mapping order by id desc";

        // return DB::select($query);

        // return IpMapping::orderByDesc('id')->paginate();

        return DB::table('ip_mapping')->orderByDesc('id')->paginate();
    }
    /**
     * get all undeleted logs
     * @return object
     */
    public static function UserName($id)
    {
        $user = DB::table('user_details')->where('user_id', $id)->first();

        return $user->user_full_name;
    }
}

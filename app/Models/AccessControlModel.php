<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AccessControlModel extends Model
{
    protected $fillable = array('module_id', 'user_type_id', 'access_add', 'access_delete', 'access_update', 'access_publish', 'access_view', 'access_upload', 'access_download');
    protected $table = 'group_access_list';

    public static function getAccessListByUserTypeId($user_type_id)
    {
        $query = "Select
						module_id,
						mo.module_name,
						al.id,al.access_add,
						al.access_delete,
						al.access_update,
						al.access_publish,
						al.access_view,
						al.access_download,
						al.access_upload
					from pndc_group_access_list al
						join
							pndc_modules mo
								on
									mo.id=al.module_id
						where user_type_id= $user_type_id and is_deleted='0' ";
        return DB::select($query);
    }

    public static function checkAccessListItemByModuleAndUsertype($module_id, $usertype)
    {
        $query = "Select id from pndc_group_access_list where module_id=$module_id and user_type_id=$usertype";
        $result = DB::select($query);
        return (count($result)) ? false : true;
    }

    /*
	* get view list of accessed modules with view enable user wise
	* @param int $usertype
	* @return object
	*/
    public static function getAccessViewByUserType($usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_view', '1')
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* get list of accessed modules with add enable user wise
	* @param int $usertype
	* @return object
	*/
    public static function getAccessAddByUserType($usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_add', '1')
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* get list of accessed modules with update enable user wise
	* @param int $usertype
	* @return object
	*/
    public static function getAccessUpdateByUserType($usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_update', '1')
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* get list of accessed modules with publish enable user wise
	* @param int $usertype
	* @return object
	*/
    public static function getAccessPublishByUserType($usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_publish', '1')
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* get list of accessed modules with delete enable user wise
	* @param int $usertype
	* @return object
	*/
    public static function getAccessDeleteByUserType($usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_delete', '1')
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* check whether view access exist or not
	* @param int $moduleId
	* @param int $usertype
	* @return int
	*/
    public static function getAccessViewByModuleAndUserType($moduleId, $usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_view', '1')
            ->where('module_id', $moduleId)
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* check whether add access exist or not
	* @param int $moduleId
	* @param int $usertype
	* @return int
	*/
    public static function getAccessAddByModuleAndUserType($moduleId, $usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_add', '1')
            ->where('module_id', $moduleId)
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* check whether publish access exist or not
	* @param int $moduleId
	* @param int $usertype
	* @return int
	*/
    public static function getAccessPublishByModuleAndUserType($moduleId, $usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_publish', '1')
            ->where('module_id', $moduleId)
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* check whether update access exist or not
	* @param int $moduleId
	* @param int $usertype
	* @return int
	*/
    public static function getAccessUpdateByModuleAndUserType($moduleId, $usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_update', '1')
            ->where('module_id', $moduleId)
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }

    /*
	* check whether delete access exist or not
	* @param int $moduleId
	* @param int $usertype
	* @return int
	*/
    public static function getAccessDeleteByModuleAndUserType($moduleId, $usertype)
    {
        return $result = DB::table('group_access_list')
            ->where('access_delete', '1')
            ->where('module_id', $moduleId)
            ->where('user_type_id', $usertype)
            ->pluck('module_id');
    }
}

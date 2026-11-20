<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAccessControlModel extends Model
{
    protected $fillable = array('module_id', 'user_map_id', 'access_add', 'access_delete', 'access_update', 'access_publish', 'access_view', 'access_upload', 'access_download');
    protected $table = 'user_access_list';

    public static function getAccessListByUsermapId($usermapid)
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
					from pndc_user_access_list al
						join
							pndc_modules mo
								on
									mo.id=al.module_id
						where user_map_id= $usermapid  ";
        return DB::select($query);
    }

    public static function checkAccessListItemByModuleAndUserMapId($module_id, $usermapid)
    {
        $query = "Select id from pndc_user_access_list where module_id=$module_id and user_map_id=$usermapid";
        $result = DB::select($query);
        return (count($result)) ? false : true;
    }

    /*
	* get view list of accessed modules with view enable user wise
	* @param int $usermapid
	* @return object
	*/
    public static function getAccessViewByUserMapId($usermapid)
    {
        $result = DB::table('user_access_list')
            ->where('access_view', '1')
            ->where('user_map_id', $usermapid)
            ->select('module_id')
            ->get();

        if (count($result) == 0) {
            $usertype    = Auth::User()->user_type_id;
            $result = DB::table('group_access_list')
                ->join('modules', 'group_access_list.module_id', '=', 'modules.id')
                ->where('modules.is_deleted', '0')
                ->where('access_view', '1')
                ->where('user_type_id', $usertype)
                ->select('module_id')
                ->get();
        }

        return $result;
    }

    /*
	* get list of accessed modules with add enable user wise
	* @param int $usermapid
	* @return object
	*
	public static function getAccessAddByUserMapId($usermapid)
	{
		return $result = DB::table('user_access_list')
							->where('access_add', 1)
							->where('user_map_id', $usermapid)
							->lists('module_id');

	}

	/*
	* get list of accessed modules with update enable user wise
	* @param int $usermapid
	* @return object
	*
	public static function getAccessUpdateByUserMapId($usermapid)
	{
		return $result = DB::table('user_access_list')
							->where('access_update', 1)
							->where('user_map_id', $usermapid)
							->lists('module_id');

	}

	/*
	* get list of accessed modules with publish enable user wise
	* @param int $usermapid
	* @return object
	*
	public static function getAccessPublishByUserMapId($usermapid)
	{
		return $result = DB::table('user_access_list')
							->where('access_publish', 1)
							->where('user_map_id', $usermapid)
							->lists('module_id');

	}

	/*
	* get list of accessed modules with delete enable user wise
	* @param int $usermapid
	* @return object
	*
	public static function getAccessDeleteByUserMapId($usermapid)
	{
		return $result = DB::table('user_access_list')
							->where('access_delete', 1)
							->where('user_map_id', $usermapid)
							->lists('module_id');

	} */

    /*
	* check whether view access exist or not
	* @param int $moduleId
	* @param int $usermapid
	* @return int
	*/
    public static function getAccessViewByModuleAndUserMapId($moduleId, $usermapid)
    {
        $result = DB::table('user_access_list')
            ->where('access_view', '1')
            ->where('module_id', $moduleId)
            ->where('user_map_id', $usermapid)
            ->pluck('module_id')->first();

        if (!$result) {
            $usertype    = Auth::User()->user_type_id;
            $result = DB::table('group_access_list')
                ->where('access_view', '1')
                ->where('module_id', $moduleId)
                ->where('user_type_id', $usertype)
                ->pluck('module_id')->first();
        }

        return $result;
    }

    /*
	* check whether add access exist or not
	* @param int $moduleId
	* @param int $usermapid
	* @return int
	*/
    public static function getAccessAddByModuleAndUserMapId($moduleId, $usermapid)
    {
        $result = DB::table('user_access_list')
            ->where('access_add', '1')
            ->where('module_id', $moduleId)
            ->where('user_map_id', $usermapid)
            ->pluck('module_id')->first();
        if (!$result) {
            $usertype    = Auth::User()->user_type_id;
            $result = DB::table('group_access_list')
                ->where('access_add', '1')
                ->where('module_id', $moduleId)
                ->where('user_type_id', $usertype)
                ->pluck('module_id')->first();
        }
        // dd($result);
        return $result;
    }

    /*
	* check whether publish access exist or not
	* @param int $moduleId
	* @param int $usermapid
	* @return int
	*/
    public static function getAccessPublishByModuleAndUserMapId($moduleId, $usermapid)
    {
        $result = DB::table('user_access_list')
            ->where('access_publish', '1')
            ->where('module_id', $moduleId)
            ->where('user_map_id', $usermapid)
            ->pluck('module_id')->first();
        if (!$result) {
            $usertype    = Auth::User()->user_type_id;
            $result = DB::table('group_access_list')
                ->where('access_publish', '1')
                ->where('module_id', $moduleId)
                ->where('user_type_id', $usertype)
                ->pluck('module_id')->first();
        }
        return $result;
    }

    /*
	* check whether update access exist or not
	* @param int $moduleId
	* @param int $usermapid
	* @return int
	*/
    public static function getAccessUpdateByModuleAndUserMapId($moduleId, $usermapid)
    {

        $result = DB::table('user_access_list')
            ->where('access_update', '1')
            ->where('module_id', $moduleId)
            ->where('user_map_id', $usermapid)
            ->pluck('module_id')->first();


        if (!$result) {
            $usertype    = Auth::User()->user_type_id;
            $result = DB::table('group_access_list')
                ->where('access_update', '1')
                ->where('module_id', $moduleId)
                ->where('user_type_id', $usertype)
                ->pluck('module_id')->first();
        }
        return $result;
    }

    /*
	* check whether delete access exist or not
	* @param int $moduleId
	* @param int $usermapid
	* @return int
	*/
    public static function getAccessDeleteByModuleAndUserMapId($moduleId, $usermapid)
    {
        $result = DB::table('user_access_list')
            ->where('access_delete', '1')
            ->where('module_id', $moduleId)
            ->where('user_map_id', $usermapid)
            ->pluck('module_id')->first();

        if (!$result) {
            $usertype    = Auth::User()->user_type_id;
            $result = DB::table('group_access_list')
                ->where('access_delete', '1')
                ->where('module_id', $moduleId)
                ->where('user_type_id', $usertype)
                ->pluck('module_id')->first();
        }
        return $result;
    }

    /*
	* check whether download access exist or not
	* @param int $moduleId
	* @param int $usermapid
	* @return int
	*/
    public static function getAccessDownloadByModuleAndUserMapId($moduleId, $usermapid)
    {
        $result = DB::table('user_access_list')
            ->where('access_download', '1')
            ->where('module_id', $moduleId)
            ->where('user_map_id', $usermapid)
            ->pluck('module_id')->first();

        if (!$result) {
            $usertype    = Auth::User()->user_type_id;
            $result = DB::table('group_access_list')
                ->where('access_download', '1')
                ->where('module_id', $moduleId)
                ->where('user_type_id', $usertype)
                ->pluck('module_id')->first();
        }

        return $result;
    }
}

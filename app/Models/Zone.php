<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Zone extends Model
{
	protected $table = 'zones';

	/*
	* get zone lists
	* @return object
	*/
	public static function getActiveZones()
	{
		$languageId = Language::getIdFromCode('en');
		return Zone::join('zone_detail', 'zones.id', '=', 'zone_detail.zone_id')
					->select('zones.id', 'zone_detail.zone_name')
					->where('zone_detail.language_id', $languageId)
					->where('zones.is_active', '1')
					->get();
	}

	/*
	* get id from url
	* @param string $url
	* @return int
	*/
	public static function getIdfromUrl($url)
	{
		return Zone::where('is_active', '1')
					->where('zone_url', $url)
					->pluck('id');
	}

	/*
	* get zone name
	* @param int $langId
	* @param int $zoneId
	* @return string
	*/
	public static function getNameZone($langId, $zoneId)
	{
		return DB::table('zones')
					->join('zone_detail', 'zones.id', '=', 'zone_detail.zone_id')
					->where('zones.id', $zoneId)
					->where('zone_detail.language_id', $langId)
					->where('zones.is_active', 1)
					->pluck('zone_detail.zone_name');
	}

	/*
	* get zone lists with office count used for maps
	* @return object
	*/
	public static function getZoneOfficeData(){
		$query = "select t1.*, (select count(distinct t2.id) from pndc_office t2 where t2.is_active = '1' and t1.id=t2.zone_id and t2.is_deleted='0') as office_count,(select count(distinct t2.id) from pndc_office t2 where t2.is_active = '1' and t1.id=t2.zone_id and t2.is_deleted='0' and t2.office_type ='3') as office_mcount from pndc_zones t1 group by t1.id ";

		return DB::select($query);
	}
}

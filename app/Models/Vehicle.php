<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Vehicle extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'vehicles';

    public function details()
    {
    	return $this->hasMany(Vehicledetail::class,'vehicle_id');
    }

    /**
     * get all undeleted vehicles
     * @return object
     */
    public static function getUndeletedVehicles($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.vehicle_type,t1.created_at	from pndc_vehicles t1
						join pndc_vehicle_detail t2 on t1.id=t2.vehicle_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' and t1.office_id ='$officeId'";

        return DB::select($query);
    }

    /**
     * get vehicle detail with languages
     * @param  int $id
     * @return object
     */
    public static function getVehicleDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_vehicles t2 where t2.id='$id') as vehicle_id,
					(select t2.vehicle_name from pndc_vehicles t2 where t2.id='$id') as vehicle_name,
					(select t2.condition_type from pndc_vehicles t2 where t2.id='$id') as condition_type,

					(select t3.id from pndc_vehicles t2 join pndc_vehicle_detail t3
							on t2.id=t3.vehicle_id where t3.language_id=t1.id and t2.id='$id') as vehicle_detail_id,

          (select t3.vehicle_type from pndc_vehicles t2 join pndc_vehicle_detail t3
							on t2.id=t3.vehicle_id where t3.language_id=t1.id and t2.id='$id') as vehicle_type,

					(select t3.vehicle_number from pndc_vehicles t2 join pndc_vehicle_detail t3
							on t2.id=t3.vehicle_id where t3.language_id=t1.id and t2.id='$id') as vehicle_num,

          (select t3.condition from pndc_vehicles t2 join pndc_vehicle_detail t3
							on t2.id=t3.vehicle_id where t3.language_id=t1.id and t2.id='$id') as vehicle_condition
					from pndc_language t1
					where t1.is_active = '1' ";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active vehicles
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveVehicles($officeId, $langId)
    {
        $paginate = Configuration::getValueFromName('per_page_vehicles');
        return Vehicle::join('vehicle_detail', 'vehicles.id', '=', 'vehicle_detail.vehicle_id')
            ->select(
                'vehicles.id', 'vehicle_detail.vehicle_type', 'vehicle_detail.vehicle_number', 'condition',
                'condition_type',
                DB::raw('
									date_format(pndc_vehicle_detail.created_at, "%d %b, %Y") as en_created_at
									')
            )
            ->where('vehicles.office_id', $officeId)
            ->where('vehicle_detail.language_id', $langId)
            ->where('vehicles.is_active', '1')
            ->where('vehicles.is_deleted', '0')
            ->paginate(10);
    }
}

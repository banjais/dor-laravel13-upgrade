<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Equipment extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');
    protected $table = 'equipment';


    /**
     * get all undeleted equipments
     * @return object
     */
    public static function getUndeletedEquipments()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,t1.equipment_name,t1.is_active,
						(select sum(equipment_log) from pndc_equipment_log t2 where t1.id = t2.equipment_id ) as total_equipments
						from pndc_equipment t1
						where t1.is_deleted='0'";

        return DB::select($query);
    }


    /**
     * get equipment details
     * @param  int $id
     * @return object
     */
    public static function getEquipmentDetails($id)
    {
        return Equipment::where('id', $id)
            ->where('is_deleted', '0')
            ->first();
    }

    /*----------------------------------------------------*/

    /**
     * get all active equipments
     * @return object
     */
    public static function getActiveEquipments()
    {
        $paginate = Configuration::getValueFromName('per_page_equipments');
        return Equipment::where('is_active', '1')
            ->where('is_deleted', '0')
            ->paginate($paginate);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Equipmentlog extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'equipment_log';

    protected $fillable = array('');

    /**
     * check whether log exist or not for individual equipment and offices
     * @param  int $officeId
     * @param  int $equipmentId
     * @return int
     */
    public static function checkExist($officeId, $equipmentId)
    {
        return Equipmentlog::where('is_deleted', '0')
            ->where('office_id', $officeId)
            ->where('equipment_id', $equipmentId)
            ->get('id')->first();
    }

    /**
     * find equipment log for individual offices
     * @param  int $officeId
     * @param  int $equipmentId
     * @return int
     */
    public static function findLog($officeId, $equipmentId)
    {
        return Equipmentlog::where('is_deleted', '0')
            ->where('office_id', $officeId)
            ->where('equipment_id', $equipmentId)
            ->select('equipment_log')->first();
    }

    /**
     * get equipment detail with office log
     * @param int $id
     * @return object
     */
    public static function viewEquipmentlog($id)
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,t1.equipment_name, t2.equipment_log,
					t3.office_subdomain, t4.office_name, t4.office_address
					from pndc_equipment t1 join pndc_equipment_log t2 on t1.id=t2.equipment_id
					join pndc_office t3 on t2.office_id = t3.id
					join pndc_office_detail t4 on t3.id=t4.office_id
					where t1.is_deleted='0' and t1.id = '$id' and t2.is_deleted='0' and t4.language_id = '$languageId' ";

        return DB::select($query);
    }
}

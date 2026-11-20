<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModuleModel extends Model
{
    protected $fillable = array('module_name', 'is_deleted');
    protected $table = 'modules';

    public static function getUndeletedModules()
    {
        $query = "select id, module_name from pndc_modules where is_deleted='0'";
        return DB::select($query);
    }
}

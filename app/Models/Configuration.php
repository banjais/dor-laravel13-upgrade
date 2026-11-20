<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'configuration';

    /*
	| get configuration value from name
	| @param $name - string
	| @return string
	*/
    public static function getValueFromName($name)
    {
        return $result = Configuration::where('configuration_name', $name)
            ->pluck('configuration_value')->first();
    }
}

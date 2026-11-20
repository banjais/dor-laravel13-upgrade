<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'language';

    /*
	| get language name from code
	| @param $name - string
	| @return string
	*/
    public static function getNameFromCode($code = 'ne')
    {
        return $result = Language::where('language_code', $code)
            ->pluck('language_name');
    }

    /*
	| get language id (or primary key) of default language
	| @param $name - string
	| @return int
	*/
    public static function getIdFromCode($code = 'ne')
    {
        return $result = Language::where('language_code', $code)
            ->pluck('id')->first();
    }

    /*
	| get active languages
	| @return array
	*/
    public static function getActiveLanguages()
    {
        return $result = Language::where('is_active', '1')
            ->where('is_deleted', '0')
            ->get();
    }
}

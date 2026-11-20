<?php

use App\Models\Album;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

if (!function_exists('dateFormat')) {

    function dateFormat($date, $format = 'Y-m-d h:i:s A', $timezone = null)
    {
        $timezone = $timezone ?: config('app.timezone', 'UTC');

        return Carbon::parse($date)->timezone($timezone)->format($format);
    }
}

if (!function_exists('devanagari')) {
    function devanagari($num)
    {
        $num_nepali = array('०', '१', '२', '३', '४', '५', '६', '७', '८', '९');
        $num_eng = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $nums = str_replace($num_eng, $num_nepali, $num);
        return $nums;
    }
}

if (!function_exists('languageId')) {
    function languageId()
    {
        return Session::get('language_id');
    }
}

if (!function_exists('personHomeTitle')) {
    function personHomeTitle($code, $title_code)
    {
        switch ($code) {
            case 'value':
                # code...
                break;

            default:
                # code...
                break;
        }
    }
}

if (!function_exists('lastUpdateDate')) {
    function lastUpdateDate($officeId)
    {
        $tables = [
            [
                'table' => 'pndc_album',
                'has_office_id' => true
            ],
            [
                'table' => 'pndc_banner',
                'has_office_id' => true
            ],
            [
                'table' => 'pndc_content',
                'has_office_id' => true
            ],
            [
                'table' => 'pndc_faq',
                'has_office_id' => false
            ],
            [
                'table' => 'pndc_gallery',
                'has_office_id' => true
            ],
            [
                'table' => 'pndc_home',
                'has_office_id' => true
            ],
            [
                'table' => 'pndc_laws',
                'has_office_id' => false
            ],
            [
                'table' => 'pndc_news',
                'has_office_id' => true
            ],
            [
                'table' => 'pndc_notice',
                'has_office_id' => true
            ],
            // [
            //     'table' => 'pndc_office',
            //     'has_office_id' => true
            // ],
            [
                'table' => 'pndc_press',
                'has_office_id' => true
            ],
            [
                'table' => 'pndc_project',
                'has_office_id' => true
            ],
            [
                'table' => 'pndc_publication',
                'has_office_id' => true
            ]
        ];

        $sql = '';

        foreach ($tables as $key => $item) {
            $sql .= "(select max(updated_at) as updated_at from " . $item['table'] . " where is_active='1' and is_deleted='0' ";
            if ($item['has_office_id']) $sql .= ' and office_id=' . $officeId;
            $sql .= ")";
            if ($key < (count($tables) - 1)) {
                $sql .= ' UNION ';
            }
        }

        $the_sql = 'select max(updated_at) as updated_at from (' . $sql . ') as updated_at;';

        try {
            $x = DB::select($the_sql);
            return $x[0]->updated_at;
        } catch (\Throwable $th) {
            return '';
        }
    }
}

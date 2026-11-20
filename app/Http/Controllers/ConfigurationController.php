<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\Configuration;
use App\Models\Zone;

use Helper;
use Calendar;
use Illuminate\Support\Facades\Response;

class ConfigurationController extends Controller
{
    public static function abs_site_url()
    {
        return Configuration::getValueFromName('absolute_site_path');
    }

    public static function rel_site_url()
    {
        return Configuration::getValueFromName('relative_site_path');
    }

    public static function site_name()
    {
        return Configuration::getValueFromName('site_name');
    }

    /*
    * change session language
    * @param null
    * return JSON
    */
    public function changeLangugage(Request $request)
    {
        $languageId = $request->get('language_id');
        Session::put('language_id', $languageId);
        $returnArray['message'] = 'success';
        $returnArray['language_id'] = $languageId;

        //return json_encode($returnArray);
        return Response::json($returnArray);
    }

    /*
    * change captcha image
    * @param null
    * return JSON
    */
    public function changeCaptcha()
    {
        $captcha = Helper::create_captcha();
        $returnValue = '<img src="' . $captcha . '">';
        return $returnValue;
    }

    /*
    * get map data
    * @param null
    * return JSON
    */
    public function getMapdata()
    {
        $zones = Zone::getZoneOfficeData();
        foreach ($zones as $zone) {
            $Array[$zone->id] = array(
                'hc-key' => $zone->zone_hc_key,
                'value' => $zone->office_count,
                'url' => $zone->zone_url
            );
        }

        $returnArray['data'] = $Array;
        $returnArray['message'] = 'success';
        return json_encode($returnArray);
    }

    public function changeDateFromEngToNep()
    {
        //echo $nepaliDate = NepaliCalendar::eng_to_nep_dor(date("Y-m-d"));
        echo $nepaliDate = Calendar::eng_to_nep_dor(date("Y-m-d"));
    }

    /*
    * change session font size
    * @param null
    * return JSON
    */
    public function changeFontSize(Request $request)
    {
        $fontSizeCode = $request->get('font_size_code');
        $allowedFontSizes = ['a-minus-2', 'a-minus', 'a', 'a-plus', 'a-plus-2'];

        if (!in_array($fontSizeCode, $allowedFontSizes, true)) {
            return Response::json(['message' => 'Invalid font size.'], 422);
        }

        Session::put('font_size_code', $fontSizeCode);
        $returnArray['message'] = 'success';
        $returnArray['font_size_code'] = $fontSizeCode;

        //return json_encode($returnArray);
        return Response::json($returnArray);
    }

    /*
    * change session font size
    * @param null
    * return JSON
    */
    public function changeBandwidth(Request $request)
    {
        $bandwidthCode = Session::get('bandwidth_code', 'high');
        if ($bandwidthCode == 'high') {
            Session::put('bandwidth_code', 'low');
        } else {
            Session::put('bandwidth_code', 'high');
        }

        $returnArray['message'] = 'success';

        return json_encode($returnArray);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Helper;

use Illuminate\Support\Facades\DB;

class Office extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by', 'office_email_address', 'office_alternative_email');

    protected $table = 'office';

    public function details()
    {
        return $this->hasMany(OfficeDetail::class, 'office_id');
    }

    public function detail($langId = 2)
    {
        return $this->details()->where('language_id', $langId);
    }

    public static function getUndeletedOffices()
    {
        $languageId = Language::getIdFromCode('en');
        $query = "Select office.id as id,office.is_active as is_active,office_email_address, office_alternative_email, office_name, office_address, office_phone_number1, office_type, order_menu from pndc_office as office join pndc_office_detail detail on office.id=detail.office_id where office.is_deleted='0' and detail.language_id=" . $languageId . " order by `order_menu`";
        return DB::select($query);
    }

    public static function getPublishedOffices()
    {
        $languageId = Language::getIdFromCode('en');
        $query = "Select office.id as id,office.is_active as is_active,office_email_address, office_alternative_email, office_name, office_address, office_phone_number1 from pndc_office as office join pndc_office_detail detail on office.id=detail.office_id where office.is_deleted='0' and office.is_active='1' and detail.language_id=" . $languageId;
        return DB::select($query);
    }

    public static function getPublishedOfficesbyID($id)
    {
        $query = "Select office.id as id, office.is_active as is_active, office.office_email_address, office.office_alternative_email, detail.office_name, detail.office_address, detail.office_phone_number1 from pndc_office as office join pndc_office_detail detail on office.id=detail.office_id where office.is_deleted='0' and office.is_active='1' and detail.language_id=2 and office.id=" . $id;
        return DB::select($query);
    }

    public static function getOfficeDetailsWithLanguages($id, $language_id = null)
    {
        if ($language_id) {
            $query = "Select lang.id, language_name, language_code,
            (select id from pndc_office_detail where lang.id=language_id and office_id=$id) as detailid,
            (select office_subdomain from pndc_office where  id=$id) as office_subdomain,
            (select office_name from pndc_office_detail where lang.id=language_id and office_id=$id) as office_name,
            (select office_address from pndc_office_detail where lang.id=language_id and office_id=$id) as office_address,
            (select office_phone_number1 from pndc_office_detail where lang.id=language_id and office_id=$id) as office_phone_number1,
            (select office_phone_number2 from pndc_office_detail where lang.id=language_id and office_id=$id) as office_phone_number2
            from pndc_language lang where lang.is_active='1' and lang.is_deleted='0' and lang.id=$language_id";
        } else {
            $query = "Select lang.id, language_name, language_code,
            (select id from pndc_office_detail where lang.id=language_id and office_id=$id) as detailid,
            (select office_subdomain from pndc_office where  id=$id) as office_subdomain,
            (select office_name from pndc_office_detail where lang.id=language_id and office_id=$id) as office_name,
            (select office_address from pndc_office_detail where lang.id=language_id and office_id=$id) as office_address,
            (select office_phone_number1 from pndc_office_detail where lang.id=language_id and office_id=$id) as office_phone_number1,
            (select office_phone_number2 from pndc_office_detail where lang.id=language_id and office_id=$id) as office_phone_number2
            from pndc_language lang where lang.is_active='1' and lang.is_deleted='0'";
        }

        // print_r($query);exit;
        return DB::select($query);
    }

    /*
	* get active offices ids
	* @return array
	*/
    public static function getActiveOfficesIds()
    {

        return Office::where('is_active', '1')
            ->where('is_deleted', '0')
            ->select('id')->get();
    }

    /*
	* get office id from office subdomain
	* @param string $subdomain
	* @return int
	*/
    public static function getIdfromSubdomain($subdomain)
    {
        // dd($subdomain);
        return Office::where('office_subdomain', $subdomain)
            ->where('is_deleted', '0')
            ->where('is_active', '1')
            ->pluck('id')
            ->first();
    }

    /*
	* get office name from office subdomain
	* @param string $subdomain
	* @param int $langId
	* @return string
	*/
    public static function getNamefromSubdomain($subdomain, $langId)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('office.office_subdomain', $subdomain)
            ->where('language_id', $langId)
            ->pluck('office_name');
    }

    /*
	* get office subdomain from office id
	* @param int $id
	* @return string
	*/
    public static function getSubdomainfromId($id)
    {
        return Office::where('id', $id)
            ->where('is_deleted', '0')
            ->where('is_active', '1')
            ->pluck('office_subdomain');
    }

    /*
	* get detail of office
	* @param int $officeId
	* @param int $langId
	* @return object
	*/
    public static function getOfficeDetailFromId($officeId, $langId)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.office_subdomain',
                'office.office_email_address',
                'office.office_latitude',
                'office.office_longitude',
                'office.office_attachment',
                'office_detail.office_name',
                'office_detail.office_address',
                'office_detail.office_phone_number1',
                'office_detail.office_phone_number2'
            )
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('office.id', $officeId)
            ->where('language_id', $langId)
            ->first();
    }

    /*
	* get detail of office
	* @param string $subdomain
	* @param int $langId
	* @return object
	*/
    public static function getOfficeDetailFromSubdomain($subdomain, $langId)
    {
        // print_r($subdomain);exit;
        // DB::enableQueryLog();
        $data = Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')

            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('office.office_subdomain', $subdomain)
            ->where('language_id', $langId)
            ->select(
                'office.id',
                'office_subdomain',
                'office_email_address',
                'office_alternative_email',
                'office_facebook_page',
                'office_instagram_page',
                'office_latitude',
                'office_longitude',
                'google_map_embed',
                'office_type',
                'office_attachment',
                'office_detail.office_name',
                'office_detail.office_address',
                'office_detail.office_phone_number1',
                'office_detail.office_phone_number2'
            )
            ->first();

        // dd(Helper::get_last_query());
        return $data;
    }

    /*
	* get office list
	* @param int $langId
	* @param string $subdomain
	* @param int $limit
	* @return object
	*/
    public static function getActiveRegionalOffices($langId, $subdomain, $limit = 10)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.office_subdomain',
                'office_detail.office_name'
            )
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('office_detail.language_id', $langId)
            ->where('office.office_type', 'like', '%2%')
            ->orderby('office.order_menu', 'ASC')
            ->take($limit)
            ->get();
    }

    /*
	* get office list
	* @param int $langId
	* @param string $subdomain
	* @param int $limit
	* @return object
	*/
    public static function getActiveHEDivisionsOffices($langId, $zoneId, $subdomain, $limit = 10)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.office_subdomain',
                'office_detail.office_name'
            )
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('zone_id', $zoneId)
            ->where('office_detail.language_id', $langId)
            ->where('office.office_type', 'like', '%4%')
            ->take($limit)
            ->get();
    }

    /*
	* get office list
	* @param int $langId
	* @param string $subdomain
	* @param int $limit
	* @return object
	*/
    public static function getActiveMechanicalOffices($langId, $zoneId, $subdomain, $limit = 10)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.office_subdomain',
                'office_detail.office_name'
            )
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('zone_id', $zoneId)
            ->where('office_detail.language_id', $langId)
            ->where('office.office_type', 'like', '%5%')
            ->take($limit)
            ->get();
    }

    /*
	* get office list zone wise
	* @param int $langId
	* @param int $zoneId
	* @return object
	*/
    public static function getOfficeListZonewise($langId, $zoneId)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.office_subdomain',
                'office_detail.office_name'
            )
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('language_id', $langId)
            ->where('zone_id', $zoneId)
            ->where('office_type', 'like', '%%')
            ->get();
    }

    /*
	* get office list zone wise
	* @param int $langId
	* @param int $officeType
	* @return object
	*/
    public static function getOfficesByType($langId, $officeType)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.office_subdomain',
                'office_detail.office_name'
            )
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('language_id', $langId)
            ->where('office_type', 'like', '%' . $officeType . '%')
            ->orderby('office.order_menu', 'ASC')
            ->get();
    }


    /**
     * get content list hierarchy wise
     * @param  int $parentId
     * @param  int $level
     * @param  int $selected
     * @return string
     */
    public static function content_list_for_Regional($officeId, $parentId, $level, $selected)
    {
        $languageId = Language::getIdFromCode('en');
        $offices = Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.parent_id',
                'office_detail.office_name'
            )
            ->where('office.office_type', $officeId)
            ->where('office.parent_id', $parentId)
            ->where('office.is_active', 1)
            ->where('office_detail.language_id', $languageId)
            ->orderby('office.id', 'asc')
            ->get();

        $returnList = '<select class="m-wrap span6" name="parent_id" id="parent_id">';
        $returnList .= '<option value="0" selected>Parent Itself</option>';
        foreach ($offices as $office) {
            if ($selected > 0 && $selected == $office->id) {
                $selected_option = 'selected="selected"';
            } else {
                $selected_option = "";
            }

            $returnList .= '<option value="' . $office->id . '" ' . $selected_option . '>' .
                str_repeat('&nbsp;--|- &nbsp;', $level) . $office->office_name . '</option>';
            $returnList .= self::suboffice_list_for_Regional($officeId, $office->id, $level + 1, $selected);
        }
        $returnList .= '</select>';

        return $returnList;
    }

    /**
     * get sub content list hierarchy wise
     * @param  int $parentId
     * @param  int $level
     * @param  int $selected
     * @return string
     */
    public static function suboffice_list_for_Regional($officeId, $parentId, $level, $selected = 0)
    {
        $languageId = Language::getIdFromCode('en');
        $subcontents = Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.parent_id',
                'office_detail.office_name'
            )
            ->where('office.office_type', $officeId)
            ->where('office.parent_id', $parentId)
            ->where('office.is_active', 1)
            ->where('office_detail.language_id', $languageId)
            ->orderby('office.id', 'asc')
            ->get();

        $returnList = '';
        foreach ($subcontents as $subcontent) {
            if ($selected > 0 && $selected == $subcontent->id) {
                $selected_option = 'selected="selected"';
            } else {
                $selected_option = "";
            }
            $returnList .= '<option value="' . $subcontent->id . '" ' . $selected_option . '>' .
                str_repeat('&nbsp;--|- &nbsp;', $level) . $subcontent->office_name . '</option>';
            $returnList .= self::suboffice_list_for_Regional($officeId, $subcontent->id, $level + 1, $selected);
        }
        return $returnList;
    }

    /**
     * get sub content list hierarchy wise
     * @param  int $parentId
     * @param  int $level
     * @param  int $selected
     * @return string
     */
    public static function suboffice_Regional($parentId, $languageId)
    {
        // $languageId = Language::getIdFromCode('en');
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.parent_id',
                'office_detail.office_name',
                'office.office_subdomain'
            )
            ->where('office.parent_id', $parentId)
            ->where('office.office_type', '3')
            ->where('office.is_active', 1)
            ->where('office_detail.language_id', $languageId)
            ->orderby('office.id', 'asc')
            ->get();
    }
    /**
     * get sub content list hierarchy wise
     * @param  int $parentId
     * @param  int $level
     * @param  int $selected
     * @return string
     */
    public static function suboffice_AllRegional($parentId, $languageId)
    {
        // $languageId = Language::getIdFromCode('en');
        // print_r($languageId);exit;
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.parent_id',
                'office_detail.office_name',
                'office.office_subdomain'
            )
            ->where('office.office_type', '3')
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('office_detail.language_id', $languageId)
            ->orderby('office.id', 'asc')
            ->get();
    }
    public static function suboffice_harakpur($parentId, $languageId)
    {
        // $languageId = Language::getIdFromCode('en');
        // print_r($languageId);exit;
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.parent_id',
                'office_detail.office_name',
                'office.office_subdomain'
            )
            ->where('office.office_type', '3')
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('office_detail.language_id', $languageId)
            ->where('office.id', '131')
            ->get();
    }

    /*
	* get detail of office
	* @param int $officeId
	* @param int $langId
	* @return object
	*/
    public static function getOfficeDetailById($id)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select(
                'office.id',
                'office.office_subdomain',
                'office.office_email_address',
                'office.office_latitude',
                'office.office_longitude',
                'office_detail.office_name',
                'office_detail.office_address',
                'office_detail.office_phone_number1',
                'office_detail.office_phone_number2'
            )
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('language_id', 2)
            ->where('office.id', $id)
            ->first();
    }

    /*
	* get office list
	* @param int $langId
	* @param string $subdomain
	* @param int $limit
	* @return object
	*/
    public static function getActiveSearchOffices($officename, $limit = 10)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select('office_detail.office_name')
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('office_detail.language_id', '2')
            // ->where('office.office_type', 'like', '%2%')
            ->where('office_detail.office_name', 'like', '%' . $officename . '%')
            ->orderby('office_detail.office_name', 'ASC')
            ->take($limit)
            ->get();
    }
    /*
	* get office list
	* @param int $langId
	* @param string $subdomain
	* @param int $limit
	* @return object
	*/
    public static function getofficeId($officename)
    {
        return Office::join('office_detail', 'office.id', '=', 'office_detail.office_id')
            ->select('office.id')
            ->where('office.is_active', '1')
            ->where('office.is_deleted', '0')
            ->where('office_detail.language_id', '2')
            ->where('office_detail.office_name', 'like', '%' . $officename . '%')
            ->pluck('id');
    }
}

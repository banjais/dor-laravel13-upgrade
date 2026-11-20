<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = array('user_full_name', 'user_attachment', 'user_permanent_address', 'is_active', 'is_deleted', 'created_by', 'updated_by', 'user_temporary_address', 'user_mobile_number', 'user_contact_number', 'date_of_birth', 'user_gender', 'office_id', 'user_designation', 'user_status', 'user_office_join_date', 'user_office_leave_date', 'user_email_address');

    protected $table = 'users';

    protected $dates = ['deleted_at'];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function usertype()
    {
        return $this->belongsTo(UserTypeModel::class, 'user_type_id');
    }

    /*
	* get office id of user from user id
	* @param int $userId
	* @return int
	*/
    public static function getOfficeId($userId)
    {
        return User::where('is_deleted', '0')
            ->where('id', $userId)
            ->pluck('office_id')
            ->first();
    }

    public static function getUndeletedUsers()
    {
        $languageId = Language::getIdFromCode('en');
        $query = "Select user.id as  id,user.office_id, (select user_full_name  from pndc_user_details where user_id=user.id and language_id=" . $languageId . " ) as user_full_name,user_name,user.is_active as  is_active , user_type_name, user_email_address from pndc_users user left join pndc_user_map usermp on user.id=usermp.user_id left join pndc_usertype type on type.id=usermp.user_type_id   where user.is_deleted='0'";
        return DB::select($query);
    }
    /*get undeleted users acccording to office id*/
    public static function getUndeletedUsersByoffice($office_id)
    {
        $languageId = Language::getIdFromCode('en');
        $query = "Select user.id as  id,user.office_id,user.user_attachment, (select user_full_name  from pndc_user_details where user_id=user.id and language_id=" . $languageId . " ) as user_full_name,user_name,user.is_active as  is_active , user_type_name, user_email_address from pndc_users user left join pndc_user_map usermp on user.id=usermp.user_id left join pndc_usertype type on type.id=usermp.user_type_id   where user.is_deleted='0' and user.office_id=$office_id order by 'order_position'";
        return DB::select($query);
    }

    public static function getUserDetailsByIdForUpdate($id)
    {
        $query = "Select lang.language_code, lang.language_name ,
					(select user_full_name         from pndc_user_details where user_id=$id and language_id=lang.id) as user_full_name,
					(select user_permanent_address from pndc_user_details where user_id=$id and language_id=lang.id) as user_permanent_address,
					(select user_temporary_address from pndc_user_details where user_id=$id and language_id=lang.id) as user_temporary_address,
					(select user_mobile_number     from pndc_user_details where user_id=$id and language_id=lang.id) as user_mobile_number,
					(select user_contact_number    from pndc_user_details where user_id=$id and language_id=lang.id) as user_contact_number,
					(select user_designation       from pndc_user_details where user_id=$id and language_id=lang.id) as user_designation,
					(select user_label       from pndc_user_details where user_id=$id and language_id=lang.id) as user_label,
					(select user_service       from pndc_user_details where user_id=$id and language_id=lang.id) as user_service,
					(select user_group       from pndc_user_details where user_id=$id and language_id=lang.id) as user_group,
					(select user_subgroup       from pndc_user_details where user_id=$id and language_id=lang.id) as user_subgroup,
					(select user_remark       from pndc_user_details where user_id=$id and language_id=lang.id) as user_remark,
					(select user_staff_id          from pndc_user_details where user_id=$id and language_id=lang.id) as user_staff_id,
					(select user_office_join_date  from pndc_user_details where user_id=$id and language_id=lang.id) as user_office_join_date,
					(select user_office_leave_date from pndc_user_details where user_id=$id and language_id=lang.id) as user_office_leave_date,
					(select id from pndc_user_details where user_id=$id and language_id=lang.id) as detail_id


					from pndc_language lang where is_deleted='0' and is_active='1'";

        return DB::select($query);
    }

    /*------------------------------------------------------*/

    /**
     * get all active staffs
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveStaffs($officeId, $langId)
    {
        return User::join('user_details', 'users.id', '=', 'user_details.user_id')
            ->select(
                'users.id',
                'order_position',
                'user_attachment',
                'users.user_email_address',
                'user_details.user_contact_number',
                'user_details.user_full_name',
                'user_details.user_designation',
                'user_details.user_label',
                'user_details.user_service',
                'user_details.user_group',
                'user_details.user_subgroup',
                'user_details.user_staff_id',
                'user_details.user_remark'
            )
            ->where('users.office_id', $officeId)
            ->where('user_details.language_id', $langId)
            ->where('users.is_active', '1')
            ->where('users.is_deleted', '0')
            ->orderby('order_position')
            ->get();
    }

    /**
     * get user detail
     * @param int $officeId
     * @param int $userId
     * @param int $langId
     * @return object
     */
    public static function getUserDetail($officeId, $userId, $langId)
    {
        $result = User::join('user_details', 'users.id', '=', 'user_details.user_id')
            ->select(
                'users.id',
                'users.office_id',
                'users.user_email_address',
                'users.user_attachment',
                'user_details.user_full_name',
                'user_details.user_designation',
                'user_details.user_contact_number'
            )
            ->where('users.office_id', $officeId)
            ->where('users.id', $userId)
            ->where('user_details.language_id', $langId)
            ->where('users.is_active', '1')
            ->where('users.is_deleted', '0')
            ->first();
        return $result;
    }

    public static function getUndeletedSortUsers($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query = "Select user.id as  id, (select user_full_name
				from pndc_user_details
				where user_id=user.id and
				language_id=" . $languageId . " ) as user_full_name,
				user_name, user.is_active as  is_active,
				user_attachment, order_position
				from pndc_users user left join pndc_user_map usermp
				on user.id=usermp.user_id left join
				pndc_usertype type on type.id=usermp.user_type_id
				where user.is_deleted='0' and user.office_id = '$officeId'
				order by order_position";
        return DB::select($query);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserMap extends Model
{
    protected $fillable = array('user_id', 'user_name', 'user_type_id', 'password', 'is_active', 'is_deleted', 'created_by', 'updated_by');
    protected $table = 'user_map';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public static function getUndeletedUsers()
    {
        $query = "Select * from pndc_user_map where is_deleted=0";
        return DB::select($query);
    }
    public static function checkUsernameForAdd($username)
    {
        $query = "Select id from pndc_user_map where user_name='$username' and  is_deleted='0'";
        $record = DB::select($query);
        return (count($record) > 0) ? false : true;
    }
    public static function getUserByEmail($email, $id)
    {
        $query = "Select count(*) as val from pndc_user_map where email='$email' and id<>$id";
        return DB::select($query);
    }

    public static function getUserMapIdByUserId($userid)
    {
        $query = "Select id from pndc_user_map where user_id=$userid";
        $user = DB::select($query);
        return $user[0]->id;
    }

    public static function getActiveUserMapWithDetails()
    {
        $language = Language::getFirstActiveLanguages();
        $languageid = $language->id;
        $query = "SELECT map.id,map.user_type_id,map.user_name,user.office_id,(
					select concat(
						user_full_name,'
						( ',(select office_name from pndc_office_detail where office_id=user.office_id and language_id=$languageid),' ) ')
						from pndc_user_details det
						   where user_id=map.user_id and language_id=$languageid)as detail
					FROM `pndc_user_map` as map join pndc_users user on user.id=map.user_id   WHERE user.is_active='1' and user.is_deleted='0' and user_id IS NOT NULL";
        return DB::select($query);
    }

    public static function getUserMapWithDetails($id)
    {
        $language = Language::getFirstActiveLanguages();
        $languageid = $language->id;
        $query = "SELECT map.id,map.user_type_id,map.user_name,user.office_id,(
					select concat(
						user_full_name,'
						( ',(select office_name from pndc_office_detail where office_id=user.office_id and language_id=$languageid),' ) ')
						from pndc_user_details det
						   where user_id=map.user_id and language_id=$languageid)as detail
					FROM `pndc_user_map` as map join pndc_users user on user.id=map.user_id   WHERE user.is_active='1' and user.is_deleted='0' and map.id=$id ";
        return DB::select($query);
    }
    public static function getUserMapWithDetailById($id, $languageid = '1')
    {

        $query = "SELECT map.id,map.user_type_id,map.user_name,map.password,user.office_id,(
					select concat(
						user_full_name,'
						( ',(select office_name from pndc_office_detail where office_id=user.office_id and language_id=$languageid),' ) ')
						from pndc_user_details det
						   where user_id=map.user_id and language_id=$languageid)as detail
					FROM `pndc_user_map` as map join pndc_users user on user.id=map.user_id   WHERE user.is_active='1' and user.is_deleted='0' and map.user_id=$id ";
        return DB::select($query);
    }
}

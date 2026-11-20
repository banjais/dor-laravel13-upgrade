<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Album extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'album';

   //SCOPE
   public function scopePublished($query)
   {
       return $query->where(['is_active'=> '1', 'is_deleted'=> '0']);
   }

   public function scopeLast($query)
   {
       return $query->published()->orderByDesc('updated_at');
   }

    public function details()
    {
        return $this->hasMany(Albumdetail::class, 'album_id');
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'album_id');
    }


    /**
     * get all undeleted albums
     * @return object
     */
    public static function getUndeletedAlbums()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.album_title
						from pndc_album t1
						join pndc_album_detail t2 on t1.id=t2.album_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted albums office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedAlbumsByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t1.album_attachment,t2.album_title
						from pndc_album t1
						join pndc_album_detail t2 on t1.id=t2.album_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' and t1.office_id='$officeId' ";

        return DB::select($query);
    }

    /**
     * check whether url exist or not for individual office
     * @param  string $url
     * @param  int $officeId
     * @return int
     */
    public static function checkExist($url, $officeId)
    {
        return $albums = Album::where('is_deleted', '0')
            ->where('album_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get album detail with languages
     * @param  int $id
     * @return object
     */
    public static function getAlbumDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_album t2 where t2.id='$id') as album_id,
					(select album_url from pndc_album t2 where t2.id='$id') as album_url,
					(select album_attachment from pndc_album t2 where t2.id='$id') as album_attachment,
					(select is_active from pndc_album t2 where t2.id='$id') as is_active,
					(select album_title from pndc_album t2 join pndc_album_detail t3
							on t2.id=t3.album_id where t3.language_id=t1.id and t2.id='$id') as album_title,
					(select album_caption from pndc_album t2 join pndc_album_detail t3
							on t2.id=t3.album_id where t3.language_id=t1.id and t2.id='$id') as album_caption,
					(select t3.id from pndc_album t2 join pndc_album_detail t3
							on t2.id=t3.album_id where t3.language_id=t1.id and t2.id='$id') as album_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active albums
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveAlbums($officeId, $langId)
    {

        $query = "select t1.id, t1.album_url, t1.album_attachment,t2.album_title,t2.album_caption,
					(select count(*) from pndc_gallery t3 where t3.album_id=t1.id and t3.is_active='1' )as count_photos
					from pndc_album t1 join pndc_album_detail t2 on t1.id=t2.album_id
					where t1.office_id='$officeId' and t1.is_active ='1' and t1.is_deleted = '0'
							and t2.language_id = '$langId' ";
        return DB::select($query);
    }

    /**
     * get seo detail album wise
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     * This function is not used currently as select columns does not exist in DB
     */
    public static function getSeoDetailsByUrlAndLanguage($officeId, $url, $langId)
    {
        return Album::join('album_detail', 'album.id', '=', 'album_detail.album_id')
            ->select('album_page_title', 'album_meta_tags', 'album_meta_description')
            ->where('album.office_id', $officeId)
            ->where('album_detail.language_id', $langId)
            ->where('album.album_url', $url)
            ->where('album.is_active', '1')
            ->where('album.is_deleted', '0')
            ->first();
    }

    /**
     * get album name
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return string
     */
    public static function getAlbumNameByUrlAndLanguage($officeId, $url, $langId)
    {
        return Album::join('album_detail', 'album.id', '=', 'album_detail.album_id')
            ->where('album.office_id', $officeId)
            ->where('album_detail.language_id', $langId)
            ->where('album.album_url', $url)
            ->where('album.is_active', '1')
            ->where('album.is_deleted', '0')
            ->pluck('album_title')
            ->first();
    }
}

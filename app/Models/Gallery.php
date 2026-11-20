<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Gallery extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'gallery';

    public function details()
    {
        return $this->hasMany(Gallerydetail::class, 'gallery_id');
    }

    /**
     * get all undeleted galleries
     * @return object
     */
    public static function getUndeletedGalleries()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.gallery_title
						from pndc_gallery t1
						join pndc_gallery_detail t2 on t1.id=t2.gallery_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted galleries office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedGalleriesByOffice($albumId, $officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t1.gallery_attachment,t2.gallery_title
						from pndc_gallery t1
						join pndc_gallery_detail t2 on t1.id=t2.gallery_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' and t1.album_id='$albumId' and t1.office_id='$officeId'";

        return DB::select($query);
    }

    /**
     * get gallery detail with languages
     * @param  int $id
     * @return object
     */
    public static function getGalleryDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_gallery t2 where t2.id='$id') as gallery_id,
					(select album_id from pndc_gallery t2 where t2.id='$id') as album_id,
					(select gallery_attachment from pndc_gallery t2 where t2.id='$id') as gallery_attachment,
					(select is_active from pndc_gallery t2 where t2.id='$id') as is_active,
					(select gallery_title from pndc_gallery t2 join pndc_gallery_detail t3
							on t2.id=t3.gallery_id where t3.language_id=t1.id and t2.id='$id') as gallery_title,
					(select gallery_caption from pndc_gallery t2 join pndc_gallery_detail t3
							on t2.id=t3.gallery_id where t3.language_id=t1.id and t2.id='$id') as gallery_caption,
					(select t3.id from pndc_gallery t2 join pndc_gallery_detail t3
							on t2.id=t3.gallery_id where t3.language_id=t1.id and t2.id='$id') as gallery_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active galleries
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveGalleries($officeId, $url, $langId)
    {

        return Album::join('gallery', 'album.id', '=', 'gallery.album_id')
            ->join('gallery_detail', 'gallery.id', '=', 'gallery_detail.gallery_id')
            ->select(
                'gallery.id',
                'gallery.album_id',
                'album.album_url',
                'gallery_attachment',
                'gallery_detail.gallery_title',
                'gallery_detail.gallery_caption'
            )
            ->where('album.album_url', $url)
            ->where('gallery.office_id', $officeId)
            ->where('gallery_detail.language_id', $langId)
            ->where('gallery.is_active', '1')
            ->where('gallery.is_deleted', '0')
            ->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Download extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $fillable=array('is_active','is_deleted','created_by','updated_by');

	protected $table = 'download';

	public function details()
	{
		return $this->hasMany(Downloaddetail::class, 'download_id');
	}


	/**
	* get all undeleted downloads
	* @return object
	*/
	public static function getUndeletedDownloads(){
		$languageId = Language::getIdFromCode('en');
		$query	= "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.download_heading
						from pndc_download t1
						join pndc_download_detail t2 on t1.id=t2.download_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

		return DB::select($query);
	}

	/**
	* get all undeleted downloads office wise
	* @param int $officeId
	* @return object
	*/
	public static function getUndeletedDownloadsByOffice($officeId){
		$languageId = Language::getIdFromCode('en');
		$query	= "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t1.download_attachment,t2.download_heading
						from pndc_download t1
						join pndc_download_detail t2 on t1.id=t2.download_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' and t1.office_id='$officeId' ";

		return DB::select($query);
	}

	/**
	* get download detail with languages
	* @param  int $id
	* @return object
	*/
	public static function getDownloadDetailsWithLanguages($id)
	{
		$query	= "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_download t2 where t2.id='$id') as download_id,
					(select download_heading from pndc_download t2 join pndc_download_detail t3
							on t2.id=t3.download_id where t3.language_id=t1.id and t2.id='$id') as download_heading,
					(select t3.id from pndc_download t2 join pndc_download_detail t3
							on t2.id=t3.download_id where t3.language_id=t1.id and t2.id='$id') as download_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

		return DB::select($query);
	}

	/*----------------------------------------------------*/

	/**
	* get all active downloads
	* @param int $officeId
	* @param int $langId
	* @return object
	*/
	public static function getActiveDownloads($officeId, $langId, $perPage=null){
        if(!$perPage){
            $paginate = Configuration::getValueFromName('per_page_notices');
        }
        else{
            $paginate = $perPage;
        }

		return Download::join('download_detail', 'download.id', '=', 'download_detail.download_id')
					->select(
								'download.id', 'download.download_url', 'download_attachment',
								'download_detail.download_heading', 'published_date',
								DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
							)
					->where('download.office_id', $officeId)
					->where('download_detail.language_id', $langId)
					->where('download.is_active', '1')
					->where('download.is_deleted', '0')
					->orderby('published_date', 'desc')
					->paginate($paginate);
	}

	/**
	* get active downloads home page
	* @param int $officeId
	* @param int $langId
	* @return object
	*/
	public static function getHomePageDownloads($officeId, $langId){
		$limit = Configuration::getValueFromName('home_page_news');
		return Download::join('download_detail', 'download.id', '=', 'download_detail.download_id')
					->select(
								'download.id', 'download.download_url', 'download_attachment',
								'download_detail.download_heading', 'published_date',
								DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
							)
					->where('download.office_id', $officeId)
					->where('download_detail.language_id', $langId)
					->where('download.is_active', '1')
					->where('download.is_deleted', '0')
					->take($limit)
					->get();
	}

	/**
	* get download detail by url
	* @param int $officeId
	* @param string $url
	* @param int $langId
	* @return object
	*/
	public static function getDownloadDetail($url, $langId){
		return Download::join('download_detail', 'download.id', '=', 'download_detail.download_id')
					->select(
								'download.id', 'download.download_url', 'download.download_meta_tags', 'download.download_meta_description', 'download_attachment',
								'download_detail.download_heading', 'download_detail.download_page_title', 'download_detail.download_description', 'published_date',
								DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
							)
					->where('download.download_url', $url)
					->where('download_detail.language_id', $langId)
					->where('download.is_active', '1')
					->where('download.is_deleted', '0')
					->first();
	}

	/**
	* get download detail by id
	* @param int $officeId
	* @param int $id
	* @param int $langId
	* @return object
	*/
	public static function getDownloadDetailById($officeId, $id, $langId){
		return Download::join('download_detail', 'download.id', '=', 'download_detail.download_id')
					->select(
								'download.id', 'download.download_url', 'download.download_meta_tags', 'download.download_meta_description', 'download_attachment',
								'download_detail.download_heading', 'download_detail.download_page_title', 'download_detail.download_description', 'published_date',
								DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
							)
					->where('download.office_id', $officeId)
					->where('download.id', $id)
					->where('download_detail.language_id', $langId)
					->where('download.is_active', '1')
					->where('download.is_deleted', '0')
					->first();
	}
}

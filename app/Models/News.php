<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class News extends Model
{
    public $languageId;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $fillable=array('is_active','is_deleted','created_by','updated_by');

	protected $table = 'news';

	public function details()
	{
		return $this->hasMany(Newsdetail::class, 'news_id');
	}


	/**
	* get all undeleted newss
	* @return object
	*/
	public static function getUndeletedNewss(){
		$languageId = Language::getIdFromCode('en');
		$query	= "Select t1.id,t1.news_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.news_heading
						from pndc_news t1
						join pndc_news_detail t2 on t1.id=t2.news_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

		return DB::select($query);
	}

	/**
	* get all undeleted newss office wise
	* @param int $officeId
	* @return object
	*/
	public static function getUndeletedNewssByOffice($officeId){
		$languageId = Language::getIdFromCode('en');
		$query	= "Select t1.id,t1.news_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.news_heading
						from pndc_news t1
						join pndc_news_detail t2 on t1.id=t2.news_id
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
		return News::where('is_deleted', '0')
					->where('news_url', $url)
					->where('office_id', $officeId)
                    ->pluck('id')->first();
	}

	/**
	* get news detail with languages
	* @param  int $id
	* @return object
	*/
	public static function getNewsDetailsWithLanguages($id)
	{
		$query	= "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_news t2 where t2.id='$id') as news_id,
					(select news_heading from pndc_news t2 join pndc_news_detail t3
							on t2.id=t3.news_id where t3.language_id=t1.id and t2.id='$id') as news_heading,
					(select news_description from pndc_news t2 join pndc_news_detail t3
							on t2.id=t3.news_id where t3.language_id=t1.id and t2.id='$id') as news_description,
					(select news_page_title from pndc_news t2 join pndc_news_detail t3
							on t2.id=t3.news_id where t3.language_id=t1.id and t2.id='$id') as news_page_title,
					(select t3.id from pndc_news t2 join pndc_news_detail t3
							on t2.id=t3.news_id where t3.language_id=t1.id and t2.id='$id') as news_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

		return DB::select($query);
	}

	/*----------------------------------------------------*/

	/**
	* get all active newss
	* @param int $officeId
	* @param int $langId
	* @return object
	*/
	public static function getActiveNews($officeId, $langId){
		$paginate = Configuration::getValueFromName('per_page_news');
		return News::join('news_detail', 'news.id', '=', 'news_detail.news_id')
					->select(
								'news.id', 'news.news_url', 'news_attachment',
								'news_detail.news_heading', 'published_date',
								DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
							)
					->where('news.office_id', $officeId)
					->where('news_detail.language_id', $langId)
					->where('news.is_active', '1')
					->where('news.is_deleted', '0')
					->orderBy('published_date', 'desc')
					->paginate($paginate);
	}

	/**
	* get active news on home page
	* @param int $officeId
	* @param int $langId
	* @return object
	*/
	public static function getHomePageNews($officeId, $langId, $limit=null){
        if(!$limit)
		    $limit = Configuration::getValueFromName('home_page_news');
		return News::join('news_detail', 'news.id', '=', 'news_detail.news_id')
					->select(
								'news.id', 'news.news_url', 'news_attachment',
								'news_detail.news_heading', 'published_date',
								DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
							)
->where('news.office_id', $officeId)
					->where('news_detail.language_id', $langId)
					->where('news.is_active', '1')
					->where('news.is_deleted', '0')
                    ->orderByDesc('news.created_at')
					->take($limit)
					->get();
	}





	/**
	* get news detail by url
	* @param int $officeId
	* @param string $url
	* @param int $langId
	* @return object
	*/
	public static function getNewsDetail($officeId, $url, $langId){
		return News::join('news_detail', 'news.id', '=', 'news_detail.news_id')
					->select(
								'news.id', 'news.news_url', 'news.news_meta_tags', 'news.news_meta_description', 'news_attachment',
								'news_detail.news_heading', 'news_detail.news_page_title', 'news_detail.news_description', 'published_date',
								DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
							)
					->where('news.office_id', $officeId)
					->where('news.news_url', $url)
					->where('news_detail.language_id', $langId)
					->where('news.is_active', '1')
					->where('news.is_deleted', '0')
					->first();
	}

	/**
	* get news detail by id
	* @param int $officeId
	* @param int $id
	* @param int $langId
	* @return object
	*/
	public static function getNewsDetailById($officeId, $id, $langId){
		return News::join('news_detail', 'news.id', '=', 'news_detail.news_id')
					->select(
								'news.id', 'news.news_url', 'news.news_meta_tags', 'news.news_meta_description', 'news_attachment',
								'news_detail.news_heading', 'news_detail.news_page_title', 'news_detail.news_description', 'published_date',
								DB::raw('
									date_format(published_date, "%d %b, %Y") as en_published_date
									')
							)
					->where('news.office_id', $officeId)
					->where('news.id', $id)
					->where('news_detail.language_id', $langId)
					->where('news.is_active', '1')
					->where('news.is_deleted', '0')
					->first();
	}
}

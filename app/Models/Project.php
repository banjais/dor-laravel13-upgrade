<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table    = 'project';

    public function details()
    {
    	return $this->hasMany(Projectdetail::class, 'project_id');
    }

    public function detailEng()
    {
        return $this->details()->where('language_id', 2);
    }

    public function detailNep()
    {
        return $this->details()->where('language_id', 1);
    }

    /**
     * get all undeleted projects
     * @return object
     */
    public static function getUndeletedProjects()
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.project_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.project_heading
						from pndc_project t1
						join pndc_project_detail t2 on t1.id=t2.project_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    public static function getPublishedProjects(){
        $languageId = Language::getIdFromCode('en');

        $query      = "Select t1.id,t1.project_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
                        t1.is_active,t2.project_heading
                        from pndc_project t1
                        join pndc_project_detail t2 on t1.id=t2.project_id
                        where t1.is_deleted='0' and t1.is_active = '1' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted projects office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedProjectsByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.project_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.project_heading
						from pndc_project t1
						join pndc_project_detail t2 on t1.id=t2.project_id
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
        return Project::where('is_deleted', '0')
            ->where('project_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get project detail with languages
     * @param  int $id
     * @return object
     */
    public static function getProjectDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_project t2 where t2.id='$id') as project_id,
					(select project_heading from pndc_project t2 join pndc_project_detail t3
							on t2.id=t3.project_id where t3.language_id=t1.id and t2.id='$id') as project_heading,
					(select project_description from pndc_project t2 join pndc_project_detail t3
							on t2.id=t3.project_id where t3.language_id=t1.id and t2.id='$id') as project_description,
					(select project_page_title from pndc_project t2 join pndc_project_detail t3
							on t2.id=t3.project_id where t3.language_id=t1.id and t2.id='$id') as project_page_title,
					(select t3.id from pndc_project t2 join pndc_project_detail t3
							on t2.id=t3.project_id where t3.language_id=t1.id and t2.id='$id') as project_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*------------------------------------------------------*/

    /**
     * get all active projects
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveProjects($officeId, $langId)
    {
        $limit = Configuration::getValueFromName('per_page_projects');
        return Project::join('project_detail', 'project.id', '=', 'project_detail.project_id')
            ->select(
                'project.id', 'project.project_url', 'project_class', 'project_ref_no', 'project_link_code', 'project_black_tapped', 'project_gravel', 'project_earthen', 'project_under_construction', 'project_plan',
                'project_detail.project_heading',
                DB::raw('project_black_tapped + project_gravel + project_earthen as project_total')
            )
            ->where('project.office_id', $officeId)
            ->where('language_id', $langId)
            ->where('project.is_active', '1')
            ->where('project.is_deleted', '0')
            ->orderBy('project_detail.project_heading', 'asc')
            ->paginate($limit);
    }

    /**
     * get project detail
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getProjectDetail($officeId, $url, $langId)
    {
        $result = Project::join('project_detail', 'project.id', '=', 'project_detail.project_id')
            ->select(
                'project.id', 'project.project_url', 'project.project_meta_tags', 'project.project_meta_description', 'project_latitude', 'project_longitude', 'project_zoom',
                'project_detail.project_heading', 'project_detail.project_page_title', 'project_detail.project_description',
                'project_detail.project_location','google_map_embed'
            )
            ->where('project.office_id', $officeId)
            ->where('project.project_url', $url)
            ->where('project_detail.language_id', $langId)
            ->where('project.is_active', '1')
            ->where('project.is_deleted', '0')
            ->first();
        return $result;
    }
}

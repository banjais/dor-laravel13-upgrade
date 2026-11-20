<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Content extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');

    protected $table = 'content';

    public function details()
    {
        return $this->hasMany(ContentDetail::class, 'content_id');
    }

    /**
     * get all undeleted contents
     * @return object
     */
    public static function getUndeletedContents()
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.content_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.content_heading
						from pndc_content t1
						join pndc_content_detail t2 on t1.id=t2.content_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }

    /**
     * get all undeleted contents office wise
     * @param int $officeId
     * @return object
     */
    public static function getUndeletedContentsByOffice($officeId)
    {
        $languageId = Language::getIdFromCode('en');
        $query      = "Select t1.id,t1.content_url,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.content_heading
						from pndc_content t1
						join pndc_content_detail t2 on t1.id=t2.content_id
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
        return Content::where('is_deleted', '0')
            ->where('content_url', $url)
            ->where('office_id', $officeId)
            ->pluck('id')
            ->first();
    }

    /**
     * get content detail with languages
     * @param  int $id
     * @return object
     */
    public static function getContentDetailsWithLanguages($id)
    {
        $query = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_content t2 where t2.id='$id') as content_id,
					(select t2.parent_id from pndc_content t2 where t2.id='$id') as parent_id,
					(select content_url from pndc_content t2 where t2.id='$id') as content_url,
					(select show_in from pndc_content t2 where t2.id='$id') as show_in,
					(select order_menu from pndc_content t2 where t2.id='$id') as order_menu,
					(select content_meta_tags from pndc_content t2 where t2.id='$id') as content_meta_tags,
					(select content_meta_description from pndc_content t2 where t2.id='$id') as content_meta_description,
					(select content_attachment from pndc_content t2 where t2.id='$id') as content_attachment,
					(select is_active from pndc_content t2 where t2.id='$id') as is_active,
					(select content_heading from pndc_content t2 join pndc_content_detail t3
							on t2.id=t3.content_id where t3.language_id=t1.id and t2.id='$id') as content_heading,
					(select content_description from pndc_content t2 join pndc_content_detail t3
							on t2.id=t3.content_id where t3.language_id=t1.id and t2.id='$id') as content_description,
					(select content_page_title from pndc_content t2 join pndc_content_detail t3
							on t2.id=t3.content_id where t3.language_id=t1.id and t2.id='$id') as content_page_title,
					(select t3.id from pndc_content t2 join pndc_content_detail t3
							on t2.id=t3.content_id where t3.language_id=t1.id and t2.id='$id') as content_detail_id
					from pndc_language t1
					where t1.is_active = '1' ";

        return DB::select($query);
    }

    /**
     * get content list hierarchy wise
     * @param  int $parentId
     * @param  int $level
     * @param  int $selected
     * @return string
     */
    public static function content_list_for_contentEntry($officeId, $parentId, $level, $selected)
    {
        $languageId = Language::getIdFromCode('en');
        $contents   = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.parent_id',
                'content_detail.content_heading'
            )
            ->where('content.office_id', $officeId)
            ->where('content.parent_id', $parentId)
            ->where('content.is_active', '1')
            ->where('content_detail.language_id', $languageId)
            ->orderby('content.id', 'asc')
            ->get();

        $returnList = '<select class="m-wrap span6" name="parent_id" id="parent_id">';
        $returnList .= '<option value="0" selected>Parent Itself</option>';
        foreach ($contents as $content) {
            if ($selected > 0 && $selected == $content->id) {
                $selected_option = 'selected="selected"';
            } else {
                $selected_option = "";
            }

            $returnList .= '<option value="' . $content->id . '" ' . $selected_option . '>' .
                str_repeat('&nbsp;--|- &nbsp;', $level) . $content->content_heading . '</option>';
            $returnList .= self::subcontent_list_for_contentEntry($officeId, $content->id, $level + 1, $selected);
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
    public static function subcontent_list_for_contentEntry($officeId, $parentId, $level, $selected = 0)
    {
        $languageId  = Language::getIdFromCode('en');
        $subcontents = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.parent_id',
                'content_detail.content_heading'
            )
            ->where('content.office_id', $officeId)
            ->where('content.parent_id', $parentId)
            ->where('content.is_active', '1')
            ->where('content_detail.language_id', $languageId)
            ->orderby('content.id', 'asc')
            ->get();

        $returnList = '';
        foreach ($subcontents as $subcontent) {
            if ($selected > 0 && $selected == $subcontent->id) {
                $selected_option = 'selected="selected"';
            } else {
                $selected_option = "";
            }
            $returnList .= '<option value="' . $subcontent->id . '" ' . $selected_option . '>' .
                str_repeat('&nbsp;--|- &nbsp;', $level) . $subcontent->content_heading . '</option>';
            $returnList .= self::subcontent_list_for_contentEntry($officeId, $subcontent->id, $level + 1, $selected);
        }
        return $returnList;
    }

    /*------------------------------------------------------*/
    /**
     * get content detail
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getContentDetail($officeId, $url, $langId)
    {
        $result = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.parent_id',
                'content.content_url',
                'content.content_meta_tags',
                'content.content_meta_description',
                'content_attachment',
                'content_detail.content_heading',
                'content_detail.content_page_title',
                'content_detail.content_description',
                'content.office_id'
            )
            ->where('content.office_id', $officeId)
            ->where('content.content_url', $url)
            ->where('content_detail.language_id', $langId)
            ->where('content.is_active', '1')
            ->where('content.is_deleted', '0')
            ->first();
        return $result;
    }

    /**
     * get content detail
     * @param int $officeId
     * @param int $id
     * @param int $langId
     * @return object
     */
    public static function getContentDetailById($officeId, $id, $langId)
    {
        $result = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.parent_id',
                'content.content_url',
                'content.content_meta_tags',
                'content.content_meta_description',
                'content_attachment',
                'content_detail.content_heading',
                'content_detail.content_page_title',
                'content_detail.content_description'
            )
            ->where('content.office_id', $officeId)
            ->where('content.id', $id)
            ->where('content_detail.language_id', $langId)
            ->where('content.is_active', '1')
            ->where('content.is_deleted', '0')
            ->orderby('content.order_menu', 'asc')
            ->first();
        return $result;
    }

    /**
     * get active content lists
     * @param int $langId
     * @param string $subdomain
     * @param int $showIn
     * @param int $limit
     * @return object
     */
    public static function getContentsPostionWise($langId, $subdomain, $showIn, $parentId = '0', $limit = '4')
    {
        $officeId = Office::getIdfromSubdomain($subdomain);
        $result   = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.content_url',
                'content_detail.content_heading'
            )
            ->where('content.office_id', $officeId)
            ->where('content_detail.language_id', $langId)
            ->where('content.show_in', 'LIKE', '%' . $showIn . '%')
            ->where('content.parent_id', $parentId)
            ->where('content.is_active', '1')
            ->where('content.is_deleted', '0')
            ->orderby('content.order_menu', 'asc')
            ->take($limit)
            ->get();
        return $result;
    }

    /**
     * get child content lists
     * @param int $officeId
     * @param int $parentId
     * @param int $langId
     * @return object
     */
    public static function getChildContents($officeId, $parentId, $langId)
    {
        $result = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.content_url',
                'content_detail.content_heading'
            )
            ->where('content.office_id', $officeId)
            ->where('content_detail.language_id', $langId)
            ->where('content.parent_id', $parentId)
            ->where('content.is_active', '1')
            ->where('content.is_deleted', '0')
            ->orderby('content.order_menu', 'asc')
            ->get();
        return $result;
    }

    /**
     * get sub content list hierarchy wise
     * @param  int $parentId
     * @param  int $level
     * @param  int $selected
     * @return string
     */
    public static function content_list_for_attachment($parentId)
    {
        $subcontents = Content::join('content_documents', 'content.id', '=', 'content_documents.content_id')
            ->select(
                'content.id',
                'content.parent_id',
                'content_documents.document_name',
                'content_documents.document_attachment',
                'content_documents.id as doc_id'
            )
            ->where('content_documents.content_id', $parentId)
            ->where('content_documents.is_active', '1')
            ->orderby('content.id', 'asc')
            ->get();

        $returnList = '<table class="table table-striped table-hover table-bordered"><tr><th>Name</th><th>Action</th></tr>';
        foreach ($subcontents as $subcontent) {

            $returnList .= '<tr><td align="center">' . $subcontent->document_name . '</td><td align="center">	<a href="javascript:void(0)" class="delete-content" id="' . $subcontent->doc_id . '" ><i class="icon-trash" ></i>Delete</a></td></tr>';
        }
        $returnList .= '</table>';
        return $returnList;
    }
    /**
     * get sub content list hierarchy wise
     * @param  int $parentId
     * @param  int $level
     * @param  int $selected
     * @return string
     */
    public static function content_alllist_for_attachment($parentId)
    {
        $subcontents = Content::join('content_documents', 'content.id', '=', 'content_documents.content_id')
            ->select(
                'content_documents.id',
                'content.parent_id',
                'content_documents.document_name',
                'content_documents.document_attachment',
                'content_documents.id as doc_id'
            )
            ->where('content_documents.content_id', $parentId)
            ->where('content_documents.is_active', '1')
            ->orderby('content.id', 'asc')
            ->get();
        return $subcontents;
    }

    /*------------------------------------------------------*/
    /**
     * get content detail
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getDocContentDetail($id)
    {
        $result = Content::join('content_documents', 'content.id', '=', 'content_documents.content_id')
            ->select(
                'content_documents.id',
                'content.parent_id',
                'content_documents.document_name',
                'content_documents.document_attachment',
                'content_documents.id as doc_id'
            )
            ->where('content_documents.id', $id)
            ->where('content_documents.is_active', '1')
            ->orderby('content.id', 'asc')
            ->first();
        return $result;
    }

    /*------------------------------------------------------*/
    /**
     * get content detail
     * @param int $officeId
     * @param string $url
     * @param int $langId
     * @return object
     */
    public static function getContentDetailUrl($url, $officeId)
    {
        $result = Content::join('content_detail', 'content.id', '=', 'content_detail.content_id')
            ->select(
                'content.id',
                'content.parent_id',
                'content.content_url',
                'content.content_meta_tags',
                'content.content_meta_description',
                'content_attachment',
                'content_detail.content_heading',
                'content_detail.content_page_title',
                'content_detail.content_description'
            )
            ->where('content.office_id', $officeId)
            ->where('content.content_url', $url)
            // ->where('content_detail.language_id', $langId)
            ->where('content.is_active', '1')
            ->where('content.is_deleted', '0')
            ->first();
        return $result;
    }
}

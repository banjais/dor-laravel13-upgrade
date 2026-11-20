<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projectdetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('project_heading', 'project_description', 'project_page_title');

    protected $table    = 'project_detail';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicationdetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'publication_detail';

    protected $fillable = array('publication_heading', 'publication_description', 'publication_page_title');
}

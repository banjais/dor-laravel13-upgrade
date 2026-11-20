<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tickerdetail extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('ticker_title', 'ticker_desc', 'ticker_id');
    protected $table = 'ticker_detail';
}

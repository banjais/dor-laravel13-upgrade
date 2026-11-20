<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ticker extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    //protected $fillable=array('is_active','is_deleted','created_by','updated_by');

    protected $table = 'ticker';

    public function details()
    {
        return $this->hasMany('Tickerdetail', 'ticker_id');
    }
    public static function getHomePageNewsTicker($officeId, $langId)
    {
        //$limit = Configuration::getValueFromName('home_page_news');
        $data = Ticker::join('ticker_detail', 'ticker.id', '=', 'ticker_detail.ticker_id')
            ->select(
                'ticker.id',
                'ticker_detail.ticker_title',
                'ticker_detail.ticker_des',
                'ticker_detail.ticker_id',
                DB::raw('
									date_format(pndc_ticker_detail.updated_at, "%d %b, %Y") as created_at
									'),
                DB::raw('
									date_format(pndc_ticker_detail.created_at, "%d %b, %Y") as updated_at
									')

            )
            ->where('ticker.office_id', $officeId)
            ->where('ticker_detail.language_id', $langId)
            ->where('ticker.is_active', '1')
            ->where('ticker.is_delete', '0')
            ->orderbyDesc('created_at')

            ->get();

        return $data;
    }

    public static function getNewsTickertDetail($officeId, $ticker_id, $langId)
    {
        $result = Ticker::join('ticker_detail', 'ticker.id', '=', 'ticker_detail.ticker_id')
            ->select(
                'ticker.id',
                'ticker_detail.ticker_title',
                'ticker_detail.ticker_des',
                DB::raw('
									date_format(pndc_ticker_detail.updated_at, "%d %b, %Y") as created_at
									'),
                DB::raw('
									date_format(pndc_ticker_detail.created_at, "%d %b, %Y") as updated_at
									')
            )
            ->where('ticker.office_id', $officeId)
            ->where('ticker.id', $ticker_id)
            ->where('ticker_detail.language_id', $langId)
            ->where('ticker.is_active', '1')
            ->where('ticker.is_delete', '0')
            ->first();
        return $result;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bill extends Model
{
    public $languageId;

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $fillable = array('is_active', 'is_deleted', 'created_by', 'updated_by');
    protected $table = 'bills';


    /**
     * get all undeleted bills
     * @return object
     */
    public static function getUndeletedBills()
    {
        $languageId = Language::getIdFromCode('en');
        $query    = "Select t1.id,date_format( t1.updated_at, '%d %b, %Y' ) AS updated_on,
						t1.is_active,t2.bill_particular, t2.bill_received_by
						from pndc_bills t1
						join pndc_bill_detail t2 on t1.id=t2.bill_id
						where t1.is_deleted='0' and t2.language_id = '$languageId' ";

        return DB::select($query);
    }


    /**
     * get bill detail with languages
     * @param  int $id
     * @return object
     */
    public static function getBillDetailsWithLanguages($id)
    {
        $query    = "select t1.id as language_id,t1.language_name,
					(select t2.id from pndc_bills t2 where t2.id='$id') as bill_id,
					(select bill_particular from pndc_bills t2 join pndc_bill_detail t3
							on t2.id=t3.bill_id where t3.language_id=t1.id and t2.id='$id') as bill_particular,
					(select bill_pcode_num from pndc_bills t2 join pndc_bill_detail t3
							on t2.id=t3.bill_id where t3.language_id=t1.id and t2.id='$id') as bill_pcode_num,
					(select bill_expense_head from pndc_bills t2 join pndc_bill_detail t3
							on t2.id=t3.bill_id where t3.language_id=t1.id and t2.id='$id') as bill_expense_head,
					(select bill_buying_head from pndc_bills t2 join pndc_bill_detail t3
							on t2.id=t3.bill_id where t3.language_id=t1.id and t2.id='$id') as bill_buying_head,
					(select bill_received_by from pndc_bills t2 join pndc_bill_detail t3
							on t2.id=t3.bill_id where t3.language_id=t1.id and t2.id='$id') as bill_received_by,
					(select bill_remarks from pndc_bills t2 join pndc_bill_detail t3
							on t2.id=t3.bill_id where t3.language_id=t1.id and t2.id='$id') as bill_remarks,
					(select t3.id from pndc_bills t2 join pndc_bill_detail t3
							on t2.id=t3.bill_id where t3.language_id=t1.id and t2.id='$id') as bill_detail_id
					from pndc_language t1
					where t1.is_active = '1'";

        return DB::select($query);
    }

    /*----------------------------------------------------*/

    /**
     * get all active bills
     * @param int $officeId
     * @param int $langId
     * @return object
     */
    public static function getActiveBills($officeId, $langId)
    {
        $paginate = Configuration::getValueFromName('per_page_bills');
        return Bill::join('bill_detail', 'bills.id', '=', 'bill_detail.bill_id')
            ->select(
                'bills.id',
                'bill_attachment',
                'bill_pan_number',
                'bill_amount',
                'bill_detail.bill_particular',
                'bill_pcode_num',
                'bill_expense_head',
                'bill_buying_head',
                'bill_received_by',
                'bill_received_date',
                'bill_detail.bill_remarks',
                DB::raw('
									date_format(bill_received_date, "%d %b, %Y") as en_bill_received_date
									')
            )
            ->where('bills.office_id', $officeId)
            ->where('bill_detail.language_id', $langId)
            ->where('bills.is_active', '1')
            ->where('bills.is_deleted', '0')
            ->paginate($paginate);
    }
}

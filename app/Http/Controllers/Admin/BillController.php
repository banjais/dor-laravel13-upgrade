<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Bill;
use App\Models\Billdetail;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BillController extends ConfigurationController
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;
    public $officeId;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name    = ConfigurationController::site_name();
    }

    /*
     * get bill list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(23, Auth::User()->id);

        if ($viewAccess) {
            $bills = Bill::getUndeletedBills();
            //echo Configuration::get_last_query(); exit();

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(23, Auth::User()->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(23, Auth::User()->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(23, Auth::User()->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(23, Auth::User()->id);

            return view('bills.admin.list')
                ->with('bills', $bills)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addAccess', $addAccess)
                ->with('editAccess', $editAccess)
                ->with('publishAccess', $publishAccess)
                ->with('deleteAccess', $deleteAccess);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get bill add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddBill()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(23, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('bills.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * post bill add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addBill(Request $request)
    {
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $officeId = Auth::user()->office_id;

        try {
            $rules = ['bill_amount' => 'required'];
            $rules = ['bill_received_date' => 'required'];
            foreach ($languages as $language) {
                $rules['bill_particular_' . $language->id]   = 'required';
                $rules['bill_expense_head_' . $language->id] = 'required';
                $rules['bill_buying_head_' . $language->id]  = 'required';
                $rules['bill_received_by_' . $language->id]  = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                Session::flash('class', 'alert alert-error');
                Session::flash('message', 'Some fields with * are missing.');
                return view('bills::admin.add')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('messages', $messages);
            } else {

                $imageFile        = $request->file('attachment');
                $destinationPath  = 'uploads/bill/';
                $rEFileTypes      = "/^\.(png|jpg|jpeg){1}$/i";
                $maximum_filesize = 2 * 1024 * 1024;

                if ($imageFile) {
                    $filename       = $imageFile->getClientOriginalName();
                    $extension      = strrchr($filename, '.');
                    $size           = $imageFile->getSize();
                    $new_image_name = "bill" . "_" . time();

                    if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                        $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                    } else if (preg_match($rEFileTypes, $extension) == false) {
                        Session::flash('attachment-error', 'Warning : Invalid Image File!');
                    } else if ($size > $maximum_filesize) {
                        Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                    }
                }
                $logo = isset($attachment) ? $new_image_name . $extension : null;

                $objectBill                     = new Bill();
                $objectBill->office_id          = $officeId;
                $objectBill->bill_attachment    = $logo;
                $objectBill->bill_amount        = $request->get('bill_amount');
                $objectBill->bill_pan_number    = $request->get('bill_pan_number');
                $objectBill->bill_received_date = $request->get('bill_received_date');
                $objectBill->is_active          = $request->get('is_active');
                $objectBill->created_by         = Auth::user()->id;
                $objectBill->updated_by         = Auth::user()->id;
                $objectBill->save();

                if ($objectBill->id) {
                    foreach ($languages as $language) {
                        $insertArray[] = [
                            'bill_id'           => $objectBill->id,
                            'language_id'       => $language->id,
                            'bill_particular'   => $request->get('bill_particular_' . $language->id),
                            'bill_pcode_num'    => $request->get('bill_pcode_num_' . $language->id),
                            'bill_expense_head' => $request->get('bill_expense_head_' . $language->id),
                            'bill_buying_head'  => $request->get('bill_buying_head_' . $language->id),
                            'bill_received_by'  => $request->get('bill_received_by_' . $language->id),
                            'bill_remarks'      => $request->get('bill_remarks_' . $language->id),
                            'created_at'        => date("Y-m-d H:i:s"),
                            'updated_at'        => date("Y-m-d H:i:s"),
                        ];
                    }
                    DB::table('bill_detail')->insert($insertArray);

                    Session::flash('class', 'alert alert-success');
                    Session::flash('message', 'New bill is added successfully.');
                    return view('bills::admin.add')
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name)
                        ->with('languages', $languages);
                }
            }
        } catch (Throwable $e) {
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view('bills::admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages);
        }
    }

    /**
     * get bill edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getEditBill($id)
    {

        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(23, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(23, Auth::User()->id);
            $billData   = Bill::find($id);

            if ($billData && $editAccess) {
                $languages = Language::getActiveLanguages();
                $bills     = Bill::getBillDetailsWithLanguages($id);
                //echo Configuration::get_last_query(); exit();

                return view('bills.admin.edit')
                    ->with('bills', $bills)
                    ->with('billData', $billData)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('addAccess', $addAccess);
            } else {
                return view('error')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name);
            }
        } catch (Throwable $e) {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * post bill edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editBill(Request $request)
    {
        $billId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        $bills     = Bill::getBillDetailsWithLanguages($billId);
        $officeId  = Auth::user()->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(23, Auth::User()->id);
        $billData  = Bill::find($billId);

        try {

            $rules = ['bill_amount' => 'required'];
            $rules = ['bill_received_date' => 'required'];
            foreach ($languages as $language) {
                $rules['bill_particular_' . $language->id]   = 'required';
                $rules['bill_expense_head_' . $language->id] = 'required';
                $rules['bill_buying_head_' . $language->id]  = 'required';
                $rules['bill_received_by_' . $language->id]  = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                Session::flash('class', 'alert alert-error');
                Session::flash('message', 'Some fields are missing');
                return view('bills.admin.edit')
                    ->with('bills', $bills)
                    ->with('$billData', $billData)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('addAccess', $addAccess);
            } else {

                $imageFile        = $request->file('attachment');
                $destinationPath  = 'uploads/bill/';
                $rEFileTypes      = "/^\.(png|jpg|jpeg){1}$/i";
                $maximum_filesize = 2 * 1024 * 1024;

                if ($imageFile) {
                    $filename       = $imageFile->getClientOriginalName();
                    $extension      = strrchr($filename, '.');
                    $size           = $imageFile->getSize();
                    $new_image_name = "bill" . "_" . time();

                    if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                        $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                    } else if (preg_match($rEFileTypes, $extension) == false) {
                        Session::flash('attachment-error', 'Warning : Invalid Image File!');
                    } else if ($size > $maximum_filesize) {
                        Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                    }
                }
                $logo = isset($attachment) ? $new_image_name . $extension : null;

                $objectBill                  = Bill::find($billId);
                $objectBill->bill_amount     = $request->get('bill_amount');
                $objectBill->bill_pan_number = $request->get('bill_pan_number');
                if ($logo) {
                    if (file_exists("uploads/bill/" . $objectBill->bill_attachment) && $objectBill->bill_attachment != '') {
                        unlink('uploads/bill/' . $objectBill->bill_attachment);
                    }
                    $objectBill->bill_attachment = $logo;
                }
                $objectBill->updated_by         = Auth::user()->id;
                $objectBill->bill_received_date = $request->get('bill_received_date');
                $objectBill->is_active          = $request->get('is_active');
                $objectBill->save();

                if ($objectBill->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);

                        $objectBillDetail = Billdetail::find($detailId);
                        if (count($objectBillDetail) > 0) {
                            $objectBillDetail->bill_particular   = trim($request->get('bill_particular_' . $language->id));
                            $objectBillDetail->bill_pcode_num    = trim($request->get('bill_pcode_num_' . $language->id));
                            $objectBillDetail->bill_expense_head = trim($request->get('bill_expense_head_' . $language->id));
                            $objectBillDetail->bill_buying_head  = trim($request->get('bill_buying_head_' . $language->id));
                            $objectBillDetail->bill_received_by  = trim($request->get('bill_received_by_' . $language->id));
                            $objectBillDetail->bill_remarks      = trim($request->get('bill_remarks_' . $language->id));
                            $objectBillDetail->save();
                            continue;
                        } else {
                            $insertArray[] = [
                                'bill_id'           => $objectBill->id,
                                'language_id'       => $language->id,
                                'bill_particular'   => $request->get('bill_particular_' . $language->id),
                                'bill_pcode_num'    => $request->get('bill_pcode_num_' . $language->id),
                                'bill_expense_head' => $request->get('bill_expense_head_' . $language->id),
                                'bill_buying_head'  => $request->get('bill_buying_head_' . $language->id),
                                'bill_received_by'  => $request->get('bill_received_by_' . $language->id),
                                'bill_remarks'      => $request->get('bill_remarks_' . $language->id),
                                'created_at'        => date("Y-m-d H:i:s"),
                                'updated_at'        => date("Y-m-d H:i:s"),
                            ];
                        }
                    }

                    if (count($insertArray) > 0) {
                        DB::table('bill_detail')->insert($insertArray);
                    }
                }

                $billData = Bill::find($billId);
                $bills    = Bill::getBillDetailsWithLanguages($billId);
                Session::flash('class', 'alert alert-success');
                Session::flash('message', 'Bill is successfully updated.');
                return view('bills.admin.edit')
                    ->with('bills', $bills)
                    ->with('billData', $billData)
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name)
                    ->with('languages', $languages)
                    ->with('addAccess', $addAccess);
            }
        } catch (Throwable $e) {
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view('bills.admin.edit')
                ->with('bills', $bills)
                ->with('$billData', $billData)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages)
                ->with('addAccess', $addAccess);
        }
    }

    /*
     * bill delete action admin panel
     * @return JSON
     */
    public function deleteBill(Request $request)
    {
        $billId                 = $request->get('id');
        $objectBill             = Bill::find($billId);
        $objectBill->is_active  = '0';
        $objectBill->is_deleted = '1';
        $objectBill->save();

        if ($objectBill->id) {
            $data['type']    = "success";
            $data['message'] = "Bill deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * bill staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $billId     = $request->get('id');
        $objectBill = Bill::find($billId);
        if ($objectBill->is_active == '0') {
            $objectBill->is_active = '1';
            $objectBill->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The bill has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectBill->is_active = '0';
            $objectBill->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The bill has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

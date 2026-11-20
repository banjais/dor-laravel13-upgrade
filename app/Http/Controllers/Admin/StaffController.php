<?php

/**
 * Project Name: Department of Road(DOR)
 * @author Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use App\Services\PhotoStudio\PhotoStudioService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Language;
use App\Http\Controllers\ConfigurationController;
use App\Models\Configuration;
use App\Models\Office;
use App\Models\Staff;
use App\Models\StaffDesignation;
use App\Models\StaffDetails;
use App\Models\StaffGroup;
use App\Models\StaffLevel;
use App\Models\StaffService;
use App\Models\StaffSubgroup;
use App\Models\UserAccessControlModel;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class StaffController extends ConfigurationController
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name    = ConfigurationController::site_name();
    }

    /**
     * This method is used to get the list of undeleted staffs(both active and deactive)
     *
     * @author $author- Sunil Adhikari
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $officeId = Auth::User()->office_id;
        $authUserId = Auth::User()->id;
        $staffs     = Staff::with(['designation', 'details' => function ($query) {
            $query->where('language_id', 2);
        }])
            ->where('office_id', $officeId)
            ->where('is_deleted', '0')
            ->orderBy('order_position', 'asc')
            ->get();

        $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(29, $authUserId);
        $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(29, $authUserId);
        $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(29, $authUserId);
        $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(29, $authUserId);

        return view('staff.staffs.staffs')
            ->with('staffs', $staffs)
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('addAccess', $addAccess)
            ->with('editAccess', $editAccess)
            ->with('publishAccess', $publishAccess)
            ->with('deleteAccess', $deleteAccess);
    }
    /**
     * This method is used to get the view part of the add staff
     *
     * @author $author- Sunil Adhikari
     * @return \Illuminate\View\View
     */
    public function getAddStaff()
    {
        $authUser = Auth::user();
        $officeId = $authUser->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(29, $authUser->id);
        if ($addAccess) {
            return view('staff.staffs.addstaff')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('officeId', $officeId)
                ->with('languages', Language::getActiveLanguages())
                ->with('designations', StaffDesignation::get())
                ->with('services', StaffService::get())
                ->with('levels', StaffLevel::get())
                ->with('groups', StaffGroup::get())
                ->with('subgroups', StaffSubgroup::get());
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * This method is used add  the staff
     *
     * @author $author- Sunil Adhikari
     * @return mixed
     */

    /**
     * Store staff photos as 800x800 images without cropping or stretching.
     * The complete original image is proportionally fitted on a light-gray canvas.
     */
    private function storeNormalizedStaffPhoto($imageFile, $destinationPath, $photoName)
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD image processing is not available.');
        }

        if (
            !is_dir($destinationPath) &&
            !mkdir($destinationPath, 0755, true) &&
            !is_dir($destinationPath)
        ) {
            throw new \RuntimeException('Unable to create the staff photo directory.');
        }

        $sourcePath = $imageFile->getPathname();
        $imageInfo = @getimagesize($sourcePath);

        if (!$imageInfo || empty($imageInfo[0]) || empty($imageInfo[1])) {
            throw new \RuntimeException('Uploaded staff photo is not a valid image.');
        }

        $sourceWidth = (int) $imageInfo[0];
        $sourceHeight = (int) $imageInfo[1];

        if ($sourceWidth > 12000 || $sourceHeight > 12000 ||
            ($sourceWidth * $sourceHeight) > 40000000) {
            throw new \RuntimeException('Uploaded staff photo dimensions are too large.');
        }

        $source = @imagecreatefromstring(file_get_contents($sourcePath));

        if (!$source) {
            throw new \RuntimeException('Unable to process the uploaded staff photo.');
        }

        // Apply common EXIF orientation values for JPEG photos.
        if (
            strtolower($imageInfo['mime'] ?? '') === 'image/jpeg' &&
            function_exists('exif_read_data')
        ) {
            $exif = @exif_read_data($sourcePath);
            $orientation = (int) ($exif['Orientation'] ?? 1);

            if (in_array($orientation, [2, 3, 4, 5, 6, 7, 8], true)) {
                if (in_array($orientation, [2, 4, 5, 7], true)) {
                    imageflip(
                        $source,
                        in_array($orientation, [2, 5], true)
                            ? IMG_FLIP_HORIZONTAL
                            : IMG_FLIP_VERTICAL
                    );
                }

                if (in_array($orientation, [3, 5, 6, 7, 8], true)) {
                    $angle = in_array($orientation, [5, 6, 7], true) ? -90 : 90;
                    $rotated = imagerotate($source, $angle, 0);

                    if ($rotated !== false) {
                        imagedestroy($source);
                        $source = $rotated;
                    }
                }
            }
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $canvas = imagecreatetruecolor(800, 800);

        if (!$canvas) {
            imagedestroy($source);
            throw new \RuntimeException('Unable to create the normalized staff photo.');
        }

        $background = imagecolorallocate($canvas, 250, 250, 250);
        imagefill($canvas, 0, 0, $background);

        $scale = min(800 / $sourceWidth, 800 / $sourceHeight);
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));
        $targetX = (int) floor((800 - $targetWidth) / 2);
        $targetY = (int) floor((800 - $targetHeight) / 2);

        imagecopyresampled(
            $canvas,
            $source,
            $targetX,
            $targetY,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        $finalPath = $destinationPath . DIRECTORY_SEPARATOR . basename($photoName);
        $temporaryPath = $finalPath . '.tmp-' . bin2hex(random_bytes(6));
        $extension = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));

        $saved = false;

        if (in_array($extension, ['jpg', 'jpeg'], true)) {
            $saved = imagejpeg($canvas, $temporaryPath, 90);
        } elseif ($extension === 'png') {
            $saved = imagepng($canvas, $temporaryPath, 6);
        }

        imagedestroy($source);
        imagedestroy($canvas);

        if (!$saved || !is_file($temporaryPath)) {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }

            throw new \RuntimeException('Unable to save the normalized staff photo.');
        }

        if (!rename($temporaryPath, $finalPath)) {
            unlink($temporaryPath);
            throw new \RuntimeException('Unable to finalize the normalized staff photo.');
        }

        return $finalPath;
    }

    public function addStaff(Request $request, PhotoStudioService $photoStudio)
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('admin.staffs');
        }

        $authUserId = $authUser->id;
        $officeId = $authUser->office_id;

        if (!UserAccessControlModel::getAccessAddByModuleAndUserMapId(29, $authUserId)) {
            return response()->json([
                'type' => 'error',
                'message' => 'You do not have permission to add staff.',
            ], 403);
        }

        $languages = Language::getActiveLanguages();
        $rules = [];

        foreach ($languages as $language) {
            $rules['full_name_' . $language->language_code] = 'required';
        }

        $rules['designation_id'] = 'required';

        if ($request->hasFile('attachment')) {
            $rules['attachment'] = 'image|mimes:jpg,jpeg,png|max:1024';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $photoStudioToken = trim((string) $request->input('photo_studio_token', ''));

        if ($request->hasFile('attachment') && $photoStudioToken === '') {
            $validator->errors()->add(
                'attachment',
                'Please process and accept the AI Photo Studio preview before saving.'
            );

            return Redirect::back()->withInput()->withErrors($validator);
        }

        $imageFile = null;
        $newPhotoPath = null;
        $photoName = null;

        try {
            if ($photoStudioToken !== '') {
                $imageFile = $photoStudio->getAcceptedUpload(
                    'staff',
                    $photoStudioToken,
                    $authUserId
                );
            }

            if ($imageFile) {
                $destinationPath = public_path('uploads/staffs');

                if (
                    !is_dir($destinationPath) &&
                    !mkdir($destinationPath, 0755, true) &&
                    !is_dir($destinationPath)
                ) {
                    throw new \RuntimeException('Unable to create the staff photo directory.');
                }

                $extension = strtolower($imageFile->getClientOriginalExtension());
                $photoName = 'staff_' . time() . '_' . $officeId . '_' .
                    $authUserId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

                $newPhotoPath = $this->storeNormalizedStaffPhoto(
                    $imageFile,
                    $destinationPath,
                    $photoName
                );
            }

            DB::transaction(function () use (
                $request,
                $languages,
                $officeId,
                $authUserId,
                $photoName
            ) {
                $staff = new Staff();
                $staff->office_id = $officeId;
                $staff->designation_id = $request->get('designation_id');
                $staff->level_id = $request->get('level_id');
                $staff->service_id = $request->get('service_id');
                $staff->group_id = $request->get('group_id');
                $staff->subgroup_id = $request->get('subgroup_id');
                $staff->email_address = $request->get('email');
                $staff->gender = $request->get('gender');
                $staff->office_join_date = $request->get('office_join_date');
                $staff->is_active = $request->get('is_active');
                $staff->is_deleted = '0';
                $staff->created_by = $authUserId;
                $staff->updated_by = $authUserId;
                $staff->order_position = 0;
                $staff->photo = $photoName;
                $staff->save();

                $insertArray = [];

                foreach ($languages as $language) {
                    $insertArray[] = [
                        'full_name' => $request->get('full_name_' . $language->language_code),
                        'permanent_address' => $request->get('permanent_address_' . $language->language_code),
                        'temporary_address' => $request->get('temporary_address_' . $language->language_code),
                        'contact_number' => $request->get('contact_number_' . $language->language_code),
                        'mobile_number' => $request->get('mobile_number_' . $language->language_code),
                        'remarks' => $request->get('remark_' . $language->language_code),
                        'language_id' => $language->id,
                        'staff_id' => $staff->id,
                    ];
                }

                if ($insertArray) {
                    StaffDetails::insert($insertArray);
                }
            });
        } catch (Throwable $e) {
            if ($newPhotoPath && is_file($newPhotoPath)) {
                unlink($newPhotoPath);
            }

            report($e);

            return Redirect::back()
                ->withInput()
                ->with('type', 'error')
                ->with('message', 'Staff could not be saved. Please try again.');
        }

        if ($photoStudioToken !== '') {
            try {
                $photoStudio->completeAcceptedPreview(
                    'staff',
                    $photoStudioToken,
                    $authUserId
                );
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }
        }

        return Redirect::route('admin.staffs')
            ->with('type', 'success')
            ->with('message', 'Staff has been created successfully.');
    }

    /**
     * This method is used add  the staff
     *
     * @author $author- Sunil Adhikari
     * @param  int $id
     * @return Illuminate\View\View

     */
    public function getEditStaff($id)
    {
        $authUser = Auth::user();
        $authUserId = $authUser->id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(29, $authUserId);
        $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(29, $authUserId);

        $staff = Staff::with('details.language')
            ->where('office_id', $authUser->office_id)
            ->where('is_deleted', '0')
            ->find($id);

        if (!$editAccess || !$staff) {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }

        return view('staff.staffs.editstaff')
            ->with('staff', $staff)
            ->with('languages', Language::getActiveLanguages())
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('addAccess', $addAccess)
            ->with('designations', StaffDesignation::get())
            ->with('services', StaffService::get())
            ->with('levels', StaffLevel::get())
            ->with('groups', StaffGroup::get())
            ->with('subgroups', StaffSubgroup::get());
    }
    /**
     * This method is used edit  the staff
     *
     * @author $author- Sunil Adhikari
     * @param  int $id
     * @return Redirect
     */
    public function editStaff($id, Request $request)
    {
        $authUser = Auth::user();
        $authUserId = $authUser->id;
        $officeId = $authUser->office_id;
        $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(29, $authUserId);

        // Use the route ID and restrict edits to the authenticated user's office.
        $staff = Staff::where('office_id', $officeId)
            ->where('is_deleted', '0')
            ->find($id);

        if (!$editAccess || !$staff) {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }

        $rule      = array();
        $languages = Language::getActiveLanguages();
        foreach ($languages as $language) {
            $rule['full_name_' . $language->language_code]   = 'required';
        }
        // $rule['email'] = 'required | email';
        $rule['designation_id'] = 'required';

        if ($request->hasFile('attachment')) {
            $rule['attachment'] = 'image|mimes:jpg,jpeg,png|max:1024';
        }

        $v = Validator::make($request->all(), $rule);
        if ($v->fails()) {
            return Redirect::back()->withInput()->withErrors($v);
        }

        $staff->designation_id  = $request->get('designation_id');
        $staff->level_id        = $request->get('level_id');
        $staff->service_id      = $request->get('service_id');
        $staff->group_id        = $request->get('group_id');
        $staff->subgroup_id     = $request->get('subgroup_id');
        $staff->email_address    = $request->get('email');
        $staff->is_active        = $request->get('is_active');
        $staff->gender           = $request->get('gender');
        $staff->office_join_date = $request->get('office_join_date');
        $staff->updated_by       = $authUserId;

        $imageFile       = $request->file('attachment');
        $destinationPath = public_path('uploads/staffs');
        $oldPhoto        = $staff->photo;
        $newPhotoPath    = null;

        if ($imageFile) {
            $extension = strtolower($imageFile->getClientOriginalExtension());
            $newPhotoName = 'staff_' . time() . '_' . $officeId . '_' . $authUserId . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

            $newPhotoPath = $this->storeNormalizedStaffPhoto(
                $imageFile,
                $destinationPath,
                $newPhotoName
            );
            $staff->photo = $newPhotoName;
        } elseif ($request->get('attachment_deleted') == '1') {
            $staff->photo = null;
        }

        // Save the database record before deleting the previous photo.
        $rec = $staff->save();

        if ($rec) {
            if ($oldPhoto && $oldPhoto !== $staff->photo) {
                // Keep the old file if another staff record still references it.
                $photoStillInUse = DB::table('staffs')
                    ->where('photo', $oldPhoto)
                    ->where('id', '!=', $staff->id)
                    ->exists();

                if (!$photoStillInUse) {
                    $oldPhotoPath = public_path('uploads/staffs/' . basename($oldPhoto));

                    if (is_file($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
            }
        } elseif ($newPhotoPath && is_file($newPhotoPath)) {
            // Remove only the new file created by this request if saving failed.
            unlink($newPhotoPath);
        }

        if (!empty($rec)) {

            foreach ($languages as $language) {
                $id          = $request->get('id_' . $language->language_code);
                $staffdetail = StaffDetails::where('staff_id', $staff->id)->find($id);
                //var_dump($staffdetail);exit;
                if ($staffdetail) {
                    $staffdetail->full_name         = $request->get('full_name_' . $language->language_code);
                    $staffdetail->permanent_address = $request->get('permanent_address_' . $language->language_code);
                    $staffdetail->temporary_address = $request->get('temporary_address_' . $language->language_code);
                    $staffdetail->mobile_number     = $request->get('mobile_number_' . $language->language_code);
                    $staffdetail->contact_number    = $request->get('contact_number_' . $language->language_code);
                    $staffdetail->remarks           = $request->get('remark_' . $language->language_code);
                    $staffdetail->save();

                    continue;
                }
                $detaildata = array(
                    'full_name' => $request->get('full_name_' . $language->language_code),
                    'permanent_address'             => $request->get('permanent_address_' . $language->language_code),
                    'temporary_address'             => $request->get('temporary_address_' . $language->language_code),
                    'mobile_number'                 => $request->get('mobile_number_' . $language->language_code),
                    'contact_number'                => $request->get('contact_number_' . $language->language_code),
                    'remarks'                       => $request->get('remark_' . $language->language_code),
                    'language_id'                   => $language->id,
                    'staff_id'                      => $staff->id
                );
                StaffDetails::create($detaildata);
            }
            return Redirect::route('admin.staffs')
                ->with('type', 'success')
                ->with('message', 'Staff has been Updated Successfully!!');
        }
        return Redirect::back()
            ->with('type', 'error')
            ->with('message', 'Could not be upadated due to some reason.');
    }

    /**
     * This method is used delete  the staff
     *
     * @author $author- Sunil Adhikari
     * @return json
     */
    public function deleteStaff(Request $request)
    {
        $user = Auth::user();

        if (!$user || !UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(29, $user->id)) {
            return response()->json([
                'type' => 'error',
                'message' => 'You do not have permission to delete staff.',
            ], 403);
        }

        $staff = Staff::where('office_id', $user->office_id)
            ->where('is_deleted', '0')
            ->find($request->input('id'));

        if (!$staff) {
            return response()->json([
                'type' => 'error',
                'message' => 'Staff record not found in your office.',
            ], 404);
        }

        $staff->is_deleted = '1';
        $staff->is_active = '0';
        $staff->updated_by = $user->id;
        $staff->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Deleted Successfully',
        ]);
    }

    /**
     * This method is used add  the staff
     * @author $author- Sunil Adhikari
     * @return json
     */
    public function changeStatus(Request $request)
    {
        $user = Auth::user();

        if (!$user || !UserAccessControlModel::getAccessPublishByModuleAndUserMapId(29, $user->id)) {
            return response()->json([
                'type' => 'error',
                'message' => 'You do not have permission to change staff status.',
            ], 403);
        }

        $staff = Staff::where('office_id', $user->office_id)
            ->where('is_deleted', '0')
            ->find($request->input('id'));

        if (!$staff) {
            return response()->json([
                'type' => 'error',
                'message' => 'Staff record not found in your office.',
            ], 404);
        }

        $staff->is_active = $staff->is_active == '0' ? '1' : '0';
        $staff->updated_by = $user->id;
        $staff->save();

        return response()->json([
            'type' => 'success',
            'message' => $staff->is_active == '1'
                ? 'The staff has been activated successfully'
                : 'The staff has been deactivated successfully',
            'icon' => $staff->is_active == '1'
                ? '<i class="icon-check"></i>'
                : '<i class="icon-check-empty"></i>',
        ]);
    }

    public function getsortStaff()
    {
        $authUser = Auth::User();
        $authUserId = $authUser->user_id;
        $office_id  = $authUser->office_id;
        $staffs     = Staff::with(['details' => function ($query) {
            $query->where('language_id', 2);
        }])
            ->where('office_id', $office_id)
            ->where('is_deleted', '0')
            ->orderBy('order_position', 'asc')
            ->get();

        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(29, $authUserId);

        return view('staff.staffs.sort_staffs')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('staffs', $staffs)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('addAccess', $addAccess)
            ->with('languages', Language::getActiveLanguages());
    }

    public function sortStaff(Request $request)
    {
        $returnArray = array('type' => 'error', 'message' => 'Server Error!');
        $staffs      = $request->get('staff');

        $position = 1;
        foreach ($staffs as $staffId) {
            $objectStaff                 = Staff::find($staffId);
            $objectStaff->order_position = $position;
            $objectStaff->save();
            $position++;
        }
        $returnArray = array('type' => 'success', 'staffs' => $staffs, 'message' => 'Staffs are sorted successfully.');
        return json_encode($returnArray);
    }

    public function getAddEmail()
    {
        $staff = Staff::find(Auth::Staff()->id);

        $staffemail = isset($staff->staff_email_address) ? $staff->staff_email_address : '';

        return view('staffs.staffs.emailrequest')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('staffemail', $staffemail)
            ->with('officelist', $this->getOfficeList())
            ->with('languages', Language::getActiveLanguages());
    }

    /**
     * This method is used add  the staff
     *

     * @author $author- Sunil Adhikari

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function addEmail(Request $request)
    {
        $change_email = $request->get('email_new');
        $staff        = Staff::find(Auth::Staff()->id);
        $details      = Staff::getStaffDetailsByIdForUpdate(Auth::Staff()->id);
        $office       = Office::getPublishedOfficesbyID($staff->office_id);

        $staffemail  = isset($staff->staff_email_address) ? $staff->staff_email_address : '';
        $staffoffice = isset($office->office_name) ? $office->office_name : '';
        $staffname   = isset($details[0]->staff_full_name) ? $details[0]->staff_full_name : '';
        if ($change_email) {

            $subject = "Email Need To Change.";

            $home = '<table cellspacing="0" cellpadding="0" width="100%">';
            $home .= '<tr><td><p>Dear <strong>admin,</strong> <br />';
            $home .= '<br />An Admin <strong>' . $staffname . '</strong> has send email to change new email.</td>';
            $home .= '</table>';
            $home .= '<table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;margin-top: 20px;">';
            $home .= '<tr><td width="150">Current Email :</td><td>' . $staffemail . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">New Email To change :</td><td>' . $change_email . '</td></tr>';
            $home .= '<tr style="margin-top: 10px;"><td width="150">Office:</td><td>' . $staffoffice . '</td></tr>';
            $home .= '</table>';
            $home .= '<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;margin-top: 20px;">';
            $home .= '<tr><td><p>Kind Regards,</strong> <br />';
            $home .= $this->site_name . '</td>';
            $home .= '</table>';

            $toEmail = Configuration::getValueFromName('developer_email');
            //$toEmail = $officeData->office_email_address;

            $email = Helper::sendEmail($toEmail, $subject, $home);

            $response['type']    = 'success';
            $response['message'] = "Staff has been created successfully.";
            exit(json_encode($response));
        }
    }
}

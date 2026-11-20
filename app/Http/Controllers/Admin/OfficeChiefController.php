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
use App\Models\OfficeChief;
use App\Models\OfficeChiefDetail;
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

class OfficeChiefController extends ConfigurationController
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

    public function getIndex()
    {
        $officeId = Auth::User()->office_id;
        $authUserId = Auth::User()->id;
        $staffs     = OfficeChief::with(['details' => function ($query) {
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

        return view('office_chief.admin.staffs')
            ->with('staffs', $staffs)
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('addAccess', $addAccess)
            ->with('editAccess', $editAccess)
            ->with('publishAccess', $publishAccess)
            ->with('deleteAccess', $deleteAccess);
    }

    public function getAddStaff()
    {
        $officeId = Auth::User()->office_id;
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(29, Auth::User()->id);
        if ($viewAccess) {
            return view('office_chief.admin.addstaff')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('officeId', $officeId)
                ->with('languages', Language::getActiveLanguages())
                ->with('designations', [
                    'Chief Engineer' => 'Chief Engineer',
                    'Director General' => 'Director General'
                ]);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }


    /**
     * Store office chief photos as 800x800 without cropping or stretching.
     * The complete original image is fitted proportionally on a #eeeeee canvas.
     */
    private function storeNormalizedOfficeChiefPhoto($imageFile, $destinationPath, $photoName)
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD image processing is not available.');
        }

        if (
            !is_dir($destinationPath) &&
            !mkdir($destinationPath, 0755, true) &&
            !is_dir($destinationPath)
        ) {
            throw new \RuntimeException('Unable to create the office chief photo directory.');
        }

        $sourcePath = $imageFile->getPathname();
        $imageInfo = @getimagesize($sourcePath);

        if (!$imageInfo || empty($imageInfo[0]) || empty($imageInfo[1])) {
            throw new \RuntimeException('Uploaded office chief photo is not a valid image.');
        }

        $sourceWidth = (int) $imageInfo[0];
        $sourceHeight = (int) $imageInfo[1];

        if (
            $sourceWidth > 12000 ||
            $sourceHeight > 12000 ||
            ($sourceWidth * $sourceHeight) > 40000000
        ) {
            throw new \RuntimeException('Uploaded photo dimensions are too large.');
        }

        $source = @imagecreatefromstring(file_get_contents($sourcePath));

        if (!$source) {
            throw new \RuntimeException('Unable to process the uploaded office chief photo.');
        }

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
            throw new \RuntimeException('Unable to create the normalized office chief photo.');
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

            throw new \RuntimeException('Unable to save the normalized office chief photo.');
        }

        if (!rename($temporaryPath, $finalPath)) {
            unlink($temporaryPath);
            throw new \RuntimeException('Unable to finalize the normalized office chief photo.');
        }

        return $finalPath;
    }

    public function addStaff(Request $request, PhotoStudioService $photoStudio)
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return redirect()->route('admin.office_chiefs');
        }

        $authUserId = $authUser->id;
        $officeId = $authUser->office_id;

        if (!UserAccessControlModel::getAccessAddByModuleAndUserMapId(29, $authUserId)) {
            return response()->json([
                'type' => 'error',
                'message' => 'You do not have permission to add office chiefs.',
            ], 403);
        }

        $languages = Language::getActiveLanguages();

        $rules = [];
        foreach ($languages as $language) {
            $rules['full_name_' . $language->language_code] = 'required';
        }

        $rules['chief_type'] = 'required';
        $rules['date_ad'] = 'required';
        $rules['date_bs'] = 'required';

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
        $destinationPath = public_path('uploads/office_chiefs');
        $photoName = null;
        $newPhotoPath = null;

        try {
            if ($photoStudioToken !== '') {
                $imageFile = $photoStudio->getAcceptedUpload(
                    'office_chief',
                    $photoStudioToken,
                    $authUserId
                );
            }

            if ($imageFile) {
                $extension = strtolower($imageFile->getClientOriginalExtension());
                $photoName = 'chief_' . time() . '_' . $officeId . '_' .
                    $authUserId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

                $newPhotoPath = $this->storeNormalizedOfficeChiefPhoto(
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
                $staff = new OfficeChief();
                $staff->office_id = $officeId;
                $staff->chief_type = $request->get('chief_type');
                $staff->date_ad = $request->get('date_ad');
                $staff->date_bs = $request->get('date_bs');
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
                        'language_id' => $language->id,
                        'chief_id' => $staff->id,
                    ];
                }

                if (!empty($insertArray)) {
                    OfficeChiefDetail::insert($insertArray);
                }
            });

            if ($photoStudioToken !== '') {
                try {
                    $photoStudio->completeAcceptedPreview(
                        'office_chief',
                        $photoStudioToken,
                        $authUserId
                    );
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            return Redirect::route('admin.office_chiefs')
                ->with('type', 'success')
                ->with('message', 'Office chief has been created successfully.');
        } catch (Throwable $exception) {
            if ($newPhotoPath && is_file($newPhotoPath)) {
                @unlink($newPhotoPath);
            }

            report($exception);

            return Redirect::back()
                ->withInput()
                ->with('type', 'error')
                ->with('message', 'Could not create office chief. Please check the entered details and try again.');
        }
    }

    public function getEditStaff($id)
    {
        $authUser = Auth::user();

        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(
            29,
            $authUser->id
        );

        abort_unless($addAccess, 403);

        $staff = OfficeChief::with('details.language')->findOrFail($id);

        if (
            (int) $authUser->user_type_id === 12 &&
            (int) $staff->office_id !== (int) $authUser->office_id
        ) {
            abort(403);
        }

        return view('office_chief.admin.editstaff')
            ->with('staff', $staff)
            ->with('languages', Language::getActiveLanguages())
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('addAccess', $addAccess)
            ->with('designations', [
                'Chief Engineer' => 'Chief Engineer',
                'Director General' => 'Director General'
            ]);
    }

    public function editStaff($id, Request $request, PhotoStudioService $photoStudio)
    {
        $authUser = Auth::user();
        $authUserId = $authUser->user_id;

        if (
            $request->filled('id') &&
            (int) $request->input('id') !== (int) $id
        ) {
            abort(400, 'Invalid Office Chief record reference.');
        }

        $staff = OfficeChief::findOrFail($id);

        if (
            (int) $authUser->user_type_id === 12 &&
            (int) $staff->office_id !== (int) $authUser->office_id
        ) {
            abort(403);
        }

        $languages = Language::getActiveLanguages();

        $rules = [];
        foreach ($languages as $language) {
            $rules['full_name_' . $language->language_code] = 'required';
        }

        $rules['chief_type'] = 'required';
        $rules['date_ad'] = 'required';
        $rules['date_bs'] = 'required';

        if ($request->hasFile('attachment')) {
            $rules['attachment'] = 'image|mimes:jpg,jpeg,png|max:1024';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $imageFile = $request->file('attachment');
        $photoStudioToken = $request->input('photo_studio_token');
        $destinationPath = public_path('uploads/office_chiefs');
        $oldPhoto = $staff->photo;
        $newPhotoPath = null;
        $newPhotoName = null;

        try {
            if ($photoStudioToken) {
                if ($imageFile) {
                    throw new \RuntimeException(
                        'Please use the accepted Photo Studio preview or upload a new photo, not both.'
                    );
                }

                $imageFile = $photoStudio->getAcceptedUpload(
                    'office_chief',
                    (string) $photoStudioToken,
                    $authUser->id
                );
            }

            if ($imageFile) {
                $extension = strtolower($imageFile->getClientOriginalExtension());

                if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                    throw new \RuntimeException('Only JPEG and PNG photos are supported.');
                }

                $newPhotoName = 'chief_' . time() . '_' . $staff->office_id . '_' .
                    $authUser->id . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

                $newPhotoPath = $this->storeNormalizedOfficeChiefPhoto(
                    $imageFile,
                    $destinationPath,
                    $newPhotoName
                );
            }

            DB::transaction(function () use (
                $request,
                $languages,
                $staff,
                $authUserId,
                $newPhotoName
            ) {
                $staff->chief_type = $request->get('chief_type');
                $staff->date_ad = $request->get('date_ad');
                $staff->date_bs = $request->get('date_bs');

                // Preserve the record's existing office, including for Global Admin.
                $staff->updated_by = $authUserId;

                if ($newPhotoName) {
                    $staff->photo = $newPhotoName;
                }

                $staff->save();

                foreach ($languages as $language) {
                    $detailId = $request->get('id_' . $language->language_code);
                    $staffDetail = $detailId
                        ? OfficeChiefDetail::where('id', $detailId)
                            ->where('chief_id', $staff->id)
                            ->first()
                        : null;

                    if ($staffDetail) {
                        $staffDetail->full_name = $request->get(
                            'full_name_' . $language->language_code
                        );
                        $staffDetail->save();
                    } else {
                        OfficeChiefDetail::create([
                            'full_name' => $request->get(
                                'full_name_' . $language->language_code
                            ),
                            'language_id' => $language->id,
                            'chief_id' => $staff->id,
                        ]);
                    }
                }
            });

            if ($photoStudioToken) {
                try {
                    $photoStudio->completeAcceptedPreview(
                        'office_chief',
                        (string) $photoStudioToken,
                        $authUser->id
                    );
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            // Keep the previous photo file for the future one-time enhancement process.

            return Redirect::route('admin.office_chiefs')
                ->with('type', 'success')
                ->with('message', 'Chief has been Updated Successfully!!');
        } catch (Throwable $exception) {
            if ($newPhotoPath && is_file($newPhotoPath)) {
                @unlink($newPhotoPath);
            }

            report($exception);

            return Redirect::back()
                ->withInput()
                ->with('type', 'error')
                ->with('message', 'Could not update office chief. Please try again.');
        }
    }

    public function deleteStaff(Request $request)
    {
        $id                = $request->get('id');
        $staff             = OfficeChief::find($id);
        $staff->is_deleted = '1';
        $staff->is_active  = '0';
        $staff->save();
        $data['type']    = "success";
        $data['message'] = "Deleted Successfully";
        echo json_encode($data);
    }

    /**
     * This method is used add  the staff
     * @author $author- Sunil Adhikari
     * @return json
     */
    public function changeStatus(Request $request)
    {
        $id    = $request->get('id');
        $staff = OfficeChief::find($id);
        if ($staff->is_active == '0') {
            $staff->is_active = '1';
            $staff->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'Record activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
            exit(json_encode($feedback));
        }
        $staff->is_active = '0';
        $staff->save();
        $feedback['type']    = 'success';
        $feedback['message'] = 'Record deactivated successfully';
        $feedback['icon']    = '<i class="icon-check-empty"></i>';
        exit(json_encode($feedback));
    }

    public function getsortStaff()
    {
        $authUser = Auth::User();
        $authUserId = $authUser->user_id;
        $office_id  = $authUser->office_id;
        $staffs     = OfficeChief::with(['details' => function ($query) {
            $query->where('language_id', 2);
        }])
            ->where('office_id', $office_id)
            ->where('is_deleted', '0')
            ->orderBy('order_position', 'asc')
            ->get();

        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(29, $authUserId);

        return view('office_chief.admin.sort_staffs')
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
            $objectStaff                 = OfficeChief::find($staffId);
            $objectStaff->order_position = $position;
            $objectStaff->save();
            $position++;
        }
        $returnArray = array('type' => 'success', 'staffs' => $staffs, 'message' => 'Records are sorted successfully.');
        return json_encode($returnArray);
    }
}

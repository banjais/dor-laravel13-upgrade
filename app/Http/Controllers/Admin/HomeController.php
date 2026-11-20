<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ConfigurationController;
use App\Models\Home;
use App\Models\Homedetail;
use App\Models\Language;
use App\Models\Ticker;
use App\Models\Tickerdetail;
use App\Models\UserAccessControlModel;
use App\Services\PhotoStudio\PhotoStudioService;
use App\Services\AutomaticImages\FlashBoxFileValidator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Throwable;

use Illuminate\Support\Facades\Validator;

class HomeController extends ConfigurationController
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

    /*
     * get home list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(1, Auth::User()->id);
        // dd($viewAccess);

        if ($viewAccess) {
            $officeId = Auth::user()->office_id;
            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);

            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(1, Auth::User()->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(1, Auth::User()->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(1, Auth::User()->id);
            $spokeId       = Home::getHomePageAdmin('dor-spoke-person', $officeId);
            $workingId     = Home::getHomePageAdmin('working-area', $officeId);
            $generalId     = Home::getHomePageAdmin('general-info', $officeId);
            $aboutId       = Home::getHomePageAdmin('about-us', $officeId);
            $grievanceId   = Home::getHomePageAdmin('grievance-hearing-officer', $officeId);
            $additionalOfficerId = Home::getHomePageAdmin('additional-officer', $officeId);

            // dd($editAccess);
            // dd($officeId);
            $t = Home::where('office_id', $officeId)->get();
            // dd($t);

            $homes    = Home::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])->where('office_id', $officeId)
                ->where('is_deleted', '0')
                ->whereRaw("home_url NOT REGEXP '^general-information(-[0-9]+)?$'")
                ->orderBy('home_identity_name')
                ->get();

            // dd($homes);

            return view('home.admin.list')
                ->with('homes', $homes)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addAccess', $addAccess)
                ->with('editAccess', $editAccess)
                ->with('publishAccess', $publishAccess)
                ->with('deleteAccess', $deleteAccess)
                ->with('spokeId', $spokeId)
                ->with('workingId', $workingId)
                ->with('generalId', $generalId)
                ->with('aboutId', $aboutId)
                ->with('grievanceId', $grievanceId)
                ->with('additionalOfficerId', $additionalOfficerId);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get home add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddHome()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
        //var_dump( $addAccess);exit;
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('home.admin.add')
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

    /**
     * Open the Add Flash Box form with the next available URL.
     */
    public function getAddLiveHome()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);

        if (!$addAccess) {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }

        $officeId = Auth::user()->office_id;

        $existingSlugs = Home::where('office_id', $officeId)
            ->where('home_url', 'REGEXP', '^general-information-[0-9]+$')
            ->pluck('home_url');

        $highestNumber = (int) $existingSlugs->map(function ($slug) {
            return (int) substr($slug, strlen('general-information-'));
        })->max();

        $nextNumber = max(2, $highestNumber + 1);

        $the_home = new Home();
        $the_home->data_1_title = 'Browse Cover Photo';
        $the_home->data_2_title = 'Choose Icon Color';
        $the_home->data_3_title = 'Update Name in Nepali';
        $the_home->data_4_title = 'Update Name in English';
        $the_home->data_5_title = 'Browse Pdf File';
        $the_home->data_6_title = 'Browse Video File';
        $the_home->data_7_title = 'Other Link';

        return view('home.admin.add')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('languages', Language::getActiveLanguages())
            ->with('addurl', 'general-information-' . $nextNumber)
            ->with('isFlashBoxAdd', true)
            ->with('the_home', $the_home);
    }

    /*
     * post home add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    /**
     * Homepage portrait entries that use the shared Photo Studio.
     */
    private function isPhotoOfficerUrl(string $url): bool
    {
        return in_array($url, [
            'message-from-director-general',
            'dor-spoke-person',
            'general-info',
            'information-officer',
            'grievance-hearing-officer',
            'additional-officer',
        ], true);
    }

    public function addHome(Request $request, PhotoStudioService $photoStudio, FlashBoxFileValidator $flashBoxValidator)
    {
        // dd($request->all());
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $officeId = Auth::user()->office_id;

        // Re-check Add permission on the server, not only in the admin menu.
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::id());
        if (!$addAccess) {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }

        $isFlashBox = $request->routeIs('admin.home.store.live');

        // echo '<pre>';
        // print_r($request->all()); exit;
        try {
            $rules = ['home_url' => 'required'];
            foreach ($languages as $language) {
                $rules['home_heading_' . $language->id]     = 'required';
                $rules['home_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);
            // dd($validator->fails());

            if ($validator->fails()) {
                // dd(0);
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                $homeUrl = $request->get('home_url');

                // Flash Box URLs are always generated server-side for this office.
                if ($isFlashBox) {
                    $existingSlugs = Home::where('office_id', $officeId)
                        ->where('home_url', 'REGEXP', '^general-information-[0-9]+$')
                        ->pluck('home_url');

                    $highestNumber = (int) $existingSlugs->map(function ($slug) {
                        return (int) substr($slug, strlen('general-information-'));
                    })->max();

                    $homeUrl = 'general-information-' . max(2, $highestNumber + 1);
                }

                if ($isFlashBox) {
                    $flashBoxValidator->validateCover($request->input('data_1'));
                    $flashBoxValidator->validatePdf($request->input('data_5_txt'));
                    $flashBoxValidator->validateVideo($request->input('data_6_txt'));
                }

                $photoToken = null;
                $isPhotoOfficer = $this->isPhotoOfficerUrl((string) $homeUrl);

                if ($isPhotoOfficer) {
                    $photoToken = $request->input('photo_studio_token');

                    if ($request->hasFile('attachment') && !$photoToken) {
                        return Redirect::back()->withInput()
                            ->with('type', 'error')
                            ->with('message', 'Please process and accept the portrait in AI Photo Studio.');
                    }
                }
                // print_r($homeUrl);exit;
                $homeExist = Home::checkExist($homeUrl, $officeId);
                // dd($homeExist);
                if ($homeExist) {
                    // dd(0);
                    $message = 'Home with url <b>' . $homeUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $imageFile = $isFlashBox ? null : ($photoToken
                        ? $photoStudio->getAcceptedUpload('home_officer', $photoToken, (int) Auth::id())
                        : $request->file('attachment'));
                    $destinationPath  = 'uploads/pages/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|gif|png){1}$/i";
                    $maximum_filesize = 5 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "pages" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                        }
                    }
                    $logo = isset($attachment) ? $new_image_name . $extension : null;

                    $objectHome                        = new Home();
                    $objectHome->home_url              = $homeUrl;
                    $objectHome->home_meta_tags        = $request->get('home_meta_tags');
                    $objectHome->home_meta_description = $request->get('home_meta_description');
                    $objectHome->is_active             = 1;
                    if ($logo) {
                        $objectHome->home_attachment = $logo;
                    }
                    $objectHome->created_by = Auth::user()->id;
                    $objectHome->updated_by = Auth::user()->id;
                    $objectHome->office_id  = $officeId;

                    // Flash Boxes belong to the homepage LIVE UPDATE board.
                    if ($isFlashBox) {
                        $objectHome->home_identity_name = 'LIVE UPDATE';
                        $objectHome->show_in = 4;

                        $objectHome->data_1_title = 'Browse Cover Photo';
                        $objectHome->data_2_title = 'Choose Icon Color';
                        $objectHome->data_3_title = 'Update Name in Nepali';
                        $objectHome->data_4_title = 'Update Name in English';
                        $objectHome->data_5_title = 'Browse Pdf File';
                        $objectHome->data_6_title = 'Browse Video File';
                        $objectHome->data_7_title = 'Other Link';

                        $objectHome->data_1 = trim((string) $request->input('data_1'));
                        $objectHome->data_2 = trim((string) $request->input('data_2'));
                        $objectHome->data_3 = trim((string) $request->input('data_3'));
                        $objectHome->data_4 = trim((string) $request->input('data_4'));
                        $objectHome->data_5 = trim((string) $request->input('data_5_txt'));
                        $objectHome->data_6 = trim((string) $request->input('data_6_txt'));
                        $objectHome->data_7 = trim((string) $request->input('data_7'));
                    } else {
                        // Homepage grievance officer is displayed in the contact-officers panel.
                        $objectHome->show_in = in_array($homeUrl, ['grievance-hearing-officer', 'additional-officer'], true) ? 4 : 1;
                    }

                    if (in_array($homeUrl, ['grievance-hearing-officer', 'additional-officer'], true)) {
                        $objectHome->data_1_title = 'Name in Nepali';
                        $objectHome->data_2_title = 'Name in English';
                        $objectHome->data_3_title = 'Phone Number';
                        $objectHome->data_4_title = 'Email Address';
                    }

                    if ($homeUrl === 'additional-officer') {
                        $objectHome->data_1 = trim($request->get('data_1'));
                        $objectHome->data_2 = trim($request->get('data_2'));
                        $objectHome->data_3 = trim($request->get('data_3'));
                        $objectHome->data_4 = trim($request->get('data_4'));
                    }

                    $objectHome->save();

                    // dd($objectHome->id);

                    if ($objectHome->id) {
                        // dd($languages);
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'home_id'                => $objectHome->id,
                                'language_id'            => $language->id,
                                'home_heading'           => $request->get('home_heading_' . $language->id),
                                'officer_name'            => trim($request->get('officer_name_' . $language->id)),
                                'home_description'       => $request->get('home_description_' . $language->id),
                                'home_page_title'        => $request->get('home_page_title_' . $language->id),
                                'created_at'             => date("Y-m-d H:i:s"),
                                'updated_at'             => date("Y-m-d H:i:s"),
                            ];
                        }
                        DB::table('home_detail')->insert($insertArray);

                        if ($photoToken) {
                            $photoStudio->completeAcceptedPreview(
                                'home_officer',
                                $photoToken,
                                (int) Auth::id()
                            );
                        }

                        return Redirect::route('admin.homes')
                            ->with('type', 'success')
                            ->with('message', 'Home content with heading "' . $request->get('home_heading_' . $language->id) . '" is added successfully.');
                    }
                }
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            dd($e->getMessage());
            Session::flash('class', 'alert alert-error');
            Session::flash('message', 'Server Error!');
            return view('home.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('languages', $languages);
        }
    } /*
     * get home add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddHomespoke()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('home.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addurl', 'dor-spoke-person')
                ->with('languages', $languages);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get home add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddHomework()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('home.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addurl', 'working-area')
                ->with('languages', $languages);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get home add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddHomegeneralinfo()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('home.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addurl', 'general-info')
                ->with('languages', $languages);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /*
     * get home add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddHomeaboutus()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('home.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addurl', 'about-us')
                ->with('languages', $languages);
        } else {
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * get home edit page admin panel
     * @param int $id
     * @return Illuminate\View\View
     */
    public function getAddHomeGrievanceOfficer()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('home.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addurl', 'grievance-hearing-officer')
                ->with('languages', $languages);
        }

        return view('error')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }

    public function getAddHomeAdditionalOfficer()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
        if ($addAccess) {
            $languages = Language::getActiveLanguages();
            return view('home.admin.add')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name)
                ->with('addurl', 'additional-officer')
                ->with('languages', $languages);
        }

        return view('error')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }

    public function getEditHome($id)
    {
        // dd($id);
        try {
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(1, Auth::User()->id);
            $objectHome = Home::find($id);
            //echo Configuration::get_last_query(); exit();

            if ($objectHome && $editAccess) {
                if ($objectHome->office_id == $officeId) {
                    $languages = Language::getActiveLanguages();
                    $homes     = Home::getHomeDetailsWithLanguages($id);
                    return view('home.admin.edit')
                        ->with('homes', $homes)
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name)
                        ->with('languages', $languages)
                        ->with('addAccess', $addAccess)
                        ->with('the_home', $objectHome);
                } else {
                    return view('error')
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name);
                }
            } else {
                return view('error')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name);
            }
        } catch (Throwable $e) {
            return view('Redirectror')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }

    /**
     * post home edit page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function editHome(Request $request, PhotoStudioService $photoStudio, FlashBoxFileValidator $flashBoxValidator)
    {
        // dd($request->all());
        $homeId = $request->get('id');
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        // dd($languages);
        $homes     = Home::getHomeDetailsWithLanguages($homeId);
        $objectHome = Home::findOrFail($homeId);
        $isFlashBox = preg_match('/^general-information(?:-[0-9]+)?$/', (string) $objectHome->home_url) === 1;
        $officeId  = Auth::user()->office_id;
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, Auth::User()->id);

        try {
            $rules = [];
            foreach ($languages as $language) {
                $rules['home_heading_' . $language->id]     = 'required';
                $rules['home_description_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {
                if ($isFlashBox) {
                    $newCover = trim((string) $request->input('data_1', ''));
                    $newPdf = trim((string) $request->input('data_5_txt', ''));
                    $newVideo = trim((string) $request->input('data_6_txt', ''));
                    if ($newCover !== trim((string) $objectHome->data_1)) {
                        $flashBoxValidator->validateCover($newCover ?: null);
                    }
                    if ($newPdf !== trim((string) $objectHome->data_5)) {
                        $flashBoxValidator->validatePdf($newPdf ?: null);
                    }
                    if ($newVideo !== trim((string) $objectHome->data_6)) {
                        $flashBoxValidator->validateVideo($newVideo ?: null);
                    }
                }

                $homeUrl   = trim(preg_replace('/[^A-Za-z0-9\-]/', '-', strtolower(trim($request->get('home_url')))), '-');
                $photoToken = null;
                $isPhotoOfficer = $this->isPhotoOfficerUrl((string) $objectHome->home_url);

                if ($isPhotoOfficer) {
                    $photoToken = $request->input('photo_studio_token');

                    if ($request->hasFile('attachment') && !$photoToken) {
                        return Redirect::back()->withInput()
                            ->with('type', 'error')
                            ->with('message', 'Please process and accept the portrait in AI Photo Studio.');
                    }
                }
                $homeExist = Home::checkExist($homeUrl, $officeId);
                // dd($homeId);

                if ($homeExist && $homeExist != $homeId) {
                    $message = 'Home with url <b>' . $homeUrl . '</b> is already exist';
                    return Redirect::back()->withInput()
                        ->with('type', 'error')
                        ->with('message', $message);
                } else {
                    $logo = null;
                    if (!$isFlashBox) {
                    $imageFile = $photoToken
                        ? $photoStudio->getAcceptedUpload('home_officer', $photoToken, (int) Auth::id())
                        : $request->file('attachment');
                    $destinationPath  = 'uploads/pages/';
                    $rEFileTypes      = "/^\.(jpg|jpeg|gif|png){1}$/i";
                    $maximum_filesize = 5 * 1024 * 1024;

                    if ($imageFile) {
                        $filename       = $imageFile->getClientOriginalName();
                        $extension      = strrchr($filename, '.');
                        $size           = $imageFile->getSize();
                        $new_image_name = "pages" . "_" . time();

                        if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                            $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                        } else if (preg_match($rEFileTypes, $extension) == false) {
                            Session::flash('attachment-error', 'Warning : Invalid Image File!');
                        } else if ($size > $maximum_filesize) {
                            Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                        }
                    } else {
                        $attachment_deleted = $request->get('attachment_deleted');
                    }

                    $logo = isset($attachment) ? $new_image_name . $extension : null;
                    }

                    // The Home model was loaded before validation.
                    //                    $objectHome->show_in               = $request->get('show_in');
                    $objectHome->home_meta_tags        = $request->get('home_meta_tags');
                    $objectHome->home_meta_description = $request->get('home_meta_description');
                    $objectHome->is_active             = 1;

                    // dd($request->all());

                    $objectHome->data_1 = trim($request->get('data_1'));
                    $objectHome->data_2 = trim($request->get('data_2'));
                    if (!$isFlashBox) {
                        $objectHome->data_3 = trim($request->get('data_3'));
                        $objectHome->data_4 = trim($request->get('data_4'));
                    }
                    $objectHome->data_5 = trim($request->get('data_5_txt'));
                    $objectHome->data_6 = trim($request->get('data_6_txt'));
                    $objectHome->data_7 = trim($request->get('data_7'));

                    if ($logo) {
                        $objectHome->home_attachment = $logo;
                    } else if (isset($attachment_deleted) && $attachment_deleted == '1') {
                        $objectHome->home_attachment = null;
                    }

                    $objectHome->updated_by = Auth::user()->id;
                    $objectHome->save();

                    $language_id = '';
                    // dd($objectHome->id);
                    if ($objectHome->id) {
                        // dd($languages);
                        $insertArray = array();
                        foreach ($languages as $language) {
                            $detailId         = $request->get('detail_id_' . $language->id);
                            $objectHomeDetail = Homedetail::find($detailId);
                            // dd(count($objectHomeDetail));
                            if ($objectHomeDetail) {
                                $objectHomeDetail->home_heading           = trim($request->get('home_heading_' . $language->id));
                                $objectHomeDetail->officer_name            = trim($request->get('officer_name_' . $language->id));
                                $objectHomeDetail->home_description       = trim($request->get('home_description_' . $language->id));
                                $objectHomeDetail->home_page_title        = trim($request->get('home_page_title_' . $language->id));


                                $objectHomeDetail->save();
                                continue;
                            } else {
                                $insertArray[] = [
                                    'home_id'                => $objectHome->id,
                                    'language_id'            => $language->id,
                                    'home_heading'           => trim($request->get('home_heading_' . $language->id)),
                                    'officer_name'            => trim($request->get('officer_name_' . $language->id)),
                                    'home_description'       => trim($request->get('home_description_' . $language->id)),
                                    'home_page_title'        => trim($request->get('home_page_title_' . $language->id)),
                                    'created_at'             => date("Y-m-d H:i:s"),
                                    'updated_at'             => date("Y-m-d H:i:s"),
                                ];
                            }
                        }
                        if (count($insertArray) > 0) {
                            DB::table('home_detail')->insert($insertArray);
                        }
                    }
                    // return Redirect::route('admin.homes')
                    //     ->with('type', 'success')
                    //     ->with('message', 'Home content with heading "' . $request->get('home_heading_' . $language->id) . '" is updated successfully.');

                    if ($photoToken) {
                        $photoStudio->completeAcceptedPreview(
                            'home_officer',
                            $photoToken,
                            (int) Auth::id()
                        );
                    }

                    return Redirect::route('admin.homes')
                        ->with('type', 'success')
                        ->with('message', 'Home content updated successfully.');
                }
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Content is not updated.');
        }
    }

    /*
     * home delete action admin panel
     * @return JSON
     */
    public function deleteHome(Request $request)
    {
        $homeId                 = $request->get('id');
        $objectHome             = Home::find($homeId);
        $objectHome->is_active  = '0';
        $objectHome->is_deleted = '1';
        $objectHome->save();

        if ($objectHome->id) {
            $data['type']    = "success";
            $data['message'] = "Home deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }

    /*
     * home staus change action admin panel
     * @return JSON
     */
    public function changeStatus(Request $request)
    {
        $homeId     = $request->get('id');
        $objectHome = Home::find($homeId);
        if ($objectHome->is_active == '0') {
            $objectHome->is_active = '1';
            $objectHome->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The home has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectHome->is_active = '0';
            $objectHome->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The home has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }

    //new ticker code  starts here


    public function getAddNewsTicker()
    {
        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(31, Auth::User()->id);
        // var_dump($addAccess);exit;
        if ($addAccess) {
            $languages = Language::getActiveLanguages();

            return view('home.admin.add_ticker')
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

    public function getNewsTicker()
    {
        /*$newss = Ticker::with(['details' => function($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', '4')
                ->where('is_delete', 0)
                ->orderBy('created_at', 'desc')
                ->get();
				var_dump( $newss);exit;*/

        //var_dump(Ticker::getHomePageBannersTest(4,1));exit;

        $authUser = Auth::user();
        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(31, $authUser->id);


        if ($viewAccess) {
            $newss = Home::getAllTicker();
            /* $newss = Ticker::with(['details' => function($query) {
                $query->where('language_id', 2);
            }])
                ->where('office_id', $authUser->office_id)
                ->where('is_deleted', 0)
                ->orderBy('created_at', 'desc')
                ->get();
				var_dump( $newss);exit;*/

            $addAccess     = UserAccessControlModel::getAccessAddByModuleAndUserMapId(31, $authUser->id);
            $editAccess    = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(31, $authUser->id);
            $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(31, $authUser->id);
            $deleteAccess  = UserAccessControlModel::getAccessDeleteByModuleAndUserMapId(31, $authUser->id);

            return view('home.admin.ticker_list')
                ->with('newss', $newss)
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

    public function getEditNewsTicker($id)
    {
        try {
            // dd(Auth::User()->user_type_id);
            $officeId   = Auth::user()->office_id;
            $addAccess  = UserAccessControlModel::getAccessAddByModuleAndUserMapId(31, Auth::User()->id);
            $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(31, Auth::User()->id);
            $objectHome = Home::findTickerById($id);

            // dd($objectHome);

            if ($objectHome && $editAccess) {
                // dd($objectHome[0]->office_id);
                if ($objectHome[0]->office_id == $officeId) {
                    $languages = Language::getActiveLanguages();
                    $homes     = Home::getTickerDetailsWithLanguages($id);
                    //var_dump($homes);exit;
                    return view('home.admin.ticker_edit')
                        ->with('newss', $homes)
                        ->with('newsData', $objectHome[0])
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name)
                        ->with('languages', $languages)
                        ->with('addAccess', $addAccess);
                } else {
                    // dd(0);
                    return view('error')
                        ->with('abs_site_url', $this->abs_site_url)
                        ->with('rel_site_url', $this->rel_site_url)
                        ->with('site_name', $this->site_name);
                }
            } else {
                return view('error')
                    ->with('abs_site_url', $this->abs_site_url)
                    ->with('rel_site_url', $this->rel_site_url)
                    ->with('site_name', $this->site_name);
            }
        } catch (Throwable $e) {
            var_dump($e);
            exit;
            return view('error')
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        }
    }
    public function editNewsTicker($id, Request $request)
    {

        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');
        $languages = Language::getActiveLanguages();
        // $newss     = Home::getTickerDetailsWithLanguages($id);
        // $officeId  = Auth::user()->office_id;
        // $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(31, Auth::User()->id);
        // $newsData  = Home::findTickerById($id);

        try {

            foreach ($languages as $language) {
                $rules['ticker_heading_' . $language->id]     = 'required';
                $rules['ticker_description_' . $language->id] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $objectNews                        = Ticker::find($id);
                $objectNews->is_active             = $request->get('is_active');

                $objectNews->updated_by = Auth::user()->id;
                $objectNews->save();

                // dd($objectNews);

                if ($objectNews->id) {
                    $insertArray = array();
                    foreach ($languages as $language) {
                        $detailId = $request->get('detail_id_' . $language->id);
                        $objectNewsDetail = Tickerdetail::find($detailId);
                        if ($objectNewsDetail) {
                            $objectNewsDetail->ticker_title           = trim($request->get('ticker_heading_' . $language->id));
                            $objectNewsDetail->ticker_des      = trim($request->get('ticker_description_' . $language->id));
                            $objectNewsDetail->updated_at        = date("Y-m-d H:i:s");



                            $objectNewsDetail->save();
                        }
                    }
                }
                return Redirect::to('admin/news-ticker')
                    ->with('type', 'success')
                    ->with('message', 'News is successfully updated.');
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'News is not updated.');
        }
    }

    public function addNewsTicker(Request $request)
    {
        $authUser = Auth::user();
        $languages = Language::getActiveLanguages();
        Session::forget('class');
        Session::forget('message');
        Session::forget('attachment-error');

        try {
            //var_dump($request->all());exit;

            foreach ($languages as $language) {
                $rules['ticker_heading_' . $language->id]     = 'required';
                $rules['ticker_description_' . $language->id] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                $objectNews                        = new Ticker();
                $objectNews->is_delete              = '0';

                $objectNews->is_active             = $request->get('is_active');
                $objectNews->created_by = $authUser->id;
                $objectNews->updated_by = $authUser->id;
                $objectNews->office_id  = $authUser->office_id;
                $objectNews->save();

                if ($objectNews->id) {
                    foreach ($languages as $language) {
                        $insertArray[] = [
                            'ticker_id'                => $objectNews->id,
                            'language_id'            => $language->id,
                            'ticker_title'           => $request->get('ticker_heading_' . $language->id),
                            'ticker_des'       => $request->get('ticker_description_' . $language->id),

                            'created_at'             => date("Y-m-d H:i:s"),
                            'updated_at'             => date("Y-m-d H:i:s"),
                        ];
                    }
                    DB::table('ticker_detail')->insert($insertArray);

                    return Redirect::to('admin/news-ticker')
                        ->with('type', 'success')
                        ->with('message', 'New news ticker is added successfully.');
                }
            }
        } catch (Throwable $e) {
            // dd($e->getMessage());
            return Redirect::back()->withInput()
                ->with('type', 'error')
                ->with('message', 'New news is not added.');
        }
    }

    public function deleteNewsTicker(Request $request)
    {
        $newsId                 = $request->get('id');
        $objectNews             = Ticker::find($newsId);
        $objectNews->is_active  = '0';
        $objectNews->is_delete = '1';
        $objectNews->save();

        if ($objectNews->id) {
            $data['type']    = "success";
            $data['message'] = "News ticker deleted Successfully.";
            exit(json_encode($data));
        } else {
            $array['type']    = "error";
            $array['message'] = "Server Error.";
            exit(json_encode($array));
        }
    }
    public function changeNewsTickerStatus(Request $request)
    {
        $newsId     = $request->get('id');
        $objectNews = Ticker::find($newsId);
        if ($objectNews->is_active == '0') {
            $objectNews->is_active = '1';
            $objectNews->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The news has been activated successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
        } else {
            $objectNews->is_active = '0';
            $objectNews->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The news has been deactivated successfully';
            $feedback['icon']    = '<i class="icon-check-empty"></i>';
        }
        exit(json_encode($feedback));
    }
}

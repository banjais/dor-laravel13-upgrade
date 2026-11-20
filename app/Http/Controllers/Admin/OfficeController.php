<?php

/**
 * Project Name: Department of Road(DOR)
 * @author    Veshraj Joshi
 * @modified by - Sunil Adhikari
 * @copytop 2015 Peace Nepal DOT Com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ConfigurationController;
use App\Models\Home;
use App\Models\Homedetail;
use App\Models\Language;
use App\Models\Office;
use App\Models\OfficeDetail;
use App\Models\Seo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class OfficeController extends ConfigurationController
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
     * Office Admin (user type 12) may manage only their own office profile.
     */
    private function isOfficeAdmin(): bool
    {
        return Auth::check() && (int) Auth::user()->user_type_id === 12;
    }

    /**
     * Keep Office Admin out of global Office Management actions.
     */
    private function denyOfficeAdminManagement(): void
    {
        abort_if($this->isOfficeAdmin(), 403, 'You are not authorized to manage offices.');
    }

    /*
     * get office list page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getIndex()
    {
        $this->denyOfficeAdminManagement();
        $offices    = Office::getUndeletedOffices();
        $officelist = '';
        $sn         = 1;
        foreach ($offices as $office) {
            $editlink    = '';
            $deletelink  = '';
            $publishlink = '';
            if ($office->is_active == 1) {
                $publish = '<i class="icon-check"></i>';
            } else {
                $publish = '<i class=" icon-check-empty"></i>';
            }

            $editlink    = '<a href="' . $this->abs_site_url . 'admin/office/edit/' . $office->id . '"><i class="icon-edit"></i>Edit</a>';
            $deletelink  = '<a href="javascript:;" class="delete-office" id="' . $office->id . '"><i class="icon-trash"></i> Delete</a> ';
            $publishlink = '<a href="javascript:;" style="font-size:1.4em;"  class="change-status" id="' . $office->id . '"><span >' . $publish . '</span></a>';

            $officelist .= '<tr class="">
									<td rowspan="=">' . $sn . '</td>
									<td >' . $office->office_name . '</td>
									<td >' . $office->office_address . '</td>
									<td>' . $office->office_phone_number1 . '</td>
									<td><a href="mailto:' . $office->office_email_address . '">' . $office->office_email_address . '</td>

									<td style="text-align:center">' . $publishlink . '</td>
									<td> ' . $editlink . ' ' . $deletelink . '</td>

								</tr>';
            $sn++;
        }

        return view('users.offices.offices')->with('offices', $officelist)
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }

    /*
     * get office add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function getAddOffice()
    {
        $this->denyOfficeAdminManagement();
        $languages = Language::getActiveLanguages();
        return view('users.offices.addoffice')->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('languages', $languages);
    }

    /*
     * post office add page admin panel
     * @param void
     * @return Illuminate\View\View
     */
    public function addOffice(Request $request)
    {
        $this->denyOfficeAdminManagement();
        // dd($request->all());
        Session::forget('attachment-error');
        $rule = [
            'office_subdomain' => 'required|unique:office,office_subdomain',
            'email' => 'required|email|max:254|unique:office,office_email_address',
            'alternative_email' => 'nullable|email|max:254|unique:office,office_alternative_email',
        ];

        $languages = Language::getActiveLanguages();
        foreach ($languages as $language) {
            $rule['name_' . $language->language_code]            = 'required';
            $rule['office_address_' . $language->language_code]  = 'required';
            $rule['primary_contact_' . $language->language_code] = 'required';
        }
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        try {
            $imageFile        = $request->file('attachment');
            $destinationPath  = 'uploads/offices/';
            $rEFileTypes      = "/^\.(jpg|jpeg|gif|png|pdf|xls|doc|docx){1}$/i";
            $maximum_filesize = 1 * 1024 * 1024;

            if ($imageFile) {
                $filename       = $imageFile->getClientOriginalName();
                $extension      = strrchr($filename, '.');
                $size           = $imageFile->getSize();
                $new_image_name = "office" . "_" . time();

                if ($size <= $maximum_filesize && preg_match($rEFileTypes, $extension)) {
                    $attachment = $imageFile->move($destinationPath, $new_image_name . $extension);
                } else if (preg_match($rEFileTypes, $extension) == false) {
                    Session::flash('attachment-error', 'Warning : Invalid Image File!');
                } else if ($size > $maximum_filesize) {
                    Session::flash('attachment-error', "  Warning : The attachment is not uploaded. It shouldn't be more than 1MB!");
                }
            }
            $logo = isset($attachment) ? $new_image_name . $extension : null;

            $zone_id = trim($request->get('zone_id'));
            if (!$zone_id) $zone_id = 0;

            $order_menu = trim($request->get('order_menu'));
            if (!$order_menu) $order_menu = 0;

            $objectOffice = new Office();
            $objectOffice->office_email_address = mb_strtolower(trim($request->get('email')));
            $objectOffice->office_alternative_email = $request->filled('alternative_email')
                ? mb_strtolower(trim($request->get('alternative_email')))
                : null;
            $objectOffice->office_subdomain = trim($request->get('office_subdomain'));
            $objectOffice->zone_id              = $zone_id;
            // $objectOffice->office_latitude      = trim($request->get('office_latitude'));
            // $objectOffice->office_longitude     = trim($request->get('office_longitude'));
            // $objectOffice->office_zoom          = trim($request->get('office_zoom'));
            $objectOffice->order_menu           = $order_menu;
            $objectOffice->created_by           = Auth::user()->id;
            $objectOffice->updated_by           = Auth::user()->id;
            $objectOffice->office_type          = ($request->get('office_type')) ? join(',', $request->get('office_type')) : '';
            $objectOffice->is_active            = $request->get('status');
            $objectOffice->is_deleted           = '0';
            $objectOffice->parent_id = $request->get('parent_id');

            if ($logo) {
                $objectOffice->office_attachment = $logo;
            }
            $objectOffice->save();
            //var_dump($objectOffice); die();
            if ($objectOffice) {
                $pages     = Seo::getPageNamesOfUndeletedSeos();
                // $homePages = Home::getShowInOfUndeleted();

                if (count($pages) > 0) {
                    $insertArray = array();

                    foreach ($pages as $page) {
                        $insertArray[] = [
                            'page_name'  => $page->page_name,
                            'office_id'  => $objectOffice->id,
                            'created_by' => Auth::user()->id,
                            'is_active'  => '1',
                            'is_deleted' => '0',
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s"),
                        ];
                    }
                    DB::table('seo')->insert($insertArray);
                }

                // if (count($homePages) > 0) {
                //     $insertArray = array();
                //     foreach ($homePages as $key=>$value) {
                //         $insertArray[] = [
                //             'show_in'    => $value,
                //             'home_url'   => $key,
                //             'office_id'  => $objectOffice->id,
                //             'created_by' => Auth::user()->id,
                //             'is_active'  => 1,
                //             'is_deleted' => 0,
                //             'created_at' => date("Y-m-d H:i:s"),
                //             'updated_at' => date("Y-m-d H:i:s"),
                //         ];
                //     }
                //     DB::table('home')->insert($insertArray);
                // }

                //echo Configuration::get_last_query();  die();

                //add extra home content for this newly created office_id//loop over each office
                $home_urls = ['facebook-feed', 'instagram-feed'];

                $languages = Language::getActiveLanguages();
                foreach ($home_urls as $key => $url) {
                    $home = Home::where(['office_id' => $objectOffice->id, 'home_url' => $url])->first();
                    if (!$home) {
                        $home  = Home::create([
                            'office_id' => $objectOffice->id,
                            'home_url' => $url,
                            'show_in' => $objectOffice->id,
                            'created_by' => 2,
                            'updated_by' => 2,
                            'is_active' => '1',
                            'is_deleted' => '0',
                            'home_attachment' => '',
                            'home_identity_name' => 'SOCIAL MEDIA',
                            'data_2_title' => $url === 'facebook-feed' ? 'Paste Facebook page link' : 'Instagram Profile URL'
                        ]);

                        //insert home detail also for the same
                        $insertArray = [];
                        foreach ($languages as $language) {
                            $insertArray[] = [
                                'home_id'                => $home->id,
                                'language_id'            => $language->id,
                                'home_heading'           => $home->home_url == 'facebook-feed' ? 'Facebook Feed' : 'Instagram Feed',
                                'home_description'       => '--',
                                'home_page_title'        => '--',
                                'created_at'             => date("Y-m-d H:i:s"),
                                'updated_at'             => date("Y-m-d H:i:s"),
                            ];
                        }

                        Homedetail::insert($insertArray);
                    }
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            $response['type']    = 'error';
            $response['message'] = 'Could not add the office';
            exit(json_encode($response));
        }
        try {
            if ($objectOffice) {
                foreach ($languages as $language) {
                    $data = array(
                        'office_name' => $request->get('name_' . $language->language_code),
                        'office_address'            => $request->get('office_address_' . $language->language_code),
                        'office_phone_number1'      => $request->get('primary_contact_' . $language->language_code),
                        'office_phone_number2'      => $request->get('secondary_contact_' . $language->language_code),
                        'language_id'               => $language->id,
                        'office_id'                 => $objectOffice->id,
                    );
                    OfficeDetail::create($data);
                }
            }
        } catch (Throwable $e) {
            dd($e->getMessage());
            return Redirect::route('admin.offices')
                ->with('type', 'error')
                ->with('message', 'Office is added but could not add the details.');
        }
        return Redirect::route('admin.offices')
            ->with('type', 'success')
            ->with('message', 'Office has been created Successfully.');
    }
    /**
     * This method is used add  the Office
     *

     * @author $author- Veshraj Joshi

     * @param array $array - $id

     * @param string  - none

     * @return View::(Offices::Offices.editOffice)

     */
    public function getEditOffice($id)
    {
        if ($this->isOfficeAdmin()) {
            $ownOfficeId = Auth::user()->office_id;
            abort_unless($ownOfficeId, 404, 'No office is assigned to this account.');

            if ((int) $id !== (int) $ownOfficeId) {
                return Redirect::route('admin.office.edit', ['id' => $ownOfficeId]);
            }

            $id = $ownOfficeId;
        }

        $Office = Office::where('is_deleted', '0')->findOrFail($id);
        $published    = '';
        $emailaddress = '';
        $unpublished  = 'selected';
        $regionalno   = 'selected';
        $regionalyes  = '';
        Session::forget('attachment-error');
        /*echo '<pre>';
        print_r(Office::getOfficeDetailsWithLanguages($id));
        echo'</pre>';;
        exit();*/
        $details = Office::getOfficeDetailsWithLanguages($id);

        if ($Office) {
            $emailaddress      = $Office->office_email_address;
            $office_subdomain  = $Office->office_subdomain;
            $zone_id           = $Office->zone_id;
            // $office_latitude   = $Office->office_latitude;
            // $office_longitude  = $Office->office_longitude;
            // $office_zoom       = $Office->office_zoom;
            $google_map_embed       = $Office->google_map_embed;
            $office_type       = $Office->office_type;
            $order_menu        = $Office->order_menu;
            $office_attachment = $Office->office_attachment;
            $parent_id         = $Office->parent_id;
            if ($Office->is_active == 1) {
                $unpublished = '';
                $published   = 'selected';
            }
        }

        $isOfficeAdmin = $this->isOfficeAdmin();
        $alternativeEmail = $Office->office_alternative_email ?? '';
        $facebookPage = $Office->office_facebook_page ?? '';
        $instagramPage = $Office->office_instagram_page ?? '';

        // Only Super Admin needs the global office list.
        $Officelist = '';
        if (!$isOfficeAdmin) {
            $Offices = Office::getUndeletedOffices();
            foreach ($Offices as $listedOffice) {
                $Officelist .= '<tr>
                    <td><a id="' . $listedOffice->id . '" class="editOffice" href="javascript:void(0);" style="text-decoration:none;" data-tip="Edit Office Details">' . $listedOffice->office_name . '</a></td>
                    <td style="text-align:center"><a id="' . $listedOffice->id . '" class="editOffice" href="javascript:void(0);" style="text-decoration:none; font-size:1.2em" data-tip="Edit Office Details"><i class="icon-edit"></i></a></td>
                </tr>';
            }
        }

        return view('users.offices.editoffice')->with('id', $id)
            ->with('zone_id', $zone_id)
            ->with('email', $emailaddress)
            ->with('alternative_email', $alternativeEmail)
            ->with('facebook_page', $facebookPage)
            ->with('instagram_page', $instagramPage)
            ->with('google_maps_link', '')
            ->with('isOfficeAdmin', $isOfficeAdmin)
            ->with('office_subdomain', $office_subdomain)
            // ->with('office_latitude', $office_latitude)
            // ->with('office_longitude', $office_longitude)
            // ->with('office_zoom', $office_zoom)
            ->with('google_map_embed', $google_map_embed)
            ->with('details', $details)
            ->with('published', $published)
            ->with('unpublished', $unpublished)
            ->with('office_type', $office_type)
            ->with('parent_id', $parent_id)
            ->with('order_menu', $order_menu)
            ->with('office_attachment', $office_attachment)
            ->with('officelist', $Officelist)
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('languages', Language::getActiveLanguages());
    }
    /**
     * This method is used edit  the Office
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function editOffice(Request $request)
    {
        // Office Admin may update only the office assigned to the authenticated account.
        if ($this->isOfficeAdmin()) {
            $officeId = Auth::user()->office_id;
            abort_unless($officeId, 404, 'No office is assigned to this account.');

            $languages = Language::getActiveLanguages();
            $rules = [
                'email' => 'nullable|email|max:254',
                'alternative_email' => 'nullable|email|max:254',
                'facebook_page' => ['nullable', 'url', 'max:1000', function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $host = strtolower(parse_url($value, PHP_URL_HOST) ?? '');
                    $allowedHosts = ['facebook.com', 'www.facebook.com', 'm.facebook.com', 'fb.com', 'www.fb.com'];

                    if (!in_array($host, $allowedHosts, true)) {
                        $fail('Please enter a valid Facebook Page URL.');
                    }
                }],
                'instagram_page' => ['nullable', 'url', 'max:1000', function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $host = strtolower(parse_url($value, PHP_URL_HOST) ?? '');
                    if (!in_array($host, ['instagram.com', 'www.instagram.com', 'm.instagram.com'], true)) {
                        $fail('Please enter a valid Instagram Profile URL.');
                    }
                }],
                'google_maps_link' => ['nullable', 'url', 'max:2048', function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }
                    $parts = parse_url($value);
                    $host = strtolower($parts['host'] ?? '');
                    $path = $parts['path'] ?? '';
                    $allowedHosts = ['google.com', 'www.google.com', 'maps.google.com'];

                    if (!in_array($host, $allowedHosts, true) ||
                        ($host !== 'maps.google.com' && !str_contains($path, '/maps'))) {
                        $fail('Please enter a full Google Maps link copied using Share > Copy link.');
                    }
                }],
            ];

            foreach ($languages as $language) {
                $code = $language->language_code;
                $rules['name_' . $code] = 'required|string|max:255';
                $rules['office_address_' . $code] = 'required|string|max:1000';
                $rules['primary_contact_' . $code] = 'required|string|max:100';
                $rules['secondary_contact_' . $code] = 'nullable|string|max:100';
            }
        $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }

            DB::transaction(function () use ($request, $officeId, $languages) {
                $office = Office::where('is_deleted', '0')
                    ->lockForUpdate()
                    ->findOrFail($officeId);

                $office->office_email_address = trim((string) $request->input('email', '')) ?: null;
                $office->office_alternative_email = trim((string) $request->input('alternative_email', '')) ?: null;
                $office->office_facebook_page = trim((string) $request->input('facebook_page', '')) ?: null;
                $office->office_instagram_page = trim((string) $request->input('instagram_page', '')) ?: null;

                // Synchronize only the authenticated Office Admin account.
                // Primary email takes precedence over the alternative email.
                $accountEmail = $office->office_email_address ?: $office->office_alternative_email;

                if ($accountEmail !== null && trim($accountEmail) !== '') {
                    $officeAdmin = User::query()
                        ->whereKey(Auth::id())
                        ->where('user_type_id', 12)
                        ->where('office_id', $officeId)
                        ->where('is_active', '1')
                        ->whereNull('deleted_at')
                        ->lockForUpdate()
                        ->firstOrFail();

                    $accountEmail = trim($accountEmail);
                    $normalizedEmail = mb_strtolower($accountEmail);

                    $emailInUse = User::query()
                        ->where('id', '<>', $officeAdmin->id)
                        ->where('is_active', '1')
                        ->whereNull('deleted_at')
                        ->where(function ($query) use ($normalizedEmail) {
                            $query->whereRaw('LOWER(email_address) = ?', [$normalizedEmail])
                                ->orWhereRaw('LOWER(username) = ?', [$normalizedEmail]);
                        })
                        ->exists();

                    if ($emailInUse) {
                        throw ValidationException::withMessages([
                            'email' => 'This email address is already used by another account. Please use a different Office Profile email.',
                        ]);
                    }

                    $officeAdmin->email_address = $accountEmail;
                    $officeAdmin->username = $accountEmail;
                    $officeAdmin->save();
                }

                // Empty map input preserves the existing map. Raw HTML is never accepted.
                $mapLink = trim((string) $request->input('google_maps_link', ''));
                if ($mapLink !== '') {
                    $embedUrl = 'https://www.google.com/maps?output=embed&q=' . rawurlencode($mapLink);
                    $office->google_map_embed = '<iframe src="' . e($embedUrl) . '" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                }

                $office->save();

                foreach ($languages as $language) {
                    $code = $language->language_code;
                    $detail = OfficeDetail::firstOrNew([
                        'office_id' => $office->id,
                        'language_id' => $language->id,
                    ]);

                    $detail->office_name = trim($request->input('name_' . $code));
                    $detail->office_address = trim($request->input('office_address_' . $code));
                    $detail->office_phone_number1 = trim($request->input('primary_contact_' . $code));
                    $detail->office_phone_number2 = trim((string) $request->input('secondary_contact_' . $code, ''));
                    $detail->save();
                }
            });

            // Refresh search indexes after the Office Admin profile update succeeds.
            DB::table('notice')
                ->where('office_id', $officeId)
                ->where('is_active', '1')
                ->where('is_deleted', '0')
                ->orderBy('id')
                ->chunkById(100, function ($notices) {
                    foreach ($notices as $notice) {
                        app(\App\Services\NoticeSearchIndexer::class)
                            ->rebuildNotice((int) $notice->id);
                    }
                });

            return Redirect::route('admin.office.edit', ['id' => $officeId])
                ->with('type', 'success')
                ->with('message', 'Office profile has been updated successfully.');
        }

        $languages = Language::getActiveLanguages();
        $id       = $request->get('id');
        $rule['email'] = 'required|email|max:254';
        $rule['alternative_email'] = 'nullable|email|max:254';
        $rule['facebook_page'] = 'nullable|url|max:1000';
        $rule['instagram_page'] = ['nullable', 'url', 'max:1000', function ($attribute, $value, $fail) {
            if (!$value) {
                return;
            }
            $host = strtolower(parse_url($value, PHP_URL_HOST) ?? '');
            if (!in_array($host, ['instagram.com', 'www.instagram.com', 'm.instagram.com'], true)) {
                $fail('Please enter a valid Instagram Profile URL.');
            }
        }];
        foreach ($languages as $language) {
            $rule['name_' . $language->language_code]            = 'required';
            $rule['office_address_' . $language->language_code]  = 'required';
            $rule['primary_contact_' . $language->language_code] = 'required';
        }
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->with($validator);
        }

        $Office = Office::findOrFail($id);
        $Office->office_email_address = mb_strtolower(trim($request->get('email')));
        $Office->office_alternative_email = $request->filled('alternative_email')
            ? mb_strtolower(trim($request->get('alternative_email')))
            : null;
        $Office->office_facebook_page = trim((string) $request->input('facebook_page', '')) ?: null;
        $Office->office_instagram_page = trim((string) $request->input('instagram_page', '')) ?: null;
        $Office->office_subdomain = trim($request->get('office_subdomain'));
        // $Office->office_latitude      = trim($request->get('office_latitude'));
        // $Office->office_longitude     = trim($request->get('office_longitude'));
        // $Office->office_zoom          = trim($request->get('office_zoom'));
        $Office->order_menu           = trim($request->get('order_menu'));
        $Office->google_map_embed           = trim($request->get('google_map_embed'));

        $zone_id = trim($request->get('zone_id'));
        if (!$zone_id) $zone_id = 0;
        $parent_id = trim($request->get('parent_id'));
        if (!$zone_id) $parent_id = 0;

        $order_menu = trim($request->get('order_menu'));
        if (!$order_menu) $order_menu = 0;

        $request->get('office_type') ? $Office->office_type = join(',', $request->get('office_type')) : '';
        $Office->parent_id                               = $parent_id;
        $Office->is_active                               = $request->get('status');
        $Office->zone_id                                 = $zone_id;
        $Office->save();

        foreach ($languages as $language) {
            $detailid   = $request->get('id_' . $language->language_code);
            $detailitem = OfficeDetail::find($detailid);
            if ($detailitem) {
                $detailitem->office_phone_number1 = trim($request->get('primary_contact_' . $language->language_code));
                $detailitem->office_phone_number2 = trim($request->get('secondary_contact_' . $language->language_code));
                $detailitem->office_name          = trim($request->get('name_' . $language->language_code));
                $detailitem->office_address       = trim($request->get('office_address_' . $language->language_code));
                $detailitem->save();
                continue;
            }
            $detaildata = array(
                'language_id' => $language->id,
                'office_id'                       => $Office->id,
                'office_phone_number1'            => trim($request->get('primary_contact_' . $language->language_code)),
                'office_phone_number2'            => trim($request->get('secondary_contact_' . $language->language_code)),
                'office_name'                     => trim($request->get('office_name_' . $language->language_code)),
                'office_address'                  => trim($request->get('office_address_' . $language->language_code))
            );
            OfficeDetail::create($detaildata);
        }

        // Refresh search indexes after the Super Admin office update succeeds.
        DB::table('notice')
            ->where('office_id', $Office->id)
            ->where('is_active', '1')
            ->where('is_deleted', '0')
            ->orderBy('id')
            ->chunkById(100, function ($notices) {
                foreach ($notices as $notice) {
                    app(\App\Services\NoticeSearchIndexer::class)
                        ->rebuildNotice((int) $notice->id);
                }
            });

        return Redirect::route('admin.offices')
            ->with('type', 'success')
            ->with('message', 'Office has been updated Successfully.');
    }
    /**
     * This method is used delete  the Office
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function deleteOffice(Request $request)
    {
        $this->denyOfficeAdminManagement();
        $id                 = $request->get('id');
        $Office             = Office::find($id);
        $Office->is_deleted = '1';
        $Office->save();
        $data['type']    = "success";
        $data['message'] = "Offfice has beenn deleted successfully";
        echo json_encode($data);
    }

    public function getSortOrder()
    {
        $this->denyOfficeAdminManagement();
        $offices = Office::getUndeletedOffices();
        // print_r($offices); exit;
        return view('users.offices.sort_order')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('offices', $offices)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name)
            ->with('languages', Language::getActiveLanguages());
    }

    public function sortOrder(Request $request)
    {
        $this->denyOfficeAdminManagement();
        $returnArray = array('type' => 'error', 'message' => 'Server Error!');
        $offices     = $request->get('office');

        $position = 1;
        foreach ($offices as $officeId) {
            $objectOffice             = Office::find($officeId);
            $objectOffice->order_menu = $position;
            $objectOffice->save();
            $position++;
        }
        $returnArray = array('type' => 'success', 'offices' => $offices, 'message' => 'Successfully Offices are sorted.');
        return json_encode($returnArray);
    }

    /**
     * This method is used add  the Office
     *

     * @author $author- Veshraj Joshi

     * @param array $array - no paramenter

     * @param string  - none

     * @return json_message_string_array

     */
    public function changeStatus(Request $request)
    {
        $this->denyOfficeAdminManagement();
        $id     = $request->get('id');
        $Office = Office::find($id);
        if ($Office->is_active == '0') {
            $Office->is_active = '1';
            $Office->save();
            $feedback['type']    = 'success';
            $feedback['message'] = 'The Office has been activate successfully';
            $feedback['icon']    = '<i class="icon-check"></i>';
            exit(json_encode($feedback));
        }
        $Office->is_active = '0';
        $Office->save();
        $feedback['type']    = 'success';
        $feedback['message'] = 'The Office has been deactivate successfully';
        $feedback['icon']    = '<i class="icon-check-empty"></i>';
        exit(json_encode($feedback));
    }

    /**
     * This method is used get Office details dynamically
     *
     * @author Veshraj Joshi
     * @param array $array - no paramenter
     * @param string  - none
     * @return json_message_string_array
     */
    public function getOfficeDetails(Request $request)
    {
        $id      = $request->get('id');
        $Office  = Office::find($id);
        $details = Office::getOfficeDetailsWithLanguages($id);
        foreach ($details as $detail) {
            $feedback['id_' . $detail->language_code]                = $detail->detailid;
            $feedback['name_' . $detail->language_code]              = $detail->office_name;
            $feedback['office_address_' . $detail->language_code]    = $detail->office_address;
            $feedback['primary_contact_' . $detail->language_code]   = $detail->office_phone_number1;
            $feedback['secondary_contact_' . $detail->language_code] = $detail->office_phone_number2;
        }
        $feedback['type'] = 'success';
        $feedback['id']   = $id;

        $feedback['zone_id']          = $Office->zone_id;
        $feedback['email']            = $Office->office_email_address;
        $feedback['office_subdomain'] = $Office->office_subdomain;
        $feedback['office_latitude']  = $Office->office_latitude;
        $feedback['office_longitude'] = $Office->office_longitude;
        $feedback['office_zoom']      = $Office->office_zoom;
        $feedback['published']        = $Office->is_active;
        $feedback['regional']         = in_array(2, explode(",", $Office->office_type)) ? true : false;
        $feedback['division']         = in_array(3, explode(",", $Office->office_type)) ? true : false;

        exit(json_encode($feedback));
    }

    /**
     * This method is used get details of the office
     *
     * @author $author- Veshraj Joshi
     *
     * @param array $array - no paramenter
     *
     * @param string  - none
     *
     * @return json_message_string_array

     */
    public function getDetailsOfOffice($id)
    {
        $officedetails = '<table>';
        $details       = Office::getOfficeDetailsWithLanguages($id);
        foreach ($details as $detail) {
            $officedetails .= '<tr>
								<td width="20%">' . $detail->language_name . '</td>
								<td width="40%">' . $detail->office_name . '</td>
								<td width="40%">' . $detail->office_address . '</td>
							</tr>';
        }
        return $officedetails . '</table>';
    }
}

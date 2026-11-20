<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ConfigurationController;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ContactBccSettingController extends Controller
{
    private function ensureSuperAdmin(): void
    {
        abort_unless(
            Auth::check() && (int) Auth::user()->user_type_id === 2,
            403
        );
    }

    public function index()
    {
        $this->ensureSuperAdmin();

        $grsEnabled = Configuration::getValueFromName('contact_grs_bcc_enabled');
        $grsEmail = Configuration::getValueFromName('contact_grs_bcc_email');

        $grsEnabled = $grsEnabled === null ? true : (bool) $grsEnabled;
        $grsEmail = $grsEmail ?: 'grs@dor.gov.np';

        $site_name = ConfigurationController::site_name();
        $abs_site_url = ConfigurationController::abs_site_url();

        return view('contact_bcc_settings.admin.index', compact(
            'grsEnabled',
            'grsEmail',
            'site_name',
            'abs_site_url'
        ));
    }

    public function update(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'grs_enabled' => ['required', 'boolean'],
            'grs_email' => ['required', 'email', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ([
                    'contact_grs_bcc_enabled' => (string) $validated['grs_enabled'],
                    'contact_grs_bcc_email' => $validated['grs_email'],
                ] as $name => $value) {
                    $existing = DB::table('configuration')
                        ->where('configuration_name', $name)
                        ->first();

                    $data = [
                        'configuration_value' => $value,
                        'is_editable' => 1,
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ];

                    if ($existing) {
                        DB::table('configuration')
                            ->where('configuration_name', $name)
                            ->update($data);
                    } else {
                        $data['configuration_name'] = $name;
                        $data['created_at'] = now();

                        DB::table('configuration')->insert($data);
                    }
                }
            });

            return redirect()
                ->route('admin.contact-bcc-settings.index')
                ->with('message', 'GRMS settings saved successfully.')
                ->with('type', 'success');
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.contact-bcc-settings.index')
                ->with('message', 'GRMS settings could not be saved. Please try again.')
                ->with('type', 'error');
        }
    }
}

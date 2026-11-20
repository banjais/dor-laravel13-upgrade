<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteIdentitySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WebsiteIdentitySettingController extends Controller
{
    private function ensureSuperAdmin(): void
    {
        abort_unless(
            Auth::check() && (int) Auth::user()->user_type_id === 2,
            403
        );
    }

    public function update(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'government_name_np' => ['required', 'string', 'max:255'],
            'government_name_en' => ['required', 'string', 'max:255'],
            'ministry_name_np' => ['required', 'string', 'max:255'],
            'ministry_name_en' => ['required', 'string', 'max:255'],
            'department_name_np' => ['required', 'string', 'max:255'],
            'department_name_en' => ['required', 'string', 'max:255'],
        ]);

        $identity = WebsiteIdentitySetting::current();
        $identity->fill($validated);
        $identity->updated_by = Auth::id();
        $identity->save();

        Cache::forget('website_identity_global');

        return redirect()
            ->route('admin.website-theme.index')
            ->with('message', 'Government identity settings have been saved successfully.')
            ->with('type', 'success');
    }
}

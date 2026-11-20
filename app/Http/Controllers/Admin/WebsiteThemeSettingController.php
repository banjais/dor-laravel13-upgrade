<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ConfigurationController;
use App\Models\WebsiteThemeSetting;
use App\Models\WebsiteIdentitySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WebsiteThemeSettingController extends Controller
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

        $theme = WebsiteThemeSetting::current();
        $identity = WebsiteIdentitySetting::current();

        $site_name = ConfigurationController::site_name();
        $abs_site_url = ConfigurationController::abs_site_url();

        return view('website_theme.admin.index', compact('theme', 'identity', 'site_name', 'abs_site_url'));
    }

    public function update(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'page_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'card_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'nav_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'footer_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'border_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $theme = WebsiteThemeSetting::current();
        $theme->fill($validated);
        $theme->updated_by = Auth::id();
        $theme->save();

        Cache::forget('website_theme_global');

        return redirect()
            ->route('admin.website-theme.index')
            ->with('message', 'Website theme settings have been saved successfully.')
            ->with('type', 'success');
    }

    public function reset()
    {
        $this->ensureSuperAdmin();

        WebsiteThemeSetting::resetToDefaults(Auth::id());
        Cache::forget('website_theme_global');

        return redirect()
            ->route('admin.website-theme.index')
            ->with('message', 'Website theme has been reset to the default colours.')
            ->with('type', 'success');
    }
}

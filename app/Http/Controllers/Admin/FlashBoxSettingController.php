<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ConfigurationController;
use App\Models\FlashBoxSetting;
use App\Models\Home;
use App\Models\UserAccessControlModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlashBoxSettingController extends Controller
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
        $user = Auth::user();
        $userId = $user->id;
        $officeId = $user->office_id;

        $viewAccess = UserAccessControlModel::getAccessViewByModuleAndUserMapId(1, $userId);

        if (!$viewAccess) {
            return view('error')
                ->with('abs_site_url', ConfigurationController::abs_site_url())
                ->with('rel_site_url', ConfigurationController::rel_site_url())
                ->with('site_name', ConfigurationController::site_name());
        }

        $addAccess = UserAccessControlModel::getAccessAddByModuleAndUserMapId(1, $userId);
        $editAccess = UserAccessControlModel::getAccessUpdateByModuleAndUserMapId(1, $userId);
        $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(1, $userId);

        $flashBoxes = Home::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])
            ->where('office_id', $officeId)
            ->where('is_deleted', '0')
            ->whereRaw("home_url REGEXP '^general-information(-[0-9]+)?$'")
            ->orderBy('id')
            ->get();

        $isSuperAdmin = (int) $user->user_type_id === 2;
        $settings = $isSuperAdmin ? FlashBoxSetting::current() : null;

        return view('flash_box_settings.admin.index', [
            'settings' => $settings,
            'flashBoxes' => $flashBoxes,
            'addAccess' => $addAccess,
            'editAccess' => $editAccess,
            'publishAccess' => $publishAccess,
            'isSuperAdmin' => $isSuperAdmin,
            'site_name' => ConfigurationController::site_name(),
            'abs_site_url' => ConfigurationController::abs_site_url(),
            'rel_site_url' => ConfigurationController::rel_site_url(),
        ]);
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $user = Auth::user();
        $publishAccess = UserAccessControlModel::getAccessPublishByModuleAndUserMapId(1, $user->id);

        abort_unless($publishAccess, 403);

        $home = Home::where('id', $request->input('id'))
            ->where('office_id', $user->office_id)
            ->where('is_deleted', '0')
            ->whereRaw("home_url REGEXP '^general-information(-[0-9]+)?$'")
            ->firstOrFail();

        $home->is_active = $home->is_active == '1' ? '0' : '1';
        $home->save();

        return response()->json([
            'type' => 'success',
            'message' => $home->is_active == '1'
                ? 'Flash Box published successfully.'
                : 'Flash Box unpublished successfully.',
            'icon' => $home->is_active == '1'
                ? '<i class="icon-check"></i>'
                : '<i class="icon-check-empty"></i>',
        ]);
    }

    public function update(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'animation_enabled' => ['required', 'boolean'],
            'autoplay_enabled' => ['required', 'boolean'],
            'autoplay_speed' => ['required', 'integer', 'min:2000', 'max:15000'],
            'animation_effect' => ['required', 'in:slide,fade'],
            'pause_on_hover' => ['required', 'boolean'],
            'arrows_enabled' => ['required', 'boolean'],
            'dots_enabled' => ['required', 'boolean'],
            'mobile_animation_enabled' => ['required', 'boolean'],
        ]);

        try {
            $settings = FlashBoxSetting::current();
            $settings->fill($validated);
            $settings->updated_by = Auth::id();

            if (!$settings->save()) {
                return redirect()
                    ->route('admin.flash-box-settings.index')
                    ->with('message', 'Flash Box settings could not be saved. Please try again.')
                    ->with('type', 'error');
            }

            return redirect()
                ->route('admin.flash-box-settings.index')
                ->with('message', 'Flash Box settings saved successfully.')
                ->with('type', 'success');

        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.flash-box-settings.index')
                ->with('message', 'An error occurred while saving Flash Box settings. Please try again.')
                ->with('type', 'error');
        }
    }
}

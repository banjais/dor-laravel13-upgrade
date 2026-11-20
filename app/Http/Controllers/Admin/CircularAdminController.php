<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ConfigurationController;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CircularAdminController extends Controller
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name = ConfigurationController::site_name();
    }

    private function siteViewData(): array
    {
        return [
            'abs_site_url' => $this->abs_site_url,
            'rel_site_url' => $this->rel_site_url,
            'site_name' => $this->site_name,
        ];
    }

    private function managerRole(): int
    {
        $roleId = (int) Auth::user()->user_type_id;

        abort_unless(in_array($roleId, [2, 12], true), 403);

        if ($roleId === 12) {
            abort_if(!Auth::user()->office_id, 403, 'Your account is not linked to an office.');
        }

        return $roleId;
    }

    private function accountsQuery()
    {
        $roleId = $this->managerRole();

        $query = User::with([
            'office.details' => function ($query) {
                $query->where('language_id', 2);
            },
        ])
            ->where('user_type_id', 13)
            ->whereNull('deleted_at');

        if ($roleId === 12) {
            $query->where('office_id', Auth::user()->office_id);
        }

        return $query;
    }

    private function availableOffices()
    {
        return Office::with(["details" => function ($query) {
            $query->where("language_id", 2);
        }])
            ->where("is_deleted", "0")
            ->orderBy("id")
            ->get()
            ->map(function ($office) {
                $office->office_name = $office->details->first()?->office_name ?? ("Office #" . $office->id);

                return $office;
            });
    }

    public function index()
    {
        $this->managerRole();

        $accounts = $this->accountsQuery()
            ->orderBy('office_id')
            ->orderBy('username')
            ->get();

        return view('users.circular-admins.index', array_merge(compact('accounts'), $this->siteViewData()));
    }

    public function create()
    {
        $roleId = $this->managerRole();
        $offices = $roleId === 2 ? $this->availableOffices() : collect();
        $selectedOfficeId = $roleId === 12 ? Auth::user()->office_id : null;
        $selectedOfficeName = null;

        if ($roleId === 12) {
            $office = Office::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])->find($selectedOfficeId);

            $selectedOfficeName = $office?->details?->first()?->office_name
                ?? 'Your assigned office';
        }

        return view('users.circular-admins.form', array_merge([
            'account' => null,
            'offices' => $offices,
            'selectedOfficeId' => $selectedOfficeId,
            'selectedOfficeName' => $selectedOfficeName,
        ], $this->siteViewData()));
    }

    public function store(Request $request)
    {
        $roleId = $this->managerRole();

        $officeId = $roleId === 12
            ? Auth::user()->office_id
            : $request->input('office_id');

        $request->merge([
            'email_address' => strtolower(trim((string) $request->input('email_address'))),
        ]);

        $request->validate([
            'email_address' => [
                'required', 'email', 'max:100',
                Rule::unique('users', 'email_address'),
                Rule::unique('users', 'username'),
            ],
            'password' => [
                'required', 'string', 'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/',
                'same:confirm_password',
            ],
            'confirm_password' => ['required', 'same:password'],
            'office_id' => $roleId === 2 ? ['required', 'integer', 'exists:office,id'] : ['prohibited'],
        ]);

        abort_unless(
            Office::where('id', $officeId)->where('is_deleted', '0')->exists(),
            422,
            'The selected office is unavailable.'
        );

        $account = new User();
        $account->username = $request->email_address;
        $account->email_address = $request->email_address;
        $account->password = Hash::make($request->password);
        $account->office_id = $officeId;
        $account->project_id = null;
        $account->user_type_id = 13;
        $account->is_active = 1;
        $account->created_by = Auth::id();
        $account->save();

        return redirect()
            ->route('admin.circular-admin.index')
            ->with('success', 'Circular Admin account created successfully.');
    }

    public function edit($id)
    {
        $roleId = $this->managerRole();
        $account = $this->accountsQuery()->whereKey($id)->firstOrFail();
        $offices = $roleId === 2 ? $this->availableOffices() : collect();
        $selectedOfficeName = null;

        if ($roleId === 12) {
            $office = Office::with(['details' => function ($query) {
                $query->where('language_id', 2);
            }])->find($account->office_id);

            $selectedOfficeName = $office?->details?->first()?->office_name
                ?? 'Your assigned office';
        }

        return view('users.circular-admins.form', array_merge([
            'account' => $account,
            'offices' => $offices,
            'selectedOfficeId' => $account->office_id,
            'selectedOfficeName' => $selectedOfficeName,
        ], $this->siteViewData()));
    }

    public function update(Request $request, $id)
    {
        $roleId = $this->managerRole();
        $account = $this->accountsQuery()->whereKey($id)->firstOrFail();

        $request->merge([
            'email_address' => strtolower(trim((string) $request->input('email_address'))),
        ]);

        $request->validate([
            'email_address' => [
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email_address')->ignore($account->id),
                Rule::unique('users', 'username')->ignore($account->id),
            ],
            'password' => [
                'nullable', 'string', 'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/',
                'same:confirm_password',
            ],
            'confirm_password' => ['required_with:password', 'same:password'],
            'office_id' => $roleId === 2 ? ['required', 'integer', 'exists:office,id'] : ['prohibited'],
            'is_active' => ['required', 'boolean'],
        ]);

        $officeId = $roleId === 12
            ? Auth::user()->office_id
            : $request->input('office_id');

        abort_unless(
            Office::where('id', $officeId)->where('is_deleted', '0')->exists(),
            422,
            'The selected office is unavailable.'
        );

        $account->username = $request->email_address;
        $account->email_address = $request->email_address;
        $account->office_id = $officeId;
        $account->is_active = (int) $request->is_active;
        $account->updated_by = Auth::id();

        if ($request->filled('password')) {
            $account->password = Hash::make($request->password);
        }

        $account->save();

        return redirect()
            ->route('admin.circular-admin.index')
            ->with('success', 'Circular Admin account updated successfully.');
    }

    public function toggleStatus($id)
    {
        $this->managerRole();
        $account = $this->accountsQuery()->whereKey($id)->firstOrFail();

        $account->is_active = $account->is_active ? 0 : 1;
        $account->updated_by = Auth::id();
        $account->save();

        return redirect()
            ->route('admin.circular-admin.index')
            ->with('success', 'Circular Admin account status updated.');
    }
}

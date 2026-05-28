<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function __construct(private SubscriptionService $subscriptions)
    {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q'));

        $businesses = Business::query()
            ->whereNull('archived_at')
            ->with('subscription')
            ->withCount('users')
            ->when($q !== '', fn ($qb) => $qb->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            }))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.businesses.index', compact('businesses', 'q'));
    }

    public function archived(Request $request): View
    {
        $businesses = Business::query()
            ->whereNotNull('archived_at')
            ->whereNull('deleted_at')
            ->with('subscription')
            ->orderByDesc('archived_at')
            ->paginate(15);

        return view('superadmin.businesses.archived', compact('businesses'));
    }

    public function trashed(Request $request): View
    {
        $businesses = Business::onlyTrashed()
            ->with('subscription')
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('superadmin.businesses.trashed', compact('businesses'));
    }

    public function restoreDeleted(int $id): RedirectResponse
    {
        $business = Business::onlyTrashed()->findOrFail($id);
        $business->restore();
        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('status', 'Business restored from trash.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $business = Business::onlyTrashed()->findOrFail($id);
        $name = $business->name;
        $business->forceDelete(); // hard-delete + cascades to users/children/sessions/etc via FK
        return redirect()
            ->route('superadmin.businesses.trashed')
            ->with('status', "Permanently deleted '{$name}' and all its data.");
    }

    public function create(): View
    {
        return view('superadmin.businesses.create', [
            'plans' => Subscription::PLANS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'],
            'code'            => ['required', 'string', 'max:10', Rule::unique('businesses', 'code')],
            'primary_color'   => ['nullable', 'string', 'max:16'],
            'contact_email'   => ['nullable', 'email', 'max:120'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'address'         => ['nullable', 'string', 'max:500'],
            'currency_symbol' => ['nullable', 'string', 'max:8'],
            'logo'            => ['nullable', 'image', 'max:2048'],

            'owner_name'      => ['required', 'string', 'max:80'],
            'owner_email'     => ['required', 'email', Rule::unique('users', 'email')],
            'owner_password'  => ['required', 'string', 'min:6'],

            'plan_type'       => ['required', Rule::in(array_keys(Subscription::PLANS))],
        ]);

        $slug = Str::slug($data['name']).'-'.Str::lower(Str::random(4));

        $business = Business::create([
            'name'            => $data['name'],
            'slug'            => $slug,
            'code'            => Str::upper($data['code']),
            'primary_color'   => $data['primary_color'] ?? '#f97316',
            'email'           => $data['contact_email'] ?? null,
            'phone'           => $data['phone'] ?? null,
            'address'         => $data['address'] ?? null,
            'currency_symbol' => $data['currency_symbol'] ?? '₹',
            'is_active'       => true,
        ]);

        if ($request->hasFile('logo')) {
            $business->logo_path = $request->file('logo')->store('branding', 'public');
            $business->save();
        }

        User::create([
            'business_id' => $business->id,
            'name'        => $data['owner_name'],
            'email'       => $data['owner_email'],
            'password'    => Hash::make($data['owner_password']),
            'role'        => 'admin',
            'is_active'   => true,
        ]);

        $this->subscriptions->applyPlan($business, $data['plan_type']);

        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('status', 'Business created with admin account & subscription.');
    }

    public function show(Business $business): View
    {
        $business->load(['subscription', 'users' => fn ($q) => $q->orderBy('name')]);
        return view('superadmin.businesses.show', [
            'business' => $business,
            'plans'    => Subscription::PLANS,
        ]);
    }

    public function edit(Business $business): View
    {
        return view('superadmin.businesses.edit', compact('business'));
    }

    public function update(Request $request, Business $business): RedirectResponse
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'],
            'code'            => ['required', 'string', 'max:10', Rule::unique('businesses', 'code')->ignore($business->id)],
            'primary_color'   => ['nullable', 'string', 'max:16'],
            'email'           => ['nullable', 'email', 'max:120'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'address'         => ['nullable', 'string', 'max:500'],
            'currency_symbol' => ['nullable', 'string', 'max:8'],
            'logo'            => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        $data['code'] = Str::upper($data['code']);

        $business->update(collect($data)->except('logo')->toArray());

        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('status', 'Business updated.');
    }

    public function deactivate(Business $business): RedirectResponse
    {
        $business->is_active = false;
        $business->save();
        return back()->with('status', 'Business deactivated. Tenant login is now blocked.');
    }

    public function reactivate(Business $business): RedirectResponse
    {
        $business->is_active = true;
        $business->archived_at = null;
        $business->save();
        return back()->with('status', 'Business reactivated.');
    }

    public function archive(Business $business): RedirectResponse
    {
        $business->is_active = false;
        $business->archived_at = now();
        $business->save();
        return back()->with('status', 'Business archived.');
    }

    public function restore(Business $business): RedirectResponse
    {
        $business->archived_at = null;
        $business->is_active = true;
        $business->save();
        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('status', 'Business restored from archive.');
    }

    public function destroy(Business $business): RedirectResponse
    {
        $business->delete(); // soft delete
        return redirect()
            ->route('superadmin.businesses.index')
            ->with('status', 'Business soft-deleted.');
    }
}

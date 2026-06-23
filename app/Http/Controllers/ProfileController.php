<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AppSetting;
use App\Support\BillingDueReminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile and settings form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $data = [
            'user' => $user,
        ];

        if ($user->isAdmin()) {
            $data['domainDueDate'] = AppSetting::get(AppSetting::KEY_DOMAIN_DUE);
            $data['hostingDueDate'] = AppSetting::get(AppSetting::KEY_HOSTING_DUE);
            $data['domainStatus'] = BillingDueReminder::billingStatus(AppSetting::KEY_DOMAIN_DUE, 'Domain');
            $data['hostingStatus'] = BillingDueReminder::billingStatus(AppSetting::KEY_HOSTING_DUE, 'Hosting');
        }

        return view('profile.edit', $data);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}

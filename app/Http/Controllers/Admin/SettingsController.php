<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'domain_payment_due_date' => ['nullable', 'date'],
            'hosting_payment_due_date' => ['nullable', 'date'],
        ]);

        AppSetting::set(
            AppSetting::KEY_DOMAIN_DUE,
            $data['domain_payment_due_date'] ?? null,
        );
        AppSetting::set(
            AppSetting::KEY_HOSTING_DUE,
            $data['hosting_payment_due_date'] ?? null,
        );

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Pengaturan jatuh tempo diperbarui.');
    }
}

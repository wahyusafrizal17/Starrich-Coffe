<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'domainDueDate' => AppSetting::get(AppSetting::KEY_DOMAIN_DUE),
            'hostingDueDate' => AppSetting::get(AppSetting::KEY_HOSTING_DUE),
        ]);
    }

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
            ->route('admin.settings.edit')
            ->with('success', 'Pengaturan jatuh tempo diperbarui.');
    }
}

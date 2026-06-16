<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Carbon;

class BillingDueReminder
{
    public const ALERT_DAYS_BEFORE = 5;

    /**
     * @return list<array{
     *     type: string,
     *     label: string,
     *     due_date: Carbon,
     *     days_remaining: int,
     *     is_overdue: bool,
     *     severity: 'warning'|'danger'
     * }>
     */
    public static function alerts(): array
    {
        $items = [
            [
                'type' => 'domain',
                'label' => 'Domain',
                'key' => AppSetting::KEY_DOMAIN_DUE,
            ],
            [
                'type' => 'hosting',
                'label' => 'Hosting',
                'key' => AppSetting::KEY_HOSTING_DUE,
            ],
        ];

        $alerts = [];
        $today = now()->startOfDay();

        foreach ($items as $item) {
            $dateStr = AppSetting::get($item['key']);
            if (! $dateStr) {
                continue;
            }

            $due = Carbon::parse($dateStr)->startOfDay();
            $daysRemaining = (int) $today->diffInDays($due, false);

            if ($daysRemaining > self::ALERT_DAYS_BEFORE) {
                continue;
            }

            $alerts[] = [
                'type' => $item['type'],
                'label' => $item['label'],
                'due_date' => $due,
                'days_remaining' => $daysRemaining,
                'is_overdue' => $daysRemaining < 0,
                'severity' => $daysRemaining <= 2 ? 'danger' : 'warning',
            ];
        }

        return $alerts;
    }

    public static function message(array $alert): string
    {
        $days = $alert['days_remaining'];
        $label = $alert['label'];
        $date = $alert['due_date']->translatedFormat('d M Y');

        if ($days < 0) {
            $overdue = abs($days);

            return "Pembayaran {$label} sudah lewat jatuh tempo {$overdue} hari ({$date}).";
        }

        if ($days === 0) {
            return "Pembayaran {$label} jatuh tempo hari ini ({$date}).";
        }

        return "Pembayaran {$label} jatuh tempo dalam {$days} hari ({$date}).";
    }
}

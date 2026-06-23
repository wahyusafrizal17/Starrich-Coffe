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

    /**
     * @return array{
     *     label: string,
     *     configured: bool,
     *     due_date: ?string,
     *     days_remaining: ?int,
     *     icon: string,
     *     value: string,
     *     caption: string
     * }
     */
    public static function billingStatus(string $key, string $label): array
    {
        $dateStr = AppSetting::get($key);
        if (! $dateStr) {
            return [
                'label' => $label,
                'configured' => false,
                'due_date' => null,
                'days_remaining' => null,
                'icon' => 'vx-bg-info',
                'value' => 'Belum diatur',
                'caption' => 'Isi tanggal jatuh tempo di bawah',
            ];
        }

        $due = Carbon::parse($dateStr)->startOfDay();
        $days = (int) now()->startOfDay()->diffInDays($due, false);
        $formatted = $due->translatedFormat('d M Y');

        if ($days < 0) {
            return [
                'label' => $label,
                'configured' => true,
                'due_date' => $dateStr,
                'days_remaining' => $days,
                'icon' => 'vx-bg-danger',
                'value' => $formatted,
                'caption' => 'Terlambat '.abs($days).' hari',
            ];
        }

        if ($days === 0) {
            return [
                'label' => $label,
                'configured' => true,
                'due_date' => $dateStr,
                'days_remaining' => 0,
                'icon' => 'vx-bg-danger',
                'value' => $formatted,
                'caption' => 'Jatuh tempo hari ini',
            ];
        }

        if ($days <= self::ALERT_DAYS_BEFORE) {
            return [
                'label' => $label,
                'configured' => true,
                'due_date' => $dateStr,
                'days_remaining' => $days,
                'icon' => $days <= 2 ? 'vx-bg-danger' : 'vx-bg-warning',
                'value' => $formatted,
                'caption' => 'Jatuh tempo dalam '.$days.' hari',
            ];
        }

        return [
            'label' => $label,
            'configured' => true,
            'due_date' => $dateStr,
            'days_remaining' => $days,
            'icon' => 'vx-bg-success',
            'value' => $formatted,
            'caption' => 'Aman · '.$days.' hari lagi',
        ];
    }
}

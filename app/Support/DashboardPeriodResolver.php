<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;

final class DashboardPeriodResolver
{
    /**
     * @return array{
     *     dari: Carbon,
     *     sampai: Carbon,
     *     mode: string,
     *     selectedDate: string,
     *     selectedMonth: string,
     *     selectedYear: int,
     *     selectedDari: string,
     *     selectedSampai: string
     * }
     */
    public static function fromRequest(Request $request, string $defaultMode = 'harian'): array
    {
        $mode = in_array($request->input('mode'), ['harian', 'bulanan', 'tahunan', 'range'], true)
            ? $request->input('mode')
            : (in_array($defaultMode, ['harian', 'bulanan', 'tahunan', 'range'], true) ? $defaultMode : 'harian');

        if ($request->filled('dari') && $request->filled('sampai') && ! $request->filled('mode')) {
            $mode = 'range';
        }

        $selectedDate = $request->input('tanggal') ?: now()->format('Y-m-d');
        $selectedMonth = $request->input('bulan') ?: now()->format('Y-m');
        $selectedYear = (int) ($request->input('tahun') ?: now()->year);
        $selectedDari = $request->input('dari') ?: now()->startOfMonth()->format('Y-m-d');
        $selectedSampai = $request->input('sampai') ?: now()->format('Y-m-d');

        if ($mode === 'range') {
            try {
                $dari = Carbon::parse($selectedDari)->startOfDay();
                $sampai = Carbon::parse($selectedSampai)->endOfDay();
            } catch (\Throwable $e) {
                $selectedDari = now()->startOfMonth()->format('Y-m-d');
                $selectedSampai = now()->format('Y-m-d');
                $dari = now()->startOfMonth()->startOfDay();
                $sampai = now()->endOfDay();
            }

            if ($dari->gt($sampai)) {
                [$dari, $sampai] = [$sampai->copy()->startOfDay(), $dari->copy()->endOfDay()];
                $selectedDari = $dari->format('Y-m-d');
                $selectedSampai = $sampai->format('Y-m-d');
            }

            return [
                'dari' => $dari,
                'sampai' => $sampai,
                'mode' => 'range',
                'selectedDate' => $selectedDari,
                'selectedMonth' => $dari->format('Y-m'),
                'selectedYear' => (int) $dari->format('Y'),
                'selectedDari' => $selectedDari,
                'selectedSampai' => $selectedSampai,
            ];
        }

        if ($mode === 'harian') {
            try {
                $dari = Carbon::parse($selectedDate)->startOfDay();
                $sampai = Carbon::parse($selectedDate)->endOfDay();
            } catch (\Throwable $e) {
                $selectedDate = now()->format('Y-m-d');
                $dari = now()->startOfDay();
                $sampai = now()->endOfDay();
            }
        } elseif ($mode === 'tahunan') {
            $selectedYear = max(2000, min(2100, $selectedYear));
            $dari = now()->setYear($selectedYear)->startOfYear();
            $sampai = now()->setYear($selectedYear)->endOfYear();
        } else {
            try {
                $monthBase = Carbon::parse($selectedMonth.'-01');
            } catch (\Throwable $e) {
                $selectedMonth = now()->format('Y-m');
                $monthBase = now()->startOfMonth();
            }
            $dari = $monthBase->copy()->startOfMonth();
            $sampai = $monthBase->copy()->endOfMonth();
        }

        return [
            'dari' => $dari,
            'sampai' => $sampai,
            'mode' => $mode,
            'selectedDate' => $selectedDate,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedDari' => $dari->format('Y-m-d'),
            'selectedSampai' => $sampai->format('Y-m-d'),
        ];
    }

    public static function title(array $period): string
    {
        return match ($period['mode']) {
            'harian' => 'Penjualan harian',
            'bulanan' => 'Penjualan bulanan',
            'tahunan' => 'Penjualan tahunan',
            'range' => 'Penjualan periode',
            default => 'Penjualan',
        };
    }

    public static function label(array $period): string
    {
        return match ($period['mode']) {
            'harian' => Carbon::parse($period['selectedDate'])->translatedFormat('l, d F Y'),
            'bulanan' => Carbon::parse($period['selectedMonth'].'-01')->translatedFormat('F Y'),
            'tahunan' => 'Tahun '.$period['selectedYear'],
            'range' => Carbon::parse($period['selectedDari'])->format('d/m/Y')
                .' – '
                .Carbon::parse($period['selectedSampai'])->format('d/m/Y'),
            default => '',
        };
    }

    /**
     * @return array<string, string|int>
     */
    public static function queryParams(array $period): array
    {
        $params = ['mode' => $period['mode']];

        return match ($period['mode']) {
            'harian' => array_merge($params, ['tanggal' => $period['selectedDate']]),
            'bulanan' => array_merge($params, ['bulan' => $period['selectedMonth']]),
            'tahunan' => array_merge($params, ['tahun' => $period['selectedYear']]),
            'range' => array_merge($params, [
                'dari' => $period['selectedDari'],
                'sampai' => $period['selectedSampai'],
            ]),
            default => $params,
        };
    }
}

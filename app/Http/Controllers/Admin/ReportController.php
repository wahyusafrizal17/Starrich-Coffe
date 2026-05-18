<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now()->endOfDay();

        $base = Transaction::paid()
            ->with(['user', 'details.product'])
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $sumTotal = (int) (clone $base)->sum('total');
        $transactions = (clone $base)->latest()->paginate(20)->withQueryString();

        return view('admin.reports.index', compact('transactions', 'sumTotal', 'from', 'to'));
    }

    public function export(Request $request): StreamedResponse
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now()->endOfDay();

        $filename = 'laporan-penjualan-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Tanggal', 'Kasir', 'Total', 'Bayar', 'Kembalian']);

            Transaction::paid()
                ->with('user')
                ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
                ->oldest()
                ->chunk(200, function ($rows) use ($out) {
                    foreach ($rows as $t) {
                        fputcsv($out, [
                            $t->id,
                            $t->created_at->format('Y-m-d H:i:s'),
                            $t->user?->name,
                            $t->total,
                            $t->bayar,
                            $t->kembalian,
                        ]);
                    }
                });

            fclose($out);
        }, 200, $headers);
    }

    public function profitLoss(Request $request): View
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now()->endOfDay();

        $rangeStart = $from->copy()->startOfDay();
        $rangeEnd = $to->copy()->endOfDay();

        $revenue = (int) Transaction::paid()
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->sum('total');

        $expensesByCategory = Expense::query()
            ->whereBetween('tanggal', [$rangeStart, $rangeEnd])
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        $categoriesMap = Expense::categories();
        $expenseLines = [];
        foreach ($categoriesMap as $key => $label) {
            $expenseLines[$key] = [
                'label' => $label,
                'total' => (int) ($expensesByCategory[$key] ?? 0),
            ];
        }

        $totalExpenses = array_sum(array_column($expenseLines, 'total'));

        $depreciationDetails = Asset::all()->map(function (Asset $asset) use ($rangeStart, $rangeEnd) {
            return [
                'asset' => $asset,
                'monthly' => $asset->monthlyDepreciation(),
                'period' => $asset->depreciationFor($rangeStart, $rangeEnd),
            ];
        });
        $totalDepreciation = (int) $depreciationDetails->sum('period');

        $operatingCost = $totalExpenses + $totalDepreciation;
        $netIncome = $revenue - $operatingCost;

        return view('admin.reports.profit-loss', [
            'from' => $from,
            'to' => $to,
            'revenue' => $revenue,
            'expenseLines' => $expenseLines,
            'totalExpenses' => $totalExpenses,
            'depreciationDetails' => $depreciationDetails,
            'totalDepreciation' => $totalDepreciation,
            'operatingCost' => $operatingCost,
            'netIncome' => $netIncome,
        ]);
    }
}

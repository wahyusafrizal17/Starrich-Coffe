<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\CapitalInflow;
use App\Models\Expense;
use App\Models\Transaction;
use App\Support\DashboardPeriodResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /** @var list<string> */
    private const COLUMN_FILTER_KEYS = [
        'filter_id',
        'filter_waktu',
        'filter_kasir',
        'filter_pembayaran',
        'filter_total',
    ];

    public function index(Request $request): View
    {
        $period = DashboardPeriodResolver::fromRequest($request, 'bulanan');
        $periodLabel = DashboardPeriodResolver::label($period);
        $filterQuery = DashboardPeriodResolver::queryParams($period);
        $columnFilters = $this->columnFiltersFromRequest($request);
        $exportQuery = array_merge($filterQuery, array_filter($columnFilters, fn (string $v) => $v !== ''));

        $rangeStart = $period['dari'];
        $rangeEnd = $period['sampai'];

        $base = Transaction::paid()
            ->with(['user', 'details.product'])
            ->whereBetween('created_at', [$rangeStart, $rangeEnd]);

        $this->applyColumnFilters($base, $columnFilters);

        $sumTotal = (int) (clone $base)->sum('total');
        $openBills = Transaction::open()
            ->with(['user', 'details.product'])
            ->latest()
            ->get();
        $openBillTotal = (int) $openBills->sum('total');
        $openBillCount = $openBills->count();
        $transactions = (clone $base)->latest()->paginate(20)->withQueryString();
        $hasActiveColumnFilters = collect($columnFilters)->contains(fn (string $v) => $v !== '');

        return view('admin.reports.index', compact(
            'transactions',
            'sumTotal',
            'openBillTotal',
            'openBillCount',
            'openBills',
            'period',
            'periodLabel',
            'filterQuery',
            'columnFilters',
            'exportQuery',
            'hasActiveColumnFilters',
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $period = DashboardPeriodResolver::fromRequest($request, 'bulanan');
        $from = $period['dari'];
        $to = $period['sampai'];
        $columnFilters = $this->columnFiltersFromRequest($request);

        $filename = 'laporan-penjualan-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->stream(function () use ($from, $to, $columnFilters) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Tanggal', 'Kasir', 'Pembayaran', 'Total', 'Bayar', 'Kembalian']);

            $query = Transaction::paid()
                ->with('user')
                ->whereBetween('created_at', [$from, $to]);

            $this->applyColumnFilters($query, $columnFilters);

            $query->oldest()
                ->chunk(200, function ($rows) use ($out) {
                    foreach ($rows as $t) {
                        fputcsv($out, [
                            $t->id,
                            $t->created_at->format('Y-m-d H:i:s'),
                            $t->cashierDisplayName(),
                            $t->paymentMethodLabel(),
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
        $period = DashboardPeriodResolver::fromRequest($request, 'bulanan');
        $periodLabel = DashboardPeriodResolver::label($period);
        $periodTitle = DashboardPeriodResolver::title($period);
        $filterQuery = DashboardPeriodResolver::queryParams($period);

        $rangeStart = $period['dari'];
        $rangeEnd = $period['sampai'];

        $salesRevenue = (int) Transaction::paid()
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->sum('total');

        $inflowsByCategory = CapitalInflow::query()
            ->whereBetween('tanggal', [$rangeStart, $rangeEnd])
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        $inflowCategoriesMap = CapitalInflow::categories();
        $inflowLines = [];
        foreach ($inflowCategoriesMap as $key => $label) {
            $inflowLines[$key] = [
                'label' => $label,
                'total' => (int) ($inflowsByCategory[$key] ?? 0),
            ];
        }

        $totalInflows = array_sum(array_column($inflowLines, 'total'));
        $totalRevenue = $salesRevenue + $totalInflows;

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
        $netIncome = $totalRevenue - $operatingCost;
        $profitShare30 = (int) round($netIncome * 0.30);

        return view('admin.reports.profit-loss', [
            'period' => $period,
            'periodLabel' => $periodLabel,
            'periodTitle' => $periodTitle,
            'filterQuery' => $filterQuery,
            'salesRevenue' => $salesRevenue,
            'inflowLines' => $inflowLines,
            'totalInflows' => $totalInflows,
            'totalRevenue' => $totalRevenue,
            'expenseLines' => $expenseLines,
            'totalExpenses' => $totalExpenses,
            'depreciationDetails' => $depreciationDetails,
            'totalDepreciation' => $totalDepreciation,
            'operatingCost' => $operatingCost,
            'netIncome' => $netIncome,
            'profitShare30' => $profitShare30,
        ]);
    }

    /** @return array<string, string> */
    private function columnFiltersFromRequest(Request $request): array
    {
        $filters = [];
        foreach (self::COLUMN_FILTER_KEYS as $key) {
            $filters[$key] = $request->string($key)->trim()->toString();
        }

        return $filters;
    }

    /** @param Builder<Transaction> $query */
    private function applyColumnFilters(Builder $query, array $filters): void
    {
        if ($filters['filter_id'] !== '') {
            $id = preg_replace('/\D/', '', $filters['filter_id']);
            if ($id !== '') {
                $query->where('id', 'like', '%'.$id.'%');
            }
        }

        if ($filters['filter_waktu'] !== '') {
            $query->whereDate('created_at', $filters['filter_waktu']);
        }

        if ($filters['filter_kasir'] !== '') {
            $needle = '%'.$filters['filter_kasir'].'%';
            $query->where(function (Builder $q) use ($needle) {
                $q->where('nama_kasir', 'like', $needle)
                    ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', $needle));
            });
        }

        if ($filters['filter_pembayaran'] !== '') {
            $query->where('metode_pembayaran', $filters['filter_pembayaran']);
        }

        if ($filters['filter_total'] !== '') {
            $digits = preg_replace('/\D/', '', $filters['filter_total']);
            if ($digits !== '') {
                $query->where('total', 'like', '%'.$digits.'%');
            }
        }
    }
}

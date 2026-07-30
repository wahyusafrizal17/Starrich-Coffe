<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Support\DashboardPeriodResolver;
use App\Support\PaymentMethodTotals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $period = DashboardPeriodResolver::fromRequest($request, 'harian');
        $periodTitle = DashboardPeriodResolver::title($period);
        $periodLabel = DashboardPeriodResolver::label($period);

        $periodTotal = (int) Transaction::paidRevenue()
            ->whereBetween('created_at', [$period['dari'], $period['sampai']])
            ->sum('total');

        $periodCount = Transaction::paidRevenue()
            ->whereBetween('created_at', [$period['dari'], $period['sampai']])
            ->count();

        $paymentTotals = PaymentMethodTotals::forRange($period['dari'], $period['sampai']);

        $paymentGrandTotal = $paymentTotals['cash'] + $paymentTotals['transfer'] + $paymentTotals['qris'];

        $averagePerTransaction = $periodCount > 0
            ? (int) round($periodTotal / $periodCount)
            : 0;

        $topProducts = TransactionDetail::query()
            ->select('product_id', DB::raw('SUM(qty) as qty_sold'))
            ->whereHas('transaction', function ($query) use ($period) {
                $query->paid()
                    ->whereBetween('created_at', [$period['dari'], $period['sampai']]);
            })
            ->groupBy('product_id')
            ->orderByDesc('qty_sold')
            ->limit(5)
            ->with('product.category')
            ->get();

        return view('dashboard', compact(
            'period',
            'periodTitle',
            'periodLabel',
            'periodTotal',
            'periodCount',
            'paymentTotals',
            'paymentGrandTotal',
            'averagePerTransaction',
            'topProducts',
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $todayTotal = (int) Transaction::whereDate('created_at', $today)->sum('total');
        $todayCount = Transaction::whereDate('created_at', $today)->count();

        $monthlyTotal = (int) Transaction::where('created_at', '>=', $monthStart)->sum('total');

        $topProducts = TransactionDetail::query()
            ->select('product_id', DB::raw('SUM(qty) as qty_sold'))
            ->groupBy('product_id')
            ->orderByDesc('qty_sold')
            ->limit(5)
            ->with('product.category')
            ->get();

        return view('dashboard', compact('todayTotal', 'todayCount', 'monthlyTotal', 'topProducts'));
    }
}

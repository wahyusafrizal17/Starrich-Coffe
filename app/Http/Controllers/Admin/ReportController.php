<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        $base = Transaction::with(['user', 'details.product'])
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

            Transaction::with('user')
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
}

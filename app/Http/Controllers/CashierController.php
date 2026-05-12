<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $products = Product::with('category')
            ->orderBy('nama_produk')
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'nama_produk' => $p->nama_produk,
                'harga' => $p->harga,
                'stok' => $p->stok,
                'kategori_id' => $p->kategori_id,
                'gambar' => $p->imageUrl(),
            ]);

        return view('cashier.index', compact('categories', 'products'));
    }

    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'payment_splits' => ['required', 'array', 'min:1'],
            'payment_splits.*.metode' => ['required', 'string', 'in:qris,transfer,cash'],
            'payment_splits.*.jumlah' => ['required', 'integer', 'min:0'],
        ]);

        $result = DB::transaction(function () use ($data, $request) {
            $total = 0;
            $prepared = [];

            foreach ($data['items'] as $line) {
                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->findOrFail($line['product_id']);

                if ($product->stok < $line['qty']) {
                    throw ValidationException::withMessages([
                        'items' => 'Stok tidak cukup untuk '.$product->nama_produk.' (tersisa '.$product->stok.').',
                    ]);
                }

                $subtotal = $product->harga * $line['qty'];
                $total += $subtotal;
                $prepared[] = [
                    'product' => $product,
                    'qty' => $line['qty'],
                    'subtotal' => $subtotal,
                ];
            }

            $splits = collect($data['payment_splits'])
                ->map(fn (array $row) => [
                    'metode' => $row['metode'],
                    'jumlah' => (int) $row['jumlah'],
                ])
                ->filter(fn (array $row) => $row['jumlah'] > 0)
                ->values()
                ->all();

            if ($splits === []) {
                throw ValidationException::withMessages([
                    'payment_splits' => 'Isi minimal satu nominal pembayaran di atas 0.',
                ]);
            }

            $bayar = (int) collect($splits)->sum('jumlah');

            if ($bayar < $total) {
                throw ValidationException::withMessages([
                    'payment_splits' => 'Total pembayaran kurang dari tagihan.',
                ]);
            }

            $kembalian = $bayar - $total;

            $metodeLabel = count($splits) > 1
                ? 'split'
                : $splits[0]['metode'];

            $transaction = Transaction::create([
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $metodeLabel,
                'payment_splits' => $splits,
                'user_id' => $request->user()->id,
            ]);

            foreach ($prepared as $row) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $row['product']->id,
                    'qty' => $row['qty'],
                    'harga' => $row['product']->harga,
                    'subtotal' => $row['subtotal'],
                ]);
                $row['product']->decrement('stok', $row['qty']);
            }

            return [
                'transaction_id' => $transaction->id,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
            ];
        });

        return response()->json([
            'ok' => true,
            'message' => 'Transaksi berhasil disimpan.',
            'data' => $result,
        ]);
    }
}

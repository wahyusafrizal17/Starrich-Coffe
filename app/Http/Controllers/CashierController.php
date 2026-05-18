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
                'kategori_id' => $p->kategori_id,
                'gambar' => $p->imageUrl(),
            ]);

        return view('cashier.index', [
            'categories' => $categories,
            'products' => $products,
            'openBillsCount' => $this->openBillsCount(),
        ]);
    }

    public function openBillsPage(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();

        $base = Transaction::query()
            ->open()
            ->with(['user', 'details.product'])
            ->latest();

        if ($q !== '') {
            $base->where(function ($qq) use ($q) {
                $qq->where('id', $q)
                    ->orWhere('nama_pelanggan', 'like', '%'.$q.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$q.'%'));
            });
        }

        $transactions = $base->get();
        $sumTotal = (int) $transactions->sum('total');
        $countBills = $transactions->count();

        return view('cashier.open-bills', [
            'transactions' => $transactions,
            'openBillsPayload' => $transactions->map(fn (Transaction $t) => $t->toOpenBillArray())->values()->all(),
            'sumTotal' => $sumTotal,
            'countBills' => $countBills,
            'q' => $q,
            'openBillsCount' => $this->openBillsCount(),
        ]);
    }

    public function invoice(Transaction $transaction): View
    {
        $transaction->load(['user', 'details.product']);

        return view('cashier.invoice', compact('transaction'));
    }

    public function history(Request $request): View
    {
        $from = $request->date('from');
        $to = $request->date('to');
        $q = $request->string('q')->trim()->toString();
        $status = $request->string('status')->trim()->toString();

        $base = Transaction::with(['user', 'details.product'])->latest();

        if ($from) {
            $base->where('created_at', '>=', $from->copy()->startOfDay());
        }
        if ($to) {
            $base->where('created_at', '<=', $to->copy()->endOfDay());
        }
        if ($q !== '') {
            $base->where(function ($qq) use ($q) {
                $qq->where('id', $q)
                    ->orWhere('nama_pelanggan', 'like', '%'.$q.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$q.'%'));
            });
        }
        if (in_array($status, [Transaction::STATUS_PAID, Transaction::STATUS_OPEN], true)) {
            $base->where('status', $status);
        }

        $sumTotal = (int) (clone $base)->sum('total');
        $countTrx = (clone $base)->count();
        $transactions = $base->paginate(20)->withQueryString();

        return view('cashier.history', [
            'transactions' => $transactions,
            'sumTotal' => $sumTotal,
            'countTrx' => $countTrx,
            'from' => $from,
            'to' => $to,
            'q' => $q,
            'status' => $status,
            'openBillsCount' => $this->openBillsCount(),
        ]);
    }

    public function openBills(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'open_bills' => $this->openBillsPayload(),
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'action' => ['nullable', 'string', 'in:pay,open_bill'],
            'order_type' => ['nullable', 'string', 'in:dine,take'],
            'nama_pelanggan' => ['required_if:action,open_bill', 'nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'payment_splits' => ['nullable', 'array'],
            'payment_splits.*.metode' => ['required_with:payment_splits', 'string', 'in:qris,transfer,cash'],
            'payment_splits.*.jumlah' => ['required_with:payment_splits', 'integer', 'min:0'],
        ]);

        $action = $data['action'] ?? 'pay';

        if ($action === 'open_bill') {
            return $this->storeOpenBill($request, $data);
        }

        return $this->storePaidCheckout($request, $data);
    }

    public function payOpenBill(Request $request, Transaction $transaction): JsonResponse
    {
        if (! $transaction->isOpen()) {
            return response()->json([
                'ok' => false,
                'message' => 'Tagihan ini sudah lunas atau tidak valid.',
            ], 422);
        }

        $data = $request->validate([
            'payment_splits' => ['required', 'array', 'min:1'],
            'payment_splits.*.metode' => ['required', 'string', 'in:qris,transfer,cash'],
            'payment_splits.*.jumlah' => ['required', 'integer', 'min:0'],
        ]);

        $total = (int) $transaction->total;

        $result = DB::transaction(function () use ($data, $transaction, $total) {
            $splits = $this->normalizePaymentSplits($data['payment_splits'] ?? []);
            $bayar = (int) collect($splits)->sum('jumlah');

            if ($bayar < $total) {
                throw ValidationException::withMessages([
                    'payment_splits' => 'Total pembayaran kurang dari tagihan.',
                ]);
            }

            $kembalian = $bayar - $total;
            $metodeLabel = count($splits) > 1 ? 'split' : $splits[0]['metode'];

            $transaction->update([
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $metodeLabel,
                'payment_splits' => $splits,
                'status' => Transaction::STATUS_PAID,
            ]);

            return [
                'transaction_id' => $transaction->id,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
            ];
        });

        return response()->json([
            'ok' => true,
            'message' => 'Pembayaran open bill berhasil.',
            'data' => $result,
            'open_bills' => $this->openBillsPayload(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function storeOpenBill(Request $request, array $data): JsonResponse
    {
        $result = DB::transaction(function () use ($data, $request) {
            [$total, $prepared] = $this->prepareLineItems($data['items']);

            $transaction = Transaction::create([
                'total' => $total,
                'bayar' => 0,
                'kembalian' => 0,
                'metode_pembayaran' => 'open_bill',
                'payment_splits' => null,
                'status' => Transaction::STATUS_OPEN,
                'order_type' => $data['order_type'] ?? null,
                'nama_pelanggan' => trim($data['nama_pelanggan'] ?? ''),
                'user_id' => $request->user()->id,
            ]);

            $this->createTransactionDetails($transaction, $prepared);

            return [
                'transaction_id' => $transaction->id,
                'total' => $total,
                'status' => Transaction::STATUS_OPEN,
            ];
        });

        return response()->json([
            'ok' => true,
            'message' => 'Open bill disimpan. Pelanggan bisa bayar nanti.',
            'data' => $result,
            'open_bills' => $this->openBillsPayload(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function storePaidCheckout(Request $request, array $data): JsonResponse
    {
        $result = DB::transaction(function () use ($data, $request) {
            [$total, $prepared] = $this->prepareLineItems($data['items']);

            $splits = $this->normalizePaymentSplits($data['payment_splits'] ?? []);
            $bayar = (int) collect($splits)->sum('jumlah');

            if ($bayar < $total) {
                throw ValidationException::withMessages([
                    'payment_splits' => 'Total pembayaran kurang dari tagihan.',
                ]);
            }

            $kembalian = $bayar - $total;
            $metodeLabel = count($splits) > 1 ? 'split' : $splits[0]['metode'];

            $transaction = Transaction::create([
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $metodeLabel,
                'payment_splits' => $splits,
                'status' => Transaction::STATUS_PAID,
                'order_type' => $data['order_type'] ?? null,
                'user_id' => $request->user()->id,
            ]);

            $this->createTransactionDetails($transaction, $prepared);

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
            'open_bills' => $this->openBillsPayload(),
        ]);
    }

    /**
     * @param  array<int, array{product_id: int, qty: int}>  $items
     * @return array{0: int, 1: list<array{product: Product, qty: int, subtotal: int}>}
     */
    private function prepareLineItems(array $items): array
    {
        $total = 0;
        $prepared = [];

        foreach ($items as $line) {
            /** @var Product $product */
            $product = Product::query()->findOrFail($line['product_id']);
            $subtotal = $product->harga * $line['qty'];
            $total += $subtotal;
            $prepared[] = [
                'product' => $product,
                'qty' => $line['qty'],
                'subtotal' => $subtotal,
            ];
        }

        return [$total, $prepared];
    }

    /**
     * @param  list<array{product: Product, qty: int, subtotal: int}>  $prepared
     */
    private function createTransactionDetails(Transaction $transaction, array $prepared): void
    {
        foreach ($prepared as $row) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $row['product']->id,
                'qty' => $row['qty'],
                'harga' => $row['product']->harga,
                'subtotal' => $row['subtotal'],
            ]);
        }
    }

    /**
     * @param  array<int, array{metode?: string, jumlah?: int}>  $rows
     * @return list<array{metode: string, jumlah: int}>
     */
    private function normalizePaymentSplits(array $rows): array
    {
        $splits = collect($rows)
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

        return $splits;
    }

    private function openBillsCount(): int
    {
        return Transaction::query()->open()->count();
    }

    /** @return list<array<string, mixed>> */
    private function openBillsPayload(): array
    {
        return Transaction::query()
            ->open()
            ->with('details.product')
            ->latest()
            ->get()
            ->map(fn (Transaction $t) => $t->toOpenBillArray())
            ->values()
            ->all();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Support\DiscountResolver;
use App\Support\OrderAddonCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function index(): View
    {
        $resolver = app(DiscountResolver::class)->warm();
        $categories = Category::orderBy('nama_kategori')->get();
        $products = Product::with('category')
            ->orderBy('nama_produk')
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'nama_produk' => $p->nama_produk,
                'harga' => $p->harga,
                'diskon' => $resolver->itemDiscountAmount($p),
                'harga_jual' => $resolver->hargaJual($p),
                'kategori_id' => $p->kategori_id,
                'gambar' => $p->imageUrl(),
                'suhu_pilihan' => $p->requiresSuhuPilihan(),
                'addon_pilihan' => $p->allowsOrderAddons(),
            ]);

        $addonsCatalog = collect(OrderAddonCatalog::definitions())
            ->map(fn (array $meta, string $code) => [
                'code' => $code,
                'label' => $meta['label'] ?? $code,
                'harga' => (int) ($meta['harga'] ?? 0),
            ])
            ->values()
            ->all();

        return view('cashier.index', [
            'categories' => $categories,
            'products' => $products,
            'addonsCatalog' => $addonsCatalog,
            'cartPromos' => $resolver->cartPromoCatalog(),
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
                    ->orWhere('nama_kasir', 'like', '%'.$q.'%')
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
                    ->orWhere('nama_kasir', 'like', '%'.$q.'%')
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

    public function updateTransactionPayment(Request $request, Transaction $transaction): JsonResponse
    {
        if (! $transaction->isPaid()) {
            return response()->json([
                'ok' => false,
                'message' => 'Hanya transaksi lunas yang bisa diubah metode pembayarannya.',
            ], 422);
        }

        $data = $request->validate([
            'metode' => ['nullable', 'string', 'in:cash,transfer,qris,karyawan'],
            'payment_splits' => ['nullable', 'array', 'min:1'],
            'payment_splits.*.metode' => ['required_with:payment_splits', 'string', 'in:cash,transfer,qris,karyawan'],
            'payment_splits.*.jumlah' => ['required_with:payment_splits', 'integer', 'min:0'],
        ]);

        if (empty($data['metode']) && empty($data['payment_splits'])) {
            throw ValidationException::withMessages([
                'metode' => 'Pilih metode pembayaran.',
            ]);
        }

        if (! empty($data['payment_splits'])) {
            $resolved = $this->resolvePaymentFromSplits($data['payment_splits'], (int) $transaction->bayar, true);
            $splits = $resolved['splits'];
            $metodeLabel = $resolved['metode'];
        } else {
            if (($data['metode'] ?? '') === Transaction::METHOD_KARYAWAN) {
                $splits = [['metode' => Transaction::METHOD_KARYAWAN, 'jumlah' => 0]];
                $metodeLabel = Transaction::METHOD_KARYAWAN;
                $transaction->update([
                    'bayar' => 0,
                    'kembalian' => 0,
                    'metode_pembayaran' => $metodeLabel,
                    'payment_splits' => $splits,
                ]);

                return response()->json([
                    'ok' => true,
                    'message' => 'Metode pembayaran diperbarui.',
                    'metode_pembayaran' => $metodeLabel,
                ]);
            }

            $existing = is_array($transaction->payment_splits) ? $transaction->payment_splits : [];

            if (count($existing) > 1) {
                throw ValidationException::withMessages([
                    'metode' => 'Transaksi split: ubah metode pada masing-masing pembayaran.',
                ]);
            }

            $jumlah = isset($existing[0]['jumlah'])
                ? (int) $existing[0]['jumlah']
                : (int) $transaction->bayar;

            $splits = [['metode' => $data['metode'], 'jumlah' => $jumlah]];
            $metodeLabel = $data['metode'];
        }

        $transaction->update([
            'metode_pembayaran' => $metodeLabel,
            'payment_splits' => $splits,
            ...(
                $metodeLabel === Transaction::METHOD_KARYAWAN
                    ? ['bayar' => 0, 'kembalian' => 0]
                    : []
            ),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Metode pembayaran diperbarui.',
            'metode_pembayaran' => $metodeLabel,
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
        $rules = [
            'action' => ['nullable', 'string', 'in:pay,open_bill'],
            'order_type' => ['nullable', 'string', 'in:dine,take'],
            'nama_pelanggan' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.suhu' => ['nullable', 'string', 'in:ice,hot'],
            'items.*.addons' => ['nullable', 'array'],
            'items.*.addons.*' => ['string', Rule::in(OrderAddonCatalog::validCodes())],
            'payment_splits' => ['nullable', 'array'],
            'payment_splits.*.metode' => ['required_with:payment_splits', 'string', 'in:qris,transfer,cash,karyawan'],
            'payment_splits.*.jumlah' => ['required_with:payment_splits', 'integer', 'min:0'],
            'diskon' => ['nullable', 'integer', 'min:0'],
        ];

        if ($request->user()->isAdmin()) {
            $rules['nama_kasir'] = ['required', 'string', 'max:100'];
        }

        $data = $request->validate($rules);

        $action = $data['action'] ?? 'pay';

        if ($action === 'open_bill') {
            if (trim((string) ($data['nama_pelanggan'] ?? '')) === '') {
                throw ValidationException::withMessages([
                    'nama_pelanggan' => 'Isi nama pelanggan untuk open bill.',
                ]);
            }

            return $this->storeOpenBill($request, $data);
        }

        return $this->storePaidCheckout($request, $data);
    }

    public function openBillEditData(Transaction $transaction): JsonResponse
    {
        if (! $transaction->isOpen()) {
            return response()->json([
                'ok' => false,
                'message' => 'Tagihan ini sudah lunas atau bukan open bill.',
            ], 422);
        }

        $transaction->load(['details.product']);

        $items = $transaction->details->map(function (TransactionDetail $d) {
            $product = $d->product;

            return [
                'product_id' => $d->product_id,
                'nama_produk' => $product?->nama_produk ?? '—',
                'harga' => (int) $d->harga,
                'qty' => (int) $d->qty,
                'gambar' => $product?->imageUrl(),
                'suhu' => $d->suhu,
                'addons' => $d->addons ?? [],
            ];
        })->values()->all();

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $transaction->id,
                'nama_pelanggan' => $transaction->nama_pelanggan,
                'nama_kasir' => $transaction->nama_kasir,
                'order_type' => $transaction->order_type ?? 'dine',
                'subtotal' => (int) ($transaction->subtotal ?: $transaction->total),
                'diskon' => (int) ($transaction->diskon ?? 0),
                'items' => $items,
            ],
        ]);
    }

    public function updateOpenBill(Request $request, Transaction $transaction): JsonResponse
    {
        if (! $transaction->isOpen()) {
            return response()->json([
                'ok' => false,
                'message' => 'Tagihan ini sudah lunas atau bukan open bill.',
            ], 422);
        }

        $rules = [
            'order_type' => ['nullable', 'string', 'in:dine,take'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.suhu' => ['nullable', 'string', 'in:ice,hot'],
            'items.*.addons' => ['nullable', 'array'],
            'items.*.addons.*' => ['string', Rule::in(OrderAddonCatalog::validCodes())],
            'diskon' => ['nullable', 'integer', 'min:0'],
        ];

        if ($request->user()->isAdmin()) {
            $rules['nama_kasir'] = ['required', 'string', 'max:100'];
        }

        $data = $request->validate($rules);

        $result = DB::transaction(function () use ($data, $transaction, $request) {
            [$subtotal, $prepared] = $this->prepareLineItems($data['items']);
            $amounts = $this->applyDiscount($subtotal, $data['diskon'] ?? 0);

            $transaction->details()->delete();
            $this->createTransactionDetails($transaction, $prepared);

            $updates = [
                'subtotal' => $amounts['subtotal'],
                'diskon' => $amounts['diskon'],
                'total' => $amounts['total'],
                'order_type' => $data['order_type'] ?? $transaction->order_type,
            ];

            if ($request->user()->isAdmin()) {
                $updates['nama_kasir'] = trim($data['nama_kasir']);
            }

            $transaction->update($updates);

            return [
                'transaction_id' => $transaction->id,
                'subtotal' => $amounts['subtotal'],
                'diskon' => $amounts['diskon'],
                'total' => $amounts['total'],
            ];
        });

        return response()->json([
            'ok' => true,
            'message' => 'Open bill diperbarui.',
            'data' => $result,
            'open_bills' => $this->openBillsPayload(),
        ]);
    }

    public function destroyOpenBill(Transaction $transaction): JsonResponse
    {
        if (! $transaction->isOpen()) {
            return response()->json([
                'ok' => false,
                'message' => 'Tagihan ini sudah lunas atau bukan open bill.',
            ], 422);
        }

        DB::transaction(function () use ($transaction) {
            $transaction->delete();
        });

        return response()->json([
            'ok' => true,
            'message' => 'Open bill dihapus.',
            'open_bills' => $this->openBillsPayload(),
        ]);
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
            'payment_splits.*.metode' => ['required', 'string', 'in:qris,transfer,cash,karyawan'],
            'payment_splits.*.jumlah' => ['required', 'integer', 'min:0'],
            'nama_pelanggan' => ['nullable', 'string', 'max:100'],
        ]);

        $total = (int) $transaction->total;

        $result = DB::transaction(function () use ($data, $transaction, $total) {
            $resolved = $this->resolvePaymentFromSplits($data['payment_splits'] ?? [], $total);

            if ($resolved['metode'] === Transaction::METHOD_KARYAWAN) {
                $nama = trim((string) ($data['nama_pelanggan'] ?? $transaction->nama_pelanggan ?? ''));
                if ($nama === '') {
                    throw ValidationException::withMessages([
                        'nama_pelanggan' => 'Isi nama karyawan untuk pencatatan.',
                    ]);
                }
            }

            $updates = [
                'bayar' => $resolved['bayar'],
                'kembalian' => $resolved['kembalian'],
                'metode_pembayaran' => $resolved['metode'],
                'payment_splits' => $resolved['splits'],
                'status' => Transaction::STATUS_PAID,
            ];

            if ($resolved['metode'] === Transaction::METHOD_KARYAWAN) {
                $updates['nama_pelanggan'] = trim((string) ($data['nama_pelanggan'] ?? $transaction->nama_pelanggan));
            }

            $transaction->update($updates);

            return [
                'transaction_id' => $transaction->id,
                'total' => $total,
                'bayar' => $resolved['bayar'],
                'kembalian' => $resolved['kembalian'],
            ];
        });

        return response()->json([
            'ok' => true,
            'message' => ($result['bayar'] === 0 && $transaction->fresh()->isKaryawan())
                ? 'Pesanan karyawan dicatat.'
                : 'Pembayaran open bill berhasil.',
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
            [$subtotal, $prepared] = $this->prepareLineItems($data['items']);
            $amounts = $this->applyDiscount($subtotal, $data['diskon'] ?? 0);

            $transaction = Transaction::create([
                'subtotal' => $amounts['subtotal'],
                'diskon' => $amounts['diskon'],
                'total' => $amounts['total'],
                'bayar' => 0,
                'kembalian' => 0,
                'metode_pembayaran' => 'open_bill',
                'payment_splits' => null,
                'status' => Transaction::STATUS_OPEN,
                'order_type' => $data['order_type'] ?? null,
                'nama_pelanggan' => trim($data['nama_pelanggan'] ?? ''),
                'nama_kasir' => $this->resolvedCashierName($request, $data),
                'user_id' => $request->user()->id,
            ]);

            $this->createTransactionDetails($transaction, $prepared);

            return [
                'transaction_id' => $transaction->id,
                'subtotal' => $amounts['subtotal'],
                'diskon' => $amounts['diskon'],
                'total' => $amounts['total'],
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
            [$subtotal, $prepared] = $this->prepareLineItems($data['items']);
            $amounts = $this->applyDiscount($subtotal, $data['diskon'] ?? 0);

            $resolved = $this->resolvePaymentFromSplits($data['payment_splits'] ?? [], $amounts['total']);

            $namaPelanggan = trim((string) ($data['nama_pelanggan'] ?? ''));
            if ($resolved['metode'] === Transaction::METHOD_KARYAWAN && $namaPelanggan === '') {
                throw ValidationException::withMessages([
                    'nama_pelanggan' => 'Isi nama karyawan untuk pencatatan.',
                ]);
            }

            $transaction = Transaction::create([
                'subtotal' => $amounts['subtotal'],
                'diskon' => $amounts['diskon'],
                'total' => $amounts['total'],
                'bayar' => $resolved['bayar'],
                'kembalian' => $resolved['kembalian'],
                'metode_pembayaran' => $resolved['metode'],
                'payment_splits' => $resolved['splits'],
                'status' => Transaction::STATUS_PAID,
                'order_type' => $data['order_type'] ?? null,
                'nama_pelanggan' => $namaPelanggan !== '' ? $namaPelanggan : null,
                'nama_kasir' => $this->resolvedCashierName($request, $data),
                'user_id' => $request->user()->id,
            ]);

            $this->createTransactionDetails($transaction, $prepared);

            return [
                'transaction_id' => $transaction->id,
                'subtotal' => $amounts['subtotal'],
                'diskon' => $amounts['diskon'],
                'total' => $amounts['total'],
                'bayar' => $resolved['bayar'],
                'kembalian' => $resolved['kembalian'],
                'metode' => $resolved['metode'],
            ];
        });

        $isKaryawan = ($result['metode'] ?? '') === Transaction::METHOD_KARYAWAN;

        return response()->json([
            'ok' => true,
            'message' => $isKaryawan ? 'Pesanan karyawan dicatat.' : 'Transaksi berhasil disimpan.',
            'data' => $result,
            'open_bills' => $this->openBillsPayload(),
        ]);
    }

    /**
     * @return array{subtotal: int, diskon: int, total: int}
     */
    private function applyDiscount(int $subtotal, mixed $diskonInput): array
    {
        $diskon = max(0, (int) $diskonInput);
        if ($diskon > $subtotal) {
            throw ValidationException::withMessages([
                'diskon' => 'Diskon tidak boleh melebihi subtotal.',
            ]);
        }

        return [
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'total' => $subtotal - $diskon,
        ];
    }

    /**
     * @param  array<int, array{product_id: int, qty: int, suhu?: string|null, addons?: array<int, string>|null}>  $items
     * @return array{0: int, 1: list<array{product: Product, qty: int, subtotal: int, suhu: string|null, addons: list<string>, unit_harga: int}>}
     */
    private function prepareLineItems(array $items): array
    {
        app(DiscountResolver::class)->warm();

        $total = 0;
        $prepared = [];

        foreach ($items as $index => $line) {
            /** @var Product $product */
            $product = Product::query()->with(['category'])->findOrFail($line['product_id']);
            $resolver = app(DiscountResolver::class);

            $addonsNorm = OrderAddonCatalog::normalize($line['addons'] ?? null);
            if (! $product->allowsOrderAddons()) {
                $addonsNorm = [];
            }

            $addonExtra = OrderAddonCatalog::extraPriceForCodes($addonsNorm);
            $unitHarga = $resolver->hargaJual($product) + $addonExtra;
            $subtotal = $unitHarga * $line['qty'];
            $total += $subtotal;

            $raw = $line['suhu'] ?? null;
            $suhu = is_string($raw) && in_array($raw, ['ice', 'hot'], true) ? $raw : null;
            if (! $product->requiresSuhuPilihan()) {
                $suhu = null;
            } elseif ($suhu === null) {
                throw ValidationException::withMessages([
                    "items.{$index}.suhu" => 'Pilih Ice atau Hot untuk menu ini.',
                ]);
            }

            $prepared[] = [
                'product' => $product,
                'qty' => $line['qty'],
                'subtotal' => $subtotal,
                'suhu' => $suhu,
                'addons' => $addonsNorm,
                'unit_harga' => $unitHarga,
            ];
        }

        return [$total, $prepared];
    }

    /**
     * @param  list<array{product: Product, qty: int, subtotal: int, suhu: string|null, addons: list<string>, unit_harga: int}>  $prepared
     */
    private function createTransactionDetails(Transaction $transaction, array $prepared): void
    {
        foreach ($prepared as $row) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $row['product']->id,
                'suhu' => $row['suhu'],
                'addons' => $row['addons'] === [] ? null : $row['addons'],
                'qty' => $row['qty'],
                'harga' => $row['unit_harga'],
                'subtotal' => $row['subtotal'],
            ]);
        }
    }

    /**
     * @param  array<int, array{metode?: string, jumlah?: int}>  $rows
     * @return array{metode: string, splits: list<array{metode: string, jumlah: int}>, bayar: int, kembalian: int}
     */
    private function resolvePaymentFromSplits(array $rows, int $total, bool $exactMatch = false): array
    {
        $normalizedRows = collect($rows)
            ->map(fn (array $row) => [
                'metode' => (string) ($row['metode'] ?? ''),
                'jumlah' => (int) ($row['jumlah'] ?? 0),
            ])
            ->values()
            ->all();

        $karyawanRows = array_values(array_filter(
            $normalizedRows,
            fn (array $row) => $row['metode'] === Transaction::METHOD_KARYAWAN
        ));

        if ($karyawanRows !== []) {
            if (count($normalizedRows) !== 1) {
                throw ValidationException::withMessages([
                    'payment_splits' => 'Metode Karyawan tidak bisa digabung dengan pembayaran lain.',
                ]);
            }

            return [
                'metode' => Transaction::METHOD_KARYAWAN,
                'splits' => [['metode' => Transaction::METHOD_KARYAWAN, 'jumlah' => 0]],
                'bayar' => 0,
                'kembalian' => 0,
            ];
        }

        $splits = $this->normalizePaymentSplits($normalizedRows);
        $bayar = (int) collect($splits)->sum('jumlah');

        if ($exactMatch) {
            if ($bayar !== $total) {
                throw ValidationException::withMessages([
                    'payment_splits' => 'Total pembayaran harus sama dengan nominal bayar transaksi.',
                ]);
            }
        } elseif ($bayar < $total) {
            throw ValidationException::withMessages([
                'payment_splits' => 'Total pembayaran kurang dari tagihan.',
            ]);
        }

        return [
            'metode' => count($splits) > 1 ? 'split' : $splits[0]['metode'],
            'splits' => $splits,
            'bayar' => $bayar,
            'kembalian' => max(0, $bayar - $total),
        ];
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
            ->filter(fn (array $row) => $row['jumlah'] > 0 && $row['metode'] !== Transaction::METHOD_KARYAWAN)
            ->values()
            ->all();

        if ($splits === []) {
            throw ValidationException::withMessages([
                'payment_splits' => 'Isi minimal satu nominal pembayaran di atas 0.',
            ]);
        }

        return $splits;
    }

    /** @param  array<string, mixed>  $data */
    private function resolvedCashierName(Request $request, array $data): ?string
    {
        if (! $request->user()->isAdmin()) {
            return null;
        }

        return trim((string) ($data['nama_kasir'] ?? ''));
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

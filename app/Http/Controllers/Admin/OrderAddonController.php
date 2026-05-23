<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderAddon;
use App\Models\TransactionDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderAddonController extends Controller
{
    public function index(): View
    {
        $addons = OrderAddon::query()->ordered()->paginate(20);

        return view('admin.order-addons.index', compact('addons'));
    }

    public function create(): View
    {
        return view('admin.order-addons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['kode'] = $this->resolveKode($request, null);
        OrderAddon::create($data);

        return redirect()->route('admin.order-addons.index')->with('success', 'Tambahan pesanan disimpan.');
    }

    public function edit(OrderAddon $orderAddon): View
    {
        return view('admin.order-addons.edit', compact('orderAddon'));
    }

    public function update(Request $request, OrderAddon $orderAddon): RedirectResponse
    {
        $data = $this->validated($request, $orderAddon);
        $orderAddon->update($data);

        return redirect()->route('admin.order-addons.index')->with('success', 'Tambahan pesanan diperbarui.');
    }

    public function destroy(OrderAddon $orderAddon): RedirectResponse
    {
        if ($this->isUsedInTransactions($orderAddon->kode)) {
            return back()->with('error', 'Tambahan sudah dipakai di transaksi. Nonaktifkan saja, jangan dihapus.');
        }

        $orderAddon->delete();

        return redirect()->route('admin.order-addons.index')->with('success', 'Tambahan pesanan dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?OrderAddon $existing = null): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'kode' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('order_addons', 'kode')->ignore($existing?->id),
            ],
        ]);

        $data['urutan'] = (int) ($data['urutan'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        unset($data['kode']);

        return $data;
    }

    private function resolveKode(Request $request, ?OrderAddon $existing): string
    {
        if ($existing) {
            return $existing->kode;
        }

        $raw = trim((string) $request->input('kode', ''));
        if ($raw !== '') {
            return Str::lower($raw);
        }

        $base = Str::slug((string) $request->input('label'), '_');
        $kode = $base !== '' ? $base : 'addon';
        $suffix = 1;
        $candidate = $kode;
        while (OrderAddon::query()->where('kode', $candidate)->exists()) {
            $candidate = $kode.'_'.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function isUsedInTransactions(string $kode): bool
    {
        return TransactionDetail::query()
            ->whereNotNull('addons')
            ->whereJsonContains('addons', $kode)
            ->exists();
    }
}

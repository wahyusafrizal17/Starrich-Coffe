<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderAddon;
use App\Models\TransactionDetail;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderAddonController extends Controller
{
    public function index(Request $request): View
    {
        $columnFilters = [
            'filter_nama' => $request->string('filter_nama')->trim()->toString(),
            'filter_status' => $request->string('filter_status')->trim()->toString(),
        ];

        $query = OrderAddon::query()->ordered();

        if ($columnFilters['filter_nama'] !== '') {
            $needle = '%'.$columnFilters['filter_nama'].'%';
            $query->where(function ($q) use ($needle) {
                $q->where('label', 'like', $needle)
                    ->orWhere('kode', 'like', $needle);
            });
        }

        if ($columnFilters['filter_status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($columnFilters['filter_status'] === 'inactive') {
            $query->where('is_active', false);
        }

        $filteredTotal = (clone $query)->count();
        $addons = $query->paginate(20)->withQueryString();
        $catalogTotal = OrderAddon::count();
        $activeCount = OrderAddon::query()->where('is_active', true)->count();
        $hasActiveFilters = collect($columnFilters)->contains(fn (string $v) => $v !== '');

        $openModal = session('_modal');
        $modalAddonId = session('_addon_id');
        $modalAddon = $modalAddonId ? OrderAddon::query()->find($modalAddonId) : null;
        $usedKodes = $this->collectUsedAddonKodes();

        return view('admin.order-addons.index', compact(
            'addons',
            'columnFilters',
            'filteredTotal',
            'catalogTotal',
            'activeCount',
            'hasActiveFilters',
            'openModal',
            'modalAddon',
            'usedKodes',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = $this->makeValidator($request);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'create', null, $validator);
        }

        $data = $this->prepareValidatedData($request, $validator->validated());
        $data['kode'] = $this->resolveKode($request, null);
        OrderAddon::create($data);

        return redirect()
            ->route('admin.order-addons.index', $this->listQueryParams($request))
            ->with('success', 'Tambahan pesanan disimpan.');
    }

    public function update(Request $request, OrderAddon $orderAddon): RedirectResponse
    {
        $validator = $this->makeValidator($request, $orderAddon);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'edit', $orderAddon->id, $validator);
        }

        $orderAddon->update($this->prepareValidatedData($request, $validator->validated()));

        return redirect()
            ->route('admin.order-addons.index', $this->listQueryParams($request))
            ->with('success', 'Tambahan pesanan diperbarui.');
    }

    public function destroy(Request $request, OrderAddon $orderAddon): RedirectResponse
    {
        if ($this->isUsedInTransactions($orderAddon->kode)) {
            return back()->with('error', 'Tambahan sudah dipakai di transaksi. Nonaktifkan saja, jangan dihapus.');
        }

        $orderAddon->delete();

        return redirect()
            ->route('admin.order-addons.index', $this->listQueryParams($request))
            ->with('success', 'Tambahan pesanan dihapus.');
    }

    private function makeValidator(Request $request, ?OrderAddon $existing = null): Validator
    {
        return ValidatorFacade::make($request->all(), [
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
    }

    /** @param  array<string, mixed>  $data */
    private function prepareValidatedData(Request $request, array $data): array
    {
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

    /** @return list<string> */
    private function collectUsedAddonKodes(): array
    {
        $used = [];

        TransactionDetail::query()
            ->whereNotNull('addons')
            ->select('addons')
            ->cursor()
            ->each(function (TransactionDetail $detail) use (&$used) {
                foreach ((array) $detail->addons as $kode) {
                    if (is_string($kode) && $kode !== '') {
                        $used[$kode] = true;
                    }
                }
            });

        return array_keys($used);
    }

    private function isUsedInTransactions(string $kode): bool
    {
        return TransactionDetail::query()
            ->whereNotNull('addons')
            ->whereJsonContains('addons', $kode)
            ->exists();
    }

    /**
     * @return array<string, mixed>
     */
    private function listQueryParams(Request $request): array
    {
        return array_filter([
            'filter_nama' => $request->input('filter_nama'),
            'filter_status' => $request->input('filter_status'),
            'page' => $request->input('page'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function redirectWithModalError(
        Request $request,
        string $modal,
        ?int $addonId,
        Validator $validator,
    ): RedirectResponse {
        $redirect = redirect()
            ->route('admin.order-addons.index', $this->listQueryParams($request))
            ->withInput()
            ->with('_modal', $modal)
            ->withErrors($validator);

        if ($addonId !== null) {
            $redirect->with('_addon_id', $addonId);
        }

        return $redirect;
    }
}

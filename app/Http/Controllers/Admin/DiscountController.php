<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function index(Request $request): View
    {
        $columnFilters = [
            'filter_nama' => $request->string('filter_nama')->trim()->toString(),
            'filter_jenis' => $request->string('filter_jenis')->trim()->toString(),
            'filter_status' => $request->string('filter_status')->trim()->toString(),
        ];

        $query = Discount::query()
            ->with(['product.category', 'category'])
            ->latest();

        if ($columnFilters['filter_nama'] !== '') {
            $needle = '%'.$columnFilters['filter_nama'].'%';
            $query->where(function ($q) use ($needle) {
                $q->where('nama', 'like', $needle)
                    ->orWhere('catatan', 'like', $needle)
                    ->orWhereHas('product', fn ($p) => $p->where('nama_produk', 'like', $needle))
                    ->orWhereHas('category', fn ($c) => $c->where('nama_kategori', 'like', $needle));
            });
        }

        if ($columnFilters['filter_jenis'] !== '' && isset(Discount::JENIS_LABELS[$columnFilters['filter_jenis']])) {
            $query->where('jenis', $columnFilters['filter_jenis']);
        }

        if ($columnFilters['filter_status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($columnFilters['filter_status'] === 'inactive') {
            $query->where('is_active', false);
        }

        $filteredTotal = (clone $query)->count();
        $discounts = $query->paginate(15)->withQueryString();
        $catalogTotal = Discount::count();
        $activeCount = Discount::query()->where('is_active', true)->count();
        $hasActiveFilters = collect($columnFilters)->contains(fn (string $v) => $v !== '');

        $openModal = session('_modal');
        $modalDiscountId = session('_discount_id');
        $modalDiscount = $modalDiscountId
            ? Discount::with(['product', 'category'])->find($modalDiscountId)
            : null;

        $products = Product::query()
            ->with(['discount'])
            ->orderBy('nama_produk')
            ->get();
        $categories = Category::query()->orderBy('nama_kategori')->get();

        return view('admin.discounts.index', compact(
            'discounts',
            'products',
            'categories',
            'columnFilters',
            'filteredTotal',
            'catalogTotal',
            'activeCount',
            'hasActiveFilters',
            'openModal',
            'modalDiscount',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = $this->makeValidator($request);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'create', null, $validator);
        }

        Discount::create($this->prepareValidatedData($validator->validated()));

        return redirect()
            ->route('admin.discounts.index', $this->listQueryParams($request))
            ->with('success', 'Diskon disimpan.');
    }

    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $validator = $this->makeValidator($request, $discount);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'edit', $discount->id, $validator);
        }

        $discount->update($this->prepareValidatedData($validator->validated()));

        return redirect()
            ->route('admin.discounts.index', $this->listQueryParams($request))
            ->with('success', 'Diskon diperbarui.');
    }

    public function destroy(Request $request, Discount $discount): RedirectResponse
    {
        $discount->delete();

        return redirect()
            ->route('admin.discounts.index', $this->listQueryParams($request))
            ->with('success', 'Diskon dihapus.');
    }

    private function makeValidator(Request $request, ?Discount $discount = null): Validator
    {
        $jenis = (string) $request->input('jenis', Discount::JENIS_PRODUCT);

        $rules = [
            'nama' => ['required', 'string', 'max:120'],
            'jenis' => ['required', Rule::in(array_keys(Discount::JENIS_LABELS))],
            'tipe_nilai' => ['required', Rule::in([Discount::TIPE_AMOUNT, Discount::TIPE_PERCENT])],
            'jumlah' => ['required', 'integer', 'min:1'],
            'product_id' => ['nullable', 'exists:products,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'min_belanja' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'jam_mulai' => ['nullable', 'date_format:H:i'],
            'jam_selesai' => ['nullable', 'date_format:H:i'],
            'hari_aktif' => ['nullable', 'array'],
            'hari_aktif.*' => ['integer', 'between:0,6'],
            'is_active' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];

        if ($request->input('tipe_nilai') === Discount::TIPE_PERCENT) {
            $rules['jumlah'][] = 'max:100';
        }

        if ($jenis === Discount::JENIS_PRODUCT) {
            $rules['product_id'] = [
                'required',
                'exists:products,id',
                Rule::unique('discounts', 'product_id')
                    ->where(fn ($q) => $q->where('jenis', Discount::JENIS_PRODUCT))
                    ->ignore($discount?->id),
            ];
        } elseif ($jenis === Discount::JENIS_CATEGORY) {
            $rules['category_id'] = ['required', 'exists:categories,id'];
        } elseif ($jenis === Discount::JENIS_MIN_PURCHASE) {
            $rules['min_belanja'] = ['required', 'integer', 'min:1'];
        } elseif ($jenis === Discount::JENIS_EVENT) {
            $rules['starts_at'] = ['required', 'date'];
            $rules['ends_at'] = ['required', 'date', 'after_or_equal:starts_at'];
        } elseif ($jenis === Discount::JENIS_HAPPY_HOUR) {
            $rules['jam_mulai'] = ['required', 'date_format:H:i'];
            $rules['jam_selesai'] = ['required', 'date_format:H:i'];
        }

        return ValidatorFacade::make($request->all(), $rules, [
            'product_id.unique' => 'Produk ini sudah punya diskon produk. Edit yang sudah ada.',
            'jumlah.max' => 'Persentase maksimal 100%.',
        ])->after(function (Validator $validator) use ($request, $jenis) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $tipe = $request->input('tipe_nilai');
            $jumlah = (int) $request->input('jumlah');
            $productId = (int) $request->input('product_id');

            if ($jenis === Discount::JENIS_PRODUCT && $tipe === Discount::TIPE_AMOUNT && $productId > 0) {
                $harga = (int) Product::query()->whereKey($productId)->value('harga');
                if ($harga > 0 && $jumlah > $harga) {
                    $validator->errors()->add(
                        'jumlah',
                        'Diskon tidak boleh melebihi harga produk ('.number_format($harga, 0, ',', '.').').'
                    );
                }
            }

            if (in_array($jenis, [Discount::JENIS_EVENT, Discount::JENIS_HAPPY_HOUR], true)) {
                $hasProduct = filled($request->input('product_id'));
                $hasCategory = filled($request->input('category_id'));
                if ($hasProduct && $hasCategory) {
                    $validator->errors()->add(
                        'product_id',
                        'Pilih produk atau kategori saja, jangan keduanya.'
                    );
                }
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareValidatedData(array $data): array
    {
        $jenis = $data['jenis'];
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['catatan'] = isset($data['catatan']) && trim((string) $data['catatan']) !== ''
            ? trim((string) $data['catatan'])
            : null;
        $data['hari_aktif'] = ! empty($data['hari_aktif'])
            ? array_values(array_unique(array_map('intval', $data['hari_aktif'])))
            : null;

        $data['product_id'] = ! empty($data['product_id']) ? (int) $data['product_id'] : null;
        $data['category_id'] = ! empty($data['category_id']) ? (int) $data['category_id'] : null;
        $data['min_belanja'] = isset($data['min_belanja']) && $data['min_belanja'] !== ''
            ? (int) $data['min_belanja']
            : null;

        if ($jenis === Discount::JENIS_PRODUCT) {
            $data['category_id'] = null;
            $data['min_belanja'] = null;
            $data['jam_mulai'] = null;
            $data['jam_selesai'] = null;
            $data['hari_aktif'] = null;
        } elseif ($jenis === Discount::JENIS_CATEGORY) {
            $data['product_id'] = null;
            $data['min_belanja'] = null;
            $data['jam_mulai'] = null;
            $data['jam_selesai'] = null;
            $data['hari_aktif'] = null;
        } elseif ($jenis === Discount::JENIS_MIN_PURCHASE) {
            $data['product_id'] = null;
            $data['category_id'] = null;
            $data['jam_mulai'] = null;
            $data['jam_selesai'] = null;
            $data['hari_aktif'] = null;
        } elseif ($jenis === Discount::JENIS_EVENT) {
            $data['jam_mulai'] = null;
            $data['jam_selesai'] = null;
            $data['hari_aktif'] = null;
        } elseif ($jenis === Discount::JENIS_HAPPY_HOUR) {
            // keep jam + optional scope + optional campaign dates
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function listQueryParams(Request $request): array
    {
        return array_filter([
            'filter_nama' => $request->input('filter_nama'),
            'filter_jenis' => $request->input('filter_jenis'),
            'filter_status' => $request->input('filter_status'),
            'page' => $request->input('page'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function redirectWithModalError(
        Request $request,
        string $modal,
        ?int $discountId,
        Validator $validator,
    ): RedirectResponse {
        $redirect = redirect()
            ->route('admin.discounts.index', $this->listQueryParams($request))
            ->withInput()
            ->with('_modal', $modal)
            ->withErrors($validator);

        if ($discountId !== null) {
            $redirect->with('_discount_id', $discountId);
        }

        return $redirect;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $columnFilters = [
            'filter_nama' => $request->string('filter_nama')->trim()->toString(),
            'filter_kategori' => $request->string('filter_kategori')->trim()->toString(),
        ];

        $query = Product::with('category')->orderBy('nama_produk');

        if ($columnFilters['filter_kategori'] !== '') {
            $query->where('kategori_id', (int) $columnFilters['filter_kategori']);
        }
        if ($columnFilters['filter_nama'] !== '') {
            $query->where('nama_produk', 'like', '%'.$columnFilters['filter_nama'].'%');
        }

        $filteredTotal = (clone $query)->count();
        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();
        $catalogTotal = Product::count();
        $categoryCount = $categories->count();
        $hasActiveFilters = collect($columnFilters)->contains(fn (string $v) => $v !== '');

        $openModal = session('_modal');
        $modalProductId = session('_product_id');
        $modalProduct = $modalProductId
            ? Product::with('category')->find($modalProductId)
            : null;

        return view('admin.products.index', compact(
            'products',
            'categories',
            'columnFilters',
            'filteredTotal',
            'catalogTotal',
            'categoryCount',
            'hasActiveFilters',
            'openModal',
            'modalProduct',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'kategori_id' => ['required', 'exists:categories,id'],
            'gambar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'create', null, $validator);
        }

        $data = $validator->validated();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('products', 'uploads');
        } else {
            unset($data['gambar']);
        }

        Product::create($data);

        return redirect()
            ->route('admin.products.index', $this->listQueryParams($request))
            ->with('success', 'Produk disimpan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'kategori_id' => ['required', 'exists:categories,id'],
            'gambar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'edit', $product->id, $validator);
        }

        $data = $validator->validated();

        if ($request->hasFile('gambar')) {
            if ($product->gambar) {
                Storage::disk('uploads')->delete($product->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('products', 'uploads');
        } else {
            unset($data['gambar']);
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index', $this->listQueryParams($request))
            ->with('success', 'Produk diperbarui.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        if ($product->transactionDetails()->exists()) {
            return back()->with(
                'error',
                'Produk tidak dapat dihapus karena sudah pernah terjual (ada di riwayat transaksi). Laporan penjualan harus tetap konsisten. Anda bisa mengubah nama atau harga produk jika menu tidak lagi dijual.'
            );
        }

        $gambarPath = $product->gambar;

        try {
            $product->delete();
        } catch (QueryException $e) {
            if (str_contains($e->message(), 'Integrity constraint') || str_contains($e->message(), '1451')) {
                return back()->with(
                    'error',
                    'Produk tidak dapat dihapus karena masih terhubung ke data transaksi. Hapus tidak diizinkan agar laporan tetap akurat.'
                );
            }

            throw $e;
        }

        if ($gambarPath) {
            Storage::disk('uploads')->delete($gambarPath);
        }

        return redirect()
            ->route('admin.products.index', $this->listQueryParams($request))
            ->with('success', 'Produk dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function listQueryParams(Request $request): array
    {
        return array_filter([
            'filter_nama' => $request->input('filter_nama'),
            'filter_kategori' => $request->input('filter_kategori'),
            'page' => $request->input('page'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function redirectWithModalError(
        Request $request,
        string $modal,
        ?int $productId,
        \Illuminate\Contracts\Validation\Validator $validator,
    ): RedirectResponse {
        $redirect = redirect()
            ->route('admin.products.index', $this->listQueryParams($request))
            ->withInput()
            ->with('_modal', $modal)
            ->withErrors($validator);

        if ($productId !== null) {
            $redirect->with('_product_id', $productId);
        }

        return $redirect;
    }
}

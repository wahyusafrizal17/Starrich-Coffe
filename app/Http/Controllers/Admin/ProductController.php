<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->orderBy('nama_produk');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->integer('kategori_id'));
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where('nama_produk', 'like', '%'.$q.'%');
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'kategori_id' => ['required', 'exists:categories,id'],
            'gambar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('products', 'uploads');
        } else {
            unset($data['gambar']);
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk disimpan.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'kategori_id' => ['required', 'exists:categories,id'],
            'gambar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('gambar')) {
            if ($product->gambar) {
                Storage::disk('uploads')->delete($product->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('products', 'uploads');
        } else {
            unset($data['gambar']);
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->gambar) {
            Storage::disk('uploads')->delete($product->gambar);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk dihapus.');
    }
}

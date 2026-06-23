<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $columnFilters = [
            'filter_nama' => $request->string('filter_nama')->trim()->toString(),
        ];

        $query = Category::withCount('products')->orderBy('nama_kategori');

        if ($columnFilters['filter_nama'] !== '') {
            $query->where('nama_kategori', 'like', '%'.$columnFilters['filter_nama'].'%');
        }

        $filteredTotal = (clone $query)->count();
        $categories = $query->paginate(15)->withQueryString();
        $catalogTotal = Category::count();
        $productTotal = Product::count();
        $hasActiveFilters = $columnFilters['filter_nama'] !== '';

        $openModal = session('_modal');
        $modalCategoryId = session('_category_id');
        $modalCategory = $modalCategoryId
            ? Category::withCount('products')->find($modalCategoryId)
            : null;

        return view('admin.categories.index', compact(
            'categories',
            'columnFilters',
            'filteredTotal',
            'catalogTotal',
            'productTotal',
            'hasActiveFilters',
            'openModal',
            'modalCategory',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = ValidatorFacade::make($request->all(), [
            'nama_kategori' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'create', null, $validator);
        }

        Category::create($validator->validated());

        return redirect()
            ->route('admin.categories.index', $this->listQueryParams($request))
            ->with('success', 'Kategori disimpan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validator = ValidatorFacade::make($request->all(), [
            'nama_kategori' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'edit', $category->id, $validator);
        }

        $category->update($validator->validated());

        return redirect()
            ->route('admin.categories.index', $this->listQueryParams($request))
            ->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori masih memiliki produk.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index', $this->listQueryParams($request))
            ->with('success', 'Kategori dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function listQueryParams(Request $request): array
    {
        return array_filter([
            'filter_nama' => $request->input('filter_nama'),
            'page' => $request->input('page'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function redirectWithModalError(
        Request $request,
        string $modal,
        ?int $categoryId,
        Validator $validator,
    ): RedirectResponse {
        $redirect = redirect()
            ->route('admin.categories.index', $this->listQueryParams($request))
            ->withInput()
            ->with('_modal', $modal)
            ->withErrors($validator);

        if ($categoryId !== null) {
            $redirect->with('_category_id', $categoryId);
        }

        return $redirect;
    }
}

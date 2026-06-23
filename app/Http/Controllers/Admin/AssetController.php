<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\View\View;

class AssetController extends Controller
{
    /** @var list<string> */
    private const COLUMN_FILTER_KEYS = [
        'filter_nama',
        'filter_tanggal',
        'filter_harga',
    ];

    public function index(Request $request): View
    {
        $columnFilters = $this->columnFiltersFromRequest($request);

        $query = Asset::query()
            ->with('user')
            ->latest('tanggal_perolehan')
            ->latest('id');

        $this->applyColumnFilters($query, $columnFilters);

        $hasActiveFilters = collect($columnFilters)->contains(fn (string $v) => $v !== '');
        $filteredCount = (clone $query)->count();
        $displayCost = (int) ($hasActiveFilters ? (clone $query)->sum('harga_perolehan') : Asset::sum('harga_perolehan'));
        $catalogTotal = Asset::count();
        $totalCost = (int) Asset::sum('harga_perolehan');
        $monthlyDepreciationTotal = (int) ($hasActiveFilters
            ? (clone $query)->get()->sum(fn (Asset $asset) => $asset->monthlyDepreciation())
            : Asset::all()->sum(fn (Asset $asset) => $asset->monthlyDepreciation()));
        $assets = $query->paginate(20)->withQueryString();

        $openModal = session('_modal');
        $modalAssetId = session('_asset_id');
        $modalAsset = $modalAssetId ? Asset::query()->find($modalAssetId) : null;

        return view('admin.assets.index', compact(
            'assets',
            'columnFilters',
            'filteredCount',
            'displayCost',
            'catalogTotal',
            'totalCost',
            'monthlyDepreciationTotal',
            'hasActiveFilters',
            'openModal',
            'modalAsset',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = $this->makeValidator($request);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'create', null, $validator);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id;
        Asset::create($data);

        return redirect()
            ->route('admin.assets.index', $this->listQueryParams($request))
            ->with('success', 'Aset tersimpan.');
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $validator = $this->makeValidator($request);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'edit', $asset->id, $validator);
        }

        $asset->update($validator->validated());

        return redirect()
            ->route('admin.assets.index', $this->listQueryParams($request))
            ->with('success', 'Aset diperbarui.');
    }

    public function destroy(Request $request, Asset $asset): RedirectResponse
    {
        $asset->delete();

        return redirect()
            ->route('admin.assets.index', $this->listQueryParams($request))
            ->with('success', 'Aset dihapus.');
    }

    private function makeValidator(Request $request): Validator
    {
        return ValidatorFacade::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_perolehan' => ['required', 'date'],
            'harga_perolehan' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    /** @return array<string, string> */
    private function columnFiltersFromRequest(Request $request): array
    {
        $filters = [];
        foreach (self::COLUMN_FILTER_KEYS as $key) {
            $filters[$key] = $request->string($key)->trim()->toString();
        }

        return $filters;
    }

    /** @param Builder<Asset> $query */
    private function applyColumnFilters(Builder $query, array $filters): void
    {
        if ($filters['filter_nama'] !== '') {
            $needle = '%'.$filters['filter_nama'].'%';
            $query->where(function (Builder $q) use ($needle) {
                $q->where('nama', 'like', $needle)
                    ->orWhere('catatan', 'like', $needle);
            });
        }

        if ($filters['filter_tanggal'] !== '') {
            $query->whereDate('tanggal_perolehan', $filters['filter_tanggal']);
        }

        if ($filters['filter_harga'] !== '') {
            $digits = preg_replace('/\D/', '', $filters['filter_harga']);
            if ($digits !== '') {
                $query->where('harga_perolehan', 'like', '%'.$digits.'%');
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function listQueryParams(Request $request): array
    {
        return array_filter(
            $request->only(array_merge(self::COLUMN_FILTER_KEYS, ['page'])),
            fn ($value) => $value !== null && $value !== '',
        );
    }

    private function redirectWithModalError(
        Request $request,
        string $modal,
        ?int $assetId,
        Validator $validator,
    ): RedirectResponse {
        $redirect = redirect()
            ->route('admin.assets.index', $this->listQueryParams($request))
            ->withInput()
            ->with('_modal', $modal)
            ->withErrors($validator);

        if ($assetId !== null) {
            $redirect->with('_asset_id', $assetId);
        }

        return $redirect;
    }
}

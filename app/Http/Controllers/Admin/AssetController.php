<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(): View
    {
        $assets = Asset::query()
            ->with('user')
            ->latest('tanggal_perolehan')
            ->latest('id')
            ->paginate(20);

        $totalCost = (int) Asset::sum('harga_perolehan');

        return view('admin.assets.index', [
            'assets' => $assets,
            'totalCost' => $totalCost,
        ]);
    }

    public function create(): View
    {
        return view('admin.assets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['user_id'] = $request->user()->id;
        Asset::create($data);

        return redirect()->route('admin.assets.index')->with('success', 'Aset tersimpan.');
    }

    public function edit(Asset $asset): View
    {
        return view('admin.assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $asset->update($this->validatedData($request));

        return redirect()->route('admin.assets.index')->with('success', 'Aset diperbarui.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $asset->delete();

        return redirect()->route('admin.assets.index')->with('success', 'Aset dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_perolehan' => ['required', 'date'],
            'harga_perolehan' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now()->endOfDay();
        $kategori = $request->string('kategori')->trim()->toString();

        $base = Expense::query()
            ->with('user')
            ->whereBetween('tanggal', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        if ($kategori !== '' && array_key_exists($kategori, Expense::categories())) {
            $base->where('kategori', $kategori);
        }

        $sumTotal = (int) (clone $base)->sum('jumlah');
        $expenses = (clone $base)->latest('tanggal')->latest('id')->paginate(20)->withQueryString();

        return view('admin.expenses.index', [
            'expenses' => $expenses,
            'sumTotal' => $sumTotal,
            'from' => $from,
            'to' => $to,
            'kategori' => $kategori,
            'categories' => Expense::categories(),
        ]);
    }

    public function create(): View
    {
        return view('admin.expenses.create', [
            'categories' => Expense::categories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['user_id'] = $request->user()->id;
        Expense::create($data);

        return redirect()->route('admin.expenses.index')->with('success', 'Pengeluaran tersimpan.');
    }

    public function edit(Expense $expense): View
    {
        return view('admin.expenses.edit', [
            'expense' => $expense,
            'categories' => Expense::categories(),
        ]);
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $expense->update($this->validatedData($request));

        return redirect()->route('admin.expenses.index')->with('success', 'Pengeluaran diperbarui.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('success', 'Pengeluaran dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'tanggal' => ['required', 'date'],
            'kategori' => ['required', 'string', 'in:'.implode(',', array_keys(Expense::categories()))],
            'nama' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}

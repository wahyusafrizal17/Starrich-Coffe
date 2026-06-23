<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Support\DashboardPeriodResolver;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /** @var list<string> */
    private const COLUMN_FILTER_KEYS = [
        'filter_tanggal',
        'filter_kategori',
        'filter_nama',
        'filter_dicatat',
    ];

    /** @var list<string> */
    private const PERIOD_QUERY_KEYS = [
        'mode',
        'tanggal',
        'bulan',
        'tahun',
        'dari',
        'sampai',
    ];

    public function index(Request $request): View
    {
        $period = DashboardPeriodResolver::fromRequest($request, 'bulanan');
        $periodLabel = DashboardPeriodResolver::label($period);
        $filterQuery = DashboardPeriodResolver::queryParams($period);
        $columnFilters = $this->columnFiltersFromRequest($request);

        $base = Expense::query()
            ->with('user')
            ->whereBetween('tanggal', [$period['dari'], $period['sampai']]);

        $this->applyColumnFilters($base, $columnFilters);

        $sumTotal = (int) (clone $base)->sum('jumlah');
        $entryCount = (clone $base)->count();
        $expenses = (clone $base)->latest('tanggal')->latest('id')->paginate(20)->withQueryString();
        $hasActiveColumnFilters = collect($columnFilters)->contains(fn (string $v) => $v !== '');
        $categories = Expense::categories();

        $openModal = session('_modal');
        $modalExpenseId = session('_expense_id');
        $modalExpense = $modalExpenseId ? Expense::query()->find($modalExpenseId) : null;

        return view('admin.expenses.index', compact(
            'expenses',
            'sumTotal',
            'entryCount',
            'period',
            'periodLabel',
            'filterQuery',
            'columnFilters',
            'hasActiveColumnFilters',
            'categories',
            'openModal',
            'modalExpense',
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
        Expense::create($data);

        return redirect()
            ->route('admin.expenses.index', $this->listQueryParams($request))
            ->with('success', 'Pengeluaran tersimpan.');
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validator = $this->makeValidator($request);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'edit', $expense->id, $validator);
        }

        $expense->update($validator->validated());

        return redirect()
            ->route('admin.expenses.index', $this->listQueryParams($request))
            ->with('success', 'Pengeluaran diperbarui.');
    }

    public function destroy(Request $request, Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()
            ->route('admin.expenses.index', $this->listQueryParams($request))
            ->with('success', 'Pengeluaran dihapus.');
    }

    private function makeValidator(Request $request): Validator
    {
        return ValidatorFacade::make($request->all(), [
            'tanggal' => ['required', 'date'],
            'kategori' => ['required', 'string', 'in:'.implode(',', array_keys(Expense::categories()))],
            'nama' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:0'],
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

    /** @param Builder<Expense> $query */
    private function applyColumnFilters(Builder $query, array $filters): void
    {
        if ($filters['filter_tanggal'] !== '') {
            $query->whereDate('tanggal', $filters['filter_tanggal']);
        }

        if ($filters['filter_kategori'] !== '' && array_key_exists($filters['filter_kategori'], Expense::categories())) {
            $query->where('kategori', $filters['filter_kategori']);
        }

        if ($filters['filter_nama'] !== '') {
            $needle = '%'.$filters['filter_nama'].'%';
            $query->where(function (Builder $q) use ($needle) {
                $q->where('nama', 'like', $needle)
                    ->orWhere('catatan', 'like', $needle);
            });
        }

        if ($filters['filter_dicatat'] !== '') {
            $needle = '%'.$filters['filter_dicatat'].'%';
            $query->whereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', $needle));
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function listQueryParams(Request $request): array
    {
        return array_filter(
            $request->only(array_merge(self::PERIOD_QUERY_KEYS, self::COLUMN_FILTER_KEYS, ['page'])),
            fn ($value) => $value !== null && $value !== '',
        );
    }

    private function redirectWithModalError(
        Request $request,
        string $modal,
        ?int $expenseId,
        Validator $validator,
    ): RedirectResponse {
        $redirect = redirect()
            ->route('admin.expenses.index', $this->listQueryParams($request))
            ->withInput()
            ->with('_modal', $modal)
            ->withErrors($validator);

        if ($expenseId !== null) {
            $redirect->with('_expense_id', $expenseId);
        }

        return $redirect;
    }
}

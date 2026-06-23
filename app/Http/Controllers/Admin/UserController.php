<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /** @var list<string> */
    private const COLUMN_FILTER_KEYS = [
        'filter_nama',
        'filter_role',
    ];

    public function index(Request $request): View
    {
        $columnFilters = $this->columnFiltersFromRequest($request);

        $query = User::query()->orderBy('name');
        $this->applyColumnFilters($query, $columnFilters);

        $hasActiveFilters = collect($columnFilters)->contains(fn (string $v) => $v !== '');
        $filteredCount = (clone $query)->count();
        $users = $query->paginate(15)->withQueryString();
        $catalogTotal = User::count();
        $adminCount = User::query()->where('role', 'admin')->count();
        $kasirCount = User::query()->where('role', 'kasir')->count();

        $openModal = session('_modal');
        $modalUserId = session('_user_id');
        $modalUser = $modalUserId ? User::query()->find($modalUserId) : null;

        return view('admin.users.index', compact(
            'users',
            'columnFilters',
            'filteredCount',
            'catalogTotal',
            'adminCount',
            'kasirCount',
            'hasActiveFilters',
            'openModal',
            'modalUser',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = $this->makeValidator($request);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'create', null, $validator);
        }

        $data = $validator->validated();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return redirect()
            ->route('admin.users.index', $this->listQueryParams($request))
            ->with('success', 'Pengguna dibuat.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validator = $this->makeValidator($request, $user);

        if ($validator->fails()) {
            return $this->redirectWithModalError($request, 'edit', $user->id, $validator);
        }

        $data = $validator->validated();

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (! empty($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index', $this->listQueryParams($request))
            ->with('success', 'Pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('admin.users.index', $this->listQueryParams($request))
                ->with('error', 'Tidak dapat menghapus akun yang sedang aktif.');
        }

        if (User::count() <= 1) {
            return redirect()
                ->route('admin.users.index', $this->listQueryParams($request))
                ->with('error', 'Minimal satu pengguna harus ada.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index', $this->listQueryParams($request))
            ->with('success', 'Pengguna dihapus.');
    }

    private function makeValidator(Request $request, ?User $existing = null): Validator
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($existing?->id),
            ],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
        ];

        if ($existing === null) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        } elseif ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return ValidatorFacade::make($request->all(), $rules);
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

    /** @param Builder<User> $query */
    private function applyColumnFilters(Builder $query, array $filters): void
    {
        if ($filters['filter_nama'] !== '') {
            $needle = '%'.$filters['filter_nama'].'%';
            $query->where(function (Builder $q) use ($needle) {
                $q->where('name', 'like', $needle)
                    ->orWhere('email', 'like', $needle);
            });
        }

        if (in_array($filters['filter_role'], ['admin', 'kasir'], true)) {
            $query->where('role', $filters['filter_role']);
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
        ?int $userId,
        Validator $validator,
    ): RedirectResponse {
        $redirect = redirect()
            ->route('admin.users.index', $this->listQueryParams($request))
            ->withInput()
            ->with('_modal', $modal)
            ->withErrors($validator);

        if ($userId !== null) {
            $redirect->with('_user_id', $userId);
        }

        return $redirect;
    }
}

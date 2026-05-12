@extends('layouts.admin')

@section('title', 'Pengguna')

@section('page_header')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengguna</h1>
            <p class="text-sm text-slate-500">Admin dan kasir</p>
        </div>
        <a
            href="{{ route('admin.users.create') }}"
            class="inline-flex h-12 items-center rounded-2xl bg-teal-700 px-5 text-sm font-semibold text-white shadow-md shadow-teal-700/25"
        >
            Tambah pengguna
        </a>
    </div>
@endsection

@section('content')
    <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $u)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $u->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $u->email }}</td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $u->role === 'admin' ? 'bg-violet-100 text-violet-800' : 'bg-slate-100 text-slate-700' }}"
                                >
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.users.edit', $u) }}" class="text-teal-700 hover:underline">Edit</a>
                                <form
                                    action="{{ route('admin.users.destroy', $u) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Hapus pengguna ini?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>
    </div>
@endsection

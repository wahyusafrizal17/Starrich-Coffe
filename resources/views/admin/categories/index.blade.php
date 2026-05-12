@extends('layouts.admin')

@section('title', 'Kategori')

@section('page_header')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kategori</h1>
            <p class="text-sm text-slate-500">Kelompokkan produk Anda</p>
        </div>
        <a
            href="{{ route('admin.categories.create') }}"
            class="inline-flex h-12 items-center rounded-2xl bg-teal-700 px-5 text-sm font-semibold text-white shadow-md shadow-teal-700/25"
        >
            Tambah
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
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($categories as $cat)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $cat->nama_kategori }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $cat->products_count }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="text-teal-700 hover:underline">Edit</a>
                                <form
                                    action="{{ route('admin.categories.destroy', $cat) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Hapus kategori ini?');"
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
        <div class="border-t border-slate-100 px-5 py-4">{{ $categories->links() }}</div>
    </div>
@endsection

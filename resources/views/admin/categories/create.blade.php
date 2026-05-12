@extends('layouts.admin')

@section('title', 'Kategori baru')

@section('page_header')
    <h1 class="text-2xl font-bold text-slate-900">Kategori baru</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-lg rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="nama_kategori" value="Nama kategori" />
                <x-text-input
                    id="nama_kategori"
                    name="nama_kategori"
                    type="text"
                    class="mt-1 block w-full rounded-2xl border-slate-200"
                    :value="old('nama_kategori')"
                    required
                />
                <x-input-error :messages="$errors->get('nama_kategori')" class="mt-2" />
            </div>
            <div class="flex gap-3">
                <x-primary-button class="rounded-2xl px-6 py-3">Simpan</x-primary-button>
                <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection

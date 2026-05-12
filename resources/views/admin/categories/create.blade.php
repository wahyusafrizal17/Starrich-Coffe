@extends('layouts.admin')

@section('title', 'Kategori baru')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.categories.index') }}">Kategori</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Baru</span>
@endsection

@section('page_header')
    <div>
        <h1>Kategori baru</h1>
        <p>Buat kelompok produk yang baru.</p>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="vx-card vx-card-pad">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
                @csrf
                <div class="vx-field">
                    <x-input-label for="nama_kategori" value="Nama kategori" />
                    <x-text-input
                        id="nama_kategori"
                        name="nama_kategori"
                        type="text"
                        :value="old('nama_kategori')"
                        placeholder="mis. Kopi"
                        required
                    />
                    <x-input-error :messages="$errors->get('nama_kategori')" />
                </div>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.categories.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
                    <x-primary-button>Simpan kategori</x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Edit produk')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.products.index') }}">Produk</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Edit</span>
@endsection

@section('page_header')
    <div>
        <h1>Edit produk</h1>
        <p>Perbarui informasi produk yang sudah ada.</p>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid gap-5 lg:grid-cols-3">
            <div class="vx-card vx-card-pad space-y-5 lg:col-span-2">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Informasi produk</h2>
                    <p class="text-xs text-slate-500">Detail dasar yang tampil di kasir.</p>
                </div>

                <div class="vx-field">
                    <x-input-label for="nama_produk" value="Nama produk" />
                    <x-text-input id="nama_produk" name="nama_produk" type="text" :value="old('nama_produk', $product->nama_produk)" required />
                    <x-input-error :messages="$errors->get('nama_produk')" />
                </div>

                <div class="vx-field">
                    <x-input-label for="kategori_id" value="Kategori" />
                    <select id="kategori_id" name="kategori_id" class="vx-select" required>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('kategori_id', $product->kategori_id) == $c->id)>{{ $c->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kategori_id')" />
                </div>

                <div class="vx-field">
                    <x-input-label for="harga" value="Harga (Rp)" />
                    <x-text-input id="harga" name="harga" type="number" min="0" :value="old('harga', $product->harga)" required />
                    <x-input-error :messages="$errors->get('harga')" />
                </div>
            </div>

            <div class="vx-card vx-card-pad space-y-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Gambar</h2>
                    <p class="text-xs text-slate-500">Ganti dengan unggah file baru.</p>
                </div>

                @if ($product->gambar)
                    <div class="flex items-center gap-3 rounded-xl border border-[var(--vx-border-soft)] bg-slate-50 p-3">
                        <img src="{{ $product->imageUrl() }}" alt="" class="h-16 w-16 rounded-lg object-cover" />
                        <p class="text-xs text-slate-500">Gambar saat ini. Unggah file baru untuk menggantinya.</p>
                    </div>
                @endif

                <div class="vx-field">
                    <x-input-label for="gambar" value="Unggah gambar baru" />
                    <input id="gambar" name="gambar" type="file" accept="image/*" class="vx-input" />
                    <p class="vx-help">Opsional.</p>
                    <x-input-error :messages="$errors->get('gambar')" />
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.products.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
            <x-primary-button>Perbarui produk</x-primary-button>
        </div>
    </form>
@endsection

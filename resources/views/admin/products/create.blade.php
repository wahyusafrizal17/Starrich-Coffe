@extends('layouts.admin')

@section('title', 'Produk baru')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.products.index') }}">Produk</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Baru</span>
@endsection

@section('page_header')
    <div>
        <h1>Produk baru</h1>
        <p>Tambahkan menu yang baru ke katalog kasir.</p>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid gap-5 lg:grid-cols-3">
            <div class="vx-card vx-card-pad space-y-5 lg:col-span-2">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Informasi produk</h2>
                    <p class="text-xs text-slate-500">Detail dasar yang akan tampil di halaman kasir.</p>
                </div>

                <div class="vx-field">
                    <x-input-label for="nama_produk" value="Nama produk" />
                    <x-text-input id="nama_produk" name="nama_produk" type="text" :value="old('nama_produk')" placeholder="mis. Espresso" required />
                    <x-input-error :messages="$errors->get('nama_produk')" />
                </div>

                <div class="vx-field">
                    <x-input-label for="kategori_id" value="Kategori" />
                    <select id="kategori_id" name="kategori_id" class="vx-select" required>
                        <option value="">— Pilih kategori —</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('kategori_id') == $c->id)>{{ $c->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kategori_id')" />
                </div>

                <div class="vx-field">
                    <x-input-label for="harga" value="Harga (Rp)" />
                    <x-text-input id="harga" name="harga" type="number" min="0" :value="old('harga')" placeholder="0" required />
                    <x-input-error :messages="$errors->get('harga')" />
                </div>
            </div>

            <div class="vx-card vx-card-pad space-y-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Gambar</h2>
                    <p class="text-xs text-slate-500">Format JPG/PNG, ukuran ideal 1:1.</p>
                </div>
                <div class="vx-field">
                    <x-input-label for="gambar" value="Unggah gambar" />
                    <input id="gambar" name="gambar" type="file" accept="image/*" class="vx-input" />
                    <p class="vx-help">Opsional. Bisa diisi kemudian.</p>
                    <x-input-error :messages="$errors->get('gambar')" />
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.products.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
            <x-primary-button>Simpan produk</x-primary-button>
        </div>
    </form>
@endsection

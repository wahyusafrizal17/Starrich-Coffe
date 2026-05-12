@extends('layouts.admin')

@section('title', 'Produk baru')

@section('page_header')
    <h1 class="text-2xl font-bold text-slate-900">Produk baru</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-lg rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="nama_produk" value="Nama produk" />
                <x-text-input id="nama_produk" name="nama_produk" type="text" class="mt-1 block w-full rounded-2xl border-slate-200" :value="old('nama_produk')" required />
                <x-input-error :messages="$errors->get('nama_produk')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="kategori_id" value="Kategori" />
                <select
                    id="kategori_id"
                    name="kategori_id"
                    class="mt-1 block w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-teal-600 focus:ring-teal-600"
                    required
                >
                    <option value="">— Pilih —</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('kategori_id') == $c->id)>{{ $c->nama_kategori }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('kategori_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="harga" value="Harga (Rp)" />
                <x-text-input id="harga" name="harga" type="number" min="0" class="mt-1 block w-full rounded-2xl border-slate-200" :value="old('harga')" required />
                <x-input-error :messages="$errors->get('harga')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="stok" value="Stok" />
                <x-text-input id="stok" name="stok" type="number" min="0" class="mt-1 block w-full rounded-2xl border-slate-200" :value="old('stok', 0)" required />
                <x-input-error :messages="$errors->get('stok')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="gambar" value="Gambar (opsional)" />
                <input
                    id="gambar"
                    name="gambar"
                    type="file"
                    accept="image/*"
                    class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-teal-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-teal-800"
                />
                <x-input-error :messages="$errors->get('gambar')" class="mt-2" />
            </div>
            <div class="flex gap-3">
                <x-primary-button class="rounded-2xl px-6 py-3">Simpan</x-primary-button>
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection

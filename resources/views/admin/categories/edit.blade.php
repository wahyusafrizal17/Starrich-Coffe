@extends('layouts.admin')

@section('title', 'Edit kategori')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.categories.index') }}">Kategori</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Edit</span>
@endsection

@section('page_header')
    <div>
        <h1>Edit kategori</h1>
        <p>Ubah nama kategori yang sudah ada.</p>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="vx-card vx-card-pad">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="vx-field">
                    <x-input-label for="nama_kategori" value="Nama kategori" />
                    <x-text-input
                        id="nama_kategori"
                        name="nama_kategori"
                        type="text"
                        :value="old('nama_kategori', $category->nama_kategori)"
                        required
                    />
                    <x-input-error :messages="$errors->get('nama_kategori')" />
                </div>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.categories.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
                    <x-primary-button>Perbarui</x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection

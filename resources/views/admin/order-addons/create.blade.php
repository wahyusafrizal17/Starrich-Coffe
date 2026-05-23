@extends('layouts.admin')

@section('title', 'Tambahan baru')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.order-addons.index') }}">Tambahan pesanan</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Baru</span>
@endsection

@section('page_header')
    <div>
        <h1>Tambahan baru</h1>
        <p>Opsi yang bisa dipilih kasir saat menambah menu minuman/kopi.</p>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="vx-card vx-card-pad">
            <form method="POST" action="{{ route('admin.order-addons.store') }}" class="space-y-5">
                @csrf
                @include('admin.order-addons._form')
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.order-addons.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
                    <x-primary-button>Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection

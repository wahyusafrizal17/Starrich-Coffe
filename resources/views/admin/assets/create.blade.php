@extends('layouts.admin')

@section('title', 'Aset baru')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.assets.index') }}">Aset</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Baru</span>
@endsection

@section('page_header')
    <div>
        <h1>Aset baru</h1>
        <p>Catat peralatan untuk perhitungan beban depresiasi.</p>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.assets.store') }}">
        @csrf
        @include('admin.assets._form', ['asset' => null])
    </form>
@endsection

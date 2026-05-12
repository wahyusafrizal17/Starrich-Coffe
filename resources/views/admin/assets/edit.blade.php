@extends('layouts.admin')

@section('title', 'Edit aset')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.assets.index') }}">Aset</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Edit</span>
@endsection

@section('page_header')
    <div>
        <h1>Edit aset</h1>
        <p>Perbarui informasi aset & masa manfaat.</p>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.assets.update', $asset) }}">
        @csrf
        @method('PUT')
        @include('admin.assets._form', ['asset' => $asset])
    </form>
@endsection

@extends('layouts.admin')

@section('title', 'Pengeluaran baru')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.expenses.index') }}">Pengeluaran</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Baru</span>
@endsection

@section('page_header')
    <div>
        <h1>Pengeluaran baru</h1>
        <p>Tambahkan satu pos biaya operasional.</p>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.expenses.store') }}">
        @csrf
        @include('admin.expenses._form', ['expense' => null, 'categories' => $categories])
    </form>
@endsection

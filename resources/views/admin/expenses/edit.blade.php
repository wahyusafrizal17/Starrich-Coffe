@extends('layouts.admin')

@section('title', 'Edit pengeluaran')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.expenses.index') }}">Pengeluaran</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Edit</span>
@endsection

@section('page_header')
    <div>
        <h1>Edit pengeluaran</h1>
        <p>Perbarui detail pos biaya.</p>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.expenses.update', $expense) }}">
        @csrf
        @method('PUT')
        @include('admin.expenses._form', ['expense' => $expense, 'categories' => $categories])
    </form>
@endsection

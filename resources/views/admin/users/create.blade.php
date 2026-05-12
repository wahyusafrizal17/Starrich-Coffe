@extends('layouts.admin')

@section('title', 'Pengguna baru')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.users.index') }}">Pengguna</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Baru</span>
@endsection

@section('page_header')
    <div>
        <h1>Pengguna baru</h1>
        <p>Buat akun untuk admin atau kasir.</p>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="vx-card vx-card-pad">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="vx-field">
                        <x-input-label for="name" value="Nama lengkap" />
                        <x-text-input id="name" name="name" type="text" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div class="vx-field">
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" />
                    </div>
                </div>

                <div class="vx-field">
                    <x-input-label for="role" value="Role" />
                    <select id="role" name="role" class="vx-select" required>
                        <option value="kasir" @selected(old('role') === 'kasir')>Kasir</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="vx-field">
                        <x-input-label for="password" value="Password" />
                        <x-text-input id="password" name="password" type="password" required />
                        <x-input-error :messages="$errors->get('password')" />
                    </div>
                    <div class="vx-field">
                        <x-input-label for="password_confirmation" value="Konfirmasi password" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" required />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.users.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
                    <x-primary-button>Simpan pengguna</x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection

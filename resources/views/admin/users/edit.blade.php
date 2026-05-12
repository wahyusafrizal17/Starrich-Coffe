@extends('layouts.admin')

@section('title', 'Edit pengguna')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <a href="{{ route('admin.users.index') }}">Pengguna</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Edit</span>
@endsection

@section('page_header')
    <div>
        <h1>Edit pengguna</h1>
        <p>Perbarui akun admin atau kasir.</p>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="vx-card vx-card-pad">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="vx-field">
                        <x-input-label for="name" value="Nama lengkap" />
                        <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>
                    <div class="vx-field">
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" />
                    </div>
                </div>

                <div class="vx-field">
                    <x-input-label for="role" value="Role" />
                    <select id="role" name="role" class="vx-select" required>
                        <option value="kasir" @selected(old('role', $user->role) === 'kasir')>Kasir</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="vx-field">
                        <x-input-label for="password" value="Password baru (opsional)" />
                        <x-text-input id="password" name="password" type="password" />
                        <x-input-error :messages="$errors->get('password')" />
                    </div>
                    <div class="vx-field">
                        <x-input-label for="password_confirmation" value="Konfirmasi password" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.users.index') }}" class="vx-btn vx-btn-ghost">Batal</a>
                    <x-primary-button>Perbarui pengguna</x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection

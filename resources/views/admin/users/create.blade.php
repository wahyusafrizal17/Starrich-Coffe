@extends('layouts.admin')

@section('title', 'Pengguna baru')

@section('page_header')
    <h1 class="text-2xl font-bold text-slate-900">Pengguna baru</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-lg rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="name" value="Nama" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-2xl border-slate-200" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-2xl border-slate-200" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="role" value="Role" />
                <select id="role" name="role" class="mt-1 block w-full rounded-2xl border-slate-200 text-sm shadow-sm focus:border-teal-600 focus:ring-teal-600" required>
                    <option value="kasir" @selected(old('role') === 'kasir')>Kasir</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full rounded-2xl border-slate-200" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi password" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-2xl border-slate-200" required />
            </div>
            <div class="flex gap-3">
                <x-primary-button class="rounded-2xl px-6 py-3">Simpan</x-primary-button>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection

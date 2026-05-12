@extends('layouts.login')

@section('content')
    <x-auth-session-status class="login-alert" :status="session('status')" />

    <div class="login-logo-row">
        <img
            class="login-logo-img"
            src="{{ asset('images/logo/logo.png') }}"
            width="120"
            height="120"
            alt="{{ config('app.name', 'Starrich') }}"
        />
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="login-field">
            <span class="login-fi-l" aria-hidden="true">
                <svg class="field-svg" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </span>
            <label for="email" class="sr-only">{{ __('Email') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="nama@email.com"
            />
            @error('email')
                <p class="login-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="login-field login-field--password" x-data="{ show: false }">
            <span class="login-fi-l" aria-hidden="true">
                <svg class="field-svg" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <label for="password" class="sr-only">{{ __('Password') }}</label>
            <input
                id="password"
                name="password"
                x-bind:type="show ? 'text' : 'password'"
                required
                autocomplete="current-password"
                placeholder="Minimal 6 karakter"
            />
            <button
                type="button"
                class="login-fi-r"
                @click.prevent="show = !show"
                :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
            >
                <svg class="field-svg" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
            @error('password')
                <p class="login-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="login-remember">
            <input id="remember_me" type="checkbox" name="remember"/>
            <label for="remember_me">Ingat saya</label>
        </div>

        <button type="submit" class="login-btn">Masuk</button>

        <p class="login-footer">© {{ date('Y') }} {{ config('app.name', 'Starrich') }}. All rights reserved.</p>
    </form>
@endsection

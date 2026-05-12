@extends('layouts.admin')

@section('title', 'Profil')

@section('page_header')
    <h1 class="text-2xl font-bold text-slate-900">Profil</h1>
    <p class="text-sm text-slate-500">Akun dan keamanan</p>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection

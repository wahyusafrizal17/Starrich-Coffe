@extends('layouts.admin')

@section('title', 'Profil')

@section('page_header')
    <div>
        <h1>Profil</h1>
        <p>Kelola informasi akun dan keamanan login.</p>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl space-y-5">
        <div class="vx-card vx-card-pad">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="vx-card vx-card-pad">
            @include('profile.partials.update-password-form')
        </div>

        <div class="vx-card vx-card-pad">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection

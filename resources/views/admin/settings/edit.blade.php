@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Beranda</a>
    <span class="vx-sep">/</span>
    <span class="vx-current">Pengaturan</span>
@endsection

@section('page_header')
    <div>
        <h1>Pengaturan</h1>
        <p>Atur tanggal jatuh tempo pembayaran domain dan hosting.</p>
    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="vx-card vx-card-pad">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="vx-field">
                    <label class="vx-label" for="domain_payment_due_date">Tanggal pembayaran domain</label>
                    <input
                        id="domain_payment_due_date"
                        name="domain_payment_due_date"
                        type="date"
                        class="vx-input w-full"
                        value="{{ old('domain_payment_due_date', $domainDueDate) }}"
                    />
                    <p class="mt-1 text-xs text-slate-500">Admin akan mendapat peringatan 5 hari sebelum tanggal ini.</p>
                    <x-input-error :messages="$errors->get('domain_payment_due_date')" />
                </div>

                <div class="vx-field">
                    <label class="vx-label" for="hosting_payment_due_date">Tanggal pembayaran hosting</label>
                    <input
                        id="hosting_payment_due_date"
                        name="hosting_payment_due_date"
                        type="date"
                        class="vx-input w-full"
                        value="{{ old('hosting_payment_due_date', $hostingDueDate) }}"
                    />
                    <p class="mt-1 text-xs text-slate-500">Admin akan mendapat peringatan 5 hari sebelum tanggal ini.</p>
                    <x-input-error :messages="$errors->get('hosting_payment_due_date')" />
                </div>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <x-primary-button>Simpan pengaturan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection

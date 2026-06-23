<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="space-y-5 p-5">
        <div class="vx-field">
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>
        <div class="vx-field">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                Email Anda belum terverifikasi.
                <button form="send-verification" class="font-semibold underline">Kirim ulang tautan verifikasi.</button>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 font-semibold text-emerald-700">Tautan verifikasi baru telah dikirim.</p>
                @endif
            </div>
        @endif
    </div>

    <div class="vx-table-foot flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
        @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-slate-500 sm:mr-auto"
            >Perubahan tersimpan.</p>
        @endif
        <x-primary-button>Simpan profil</x-primary-button>
    </div>
</form>

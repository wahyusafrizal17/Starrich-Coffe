<section>
    <header class="mb-5">
        <h2 class="text-base font-semibold text-slate-900">Ubah password</h2>
        <p class="mt-1 text-sm text-slate-500">Gunakan password yang panjang dan unik agar akun tetap aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div class="vx-field">
            <x-input-label for="update_password_current_password" value="Password saat ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="vx-field">
                <x-input-label for="update_password_password" value="Password baru" />
                <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" />
            </div>
            <div class="vx-field">
                <x-input-label for="update_password_password_confirmation" value="Konfirmasi password" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>Simpan</x-primary-button>
            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-500"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>

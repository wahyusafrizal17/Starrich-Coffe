<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="space-y-5 p-5">
        <div class="vx-field">
            <x-input-label for="update_password_current_password" value="Password saat ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

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

    <div class="vx-table-foot flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
        @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-slate-500 sm:mr-auto"
            >Password diperbarui.</p>
        @endif
        <x-primary-button>Simpan password</x-primary-button>
    </div>
</form>

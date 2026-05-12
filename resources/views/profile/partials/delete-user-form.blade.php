<section class="space-y-4">
    <header>
        <h2 class="text-base font-semibold text-slate-900">Hapus akun</h2>
        <p class="mt-1 text-sm text-slate-500">
            Setelah akun dihapus, seluruh data tidak dapat dipulihkan. Pastikan Anda telah menyimpan data penting terlebih dahulu.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Hapus akun</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-base font-semibold text-slate-900">
                Yakin ingin menghapus akun Anda?
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Tindakan ini tidak dapat dibatalkan. Masukkan password untuk mengonfirmasi.
            </p>

            <div class="mt-5 vx-field">
                <x-input-label for="password" value="Password" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Password"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button>
                    Hapus akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

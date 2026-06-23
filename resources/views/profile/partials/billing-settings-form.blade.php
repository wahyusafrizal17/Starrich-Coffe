<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="space-y-5 p-5">
        <div class="vx-field">
            <label class="vx-label" for="domain_payment_due_date">Tanggal pembayaran domain</label>
            <input
                id="domain_payment_due_date"
                name="domain_payment_due_date"
                type="date"
                class="vx-input w-full"
                value="{{ old('domain_payment_due_date', $domainDueDate) }}"
            />
            <p class="vx-help">Kosongkan jika tidak ingin mengatur peringatan domain.</p>
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
            <p class="vx-help">Kosongkan jika tidak ingin mengatur peringatan hosting.</p>
            <x-input-error :messages="$errors->get('hosting_payment_due_date')" />
        </div>
    </div>

    <div class="vx-table-foot flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <x-primary-button>Simpan pengaturan</x-primary-button>
    </div>
</form>

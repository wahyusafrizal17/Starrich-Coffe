@if (! empty($billingDueAlerts))
    <div class="mb-5 space-y-3">
        @foreach ($billingDueAlerts as $alert)
            @php
                $isDanger = $alert['severity'] === 'danger';
            @endphp
            <div
                class="flex flex-col gap-3 rounded-xl border px-4 py-3 sm:flex-row sm:items-center sm:justify-between {{ $isDanger ? 'border-red-200 bg-red-50 text-red-900' : 'border-amber-200 bg-amber-50 text-amber-950' }}"
                role="alert"
            >
                <div class="flex items-start gap-3 min-w-0">
                    <span class="mt-0.5 shrink-0 {{ $isDanger ? 'text-red-600' : 'text-amber-600' }}" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold">{{ $alert['label'] }}</p>
                        <p class="text-sm opacity-90">{{ \App\Support\BillingDueReminder::message($alert) }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.settings.edit') }}" class="vx-btn vx-btn-ghost shrink-0 self-start sm:self-center {{ $isDanger ? 'border-red-200 text-red-800 hover:bg-red-100' : 'border-amber-200 text-amber-900 hover:bg-amber-100' }}">
                    Kelola pengaturan
                </a>
            </div>
        @endforeach
    </div>
@endif

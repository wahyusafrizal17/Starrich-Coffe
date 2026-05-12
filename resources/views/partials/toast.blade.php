<div
    x-show="$store.toast.open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="translate-y-3 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-2 opacity-0"
    class="pointer-events-none fixed bottom-6 left-1/2 z-[100] w-[calc(100%-2rem)] max-w-md -translate-x-1/2 px-4 sm:px-0"
    x-cloak
>
    <div
        class="pointer-events-auto rounded-2xl px-4 py-3 text-center text-sm font-semibold shadow-lg ring-1 ring-black/5"
        :class="$store.toast.variant === 'error' ? 'bg-red-600 text-white' : 'bg-teal-700 text-white'"
        x-text="$store.toast.message"
    ></div>
</div>

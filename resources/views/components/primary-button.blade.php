<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-3 bg-teal-700 border border-transparent rounded-2xl font-semibold text-sm text-white hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50']) }}>
    {{ $slot }}
</button>

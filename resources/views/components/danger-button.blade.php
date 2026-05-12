<button {{ $attributes->merge(['type' => 'submit', 'class' => 'vx-btn vx-btn-primary !bg-red-600 !shadow-red-600/30 hover:!bg-red-700']) }}>
    {{ $slot }}
</button>

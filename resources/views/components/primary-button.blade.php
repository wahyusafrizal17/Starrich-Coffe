<button {{ $attributes->merge(['type' => 'submit', 'class' => 'vx-btn vx-btn-primary']) }}>
    {{ $slot }}
</button>

@if (session('success'))
    <div data-flash-success class="sr-only" data-flash-success="{{ session('success') }}"></div>
@endif
@if (session('error'))
    <div data-flash-error class="sr-only" data-flash-error="{{ session('error') }}"></div>
@endif
@if (session('status'))
    <div data-flash-success class="sr-only" data-flash-success="{{ session('status') }}"></div>
@endif

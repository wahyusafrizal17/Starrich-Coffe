@extends('layouts.pos')

@section('title', 'Kasir')

@php
    $posPayload = [
        'products' => $products->values()->all(),
        'categories' => $categories
            ->values()
            ->map(fn ($c) => ['id' => $c->id, 'nama_kategori' => $c->nama_kategori])
            ->values()
            ->all(),
        'checkoutUrl' => route('cashier.checkout'),
        'openBillsUrl' => route('cashier.open-bills.data'),
        'payOpenBillUrlTemplate' => route('cashier.open-bills.pay', ['transaction' => '__ID__']),
        'invoiceUrlTemplate' => route('cashier.invoice', ['transaction' => '__ID__']),
        'csrf' => csrf_token(),
    ];
@endphp

@section('content')
    @include('cashier._pos-coffee-styles')

    <div
        class="pos-coffee flex min-h-0 flex-1 flex-col overflow-hidden"
        x-data="StarrichPos({{ \Illuminate\Support\Js::from($posPayload) }})"
        x-on:keydown.escape.window="if (payModalOpen) { closePaymentModal(); } else { cartOpen = false }"
    >
        @include('cashier._cashier-header', ['openBillsCount' => $openBillsCount])

        <div class="pc-main min-h-0 flex-1">
            <div class="pc-left">
                <div class="pc-categories">
                    <button
                        type="button"
                        class="pc-cat-btn"
                        :class="{ active: categoryId === '' }"
                        x-on:click="categoryId = ''"
                    >
                        Semua
                    </button>
                    <template x-for="c in categories" :key="c.id">
                        <button
                            type="button"
                            class="pc-cat-btn"
                            :class="{ active: String(categoryId) === String(c.id) }"
                            x-on:click="categoryId = String(c.id)"
                            x-text="c.nama_kategori"
                        ></button>
                    </template>
                </div>

                <div class="pc-search-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input
                        type="search"
                        class="pc-search-input"
                        x-model="search"
                        placeholder="Cari menu…"
                        autocomplete="off"
                    />
                </div>

                <div class="pc-menu-grid">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <button
                            type="button"
                            class="pc-menu-card"
                            x-on:click="addProduct(p)"
                        >
                            <div class="pc-card-thumb">
                                <img x-show="p.gambar" x-cloak :src="p.gambar" :alt="p.nama_produk" loading="lazy" />
                                <span class="pc-card-emoji" x-show="!p.gambar" x-text="emojiIcon(p)" x-cloak></span>
                            </div>
                            <div class="pc-card-name" x-text="p.nama_produk"></div>
                            <div class="pc-card-footer">
                                <span class="pc-card-price" x-text="formatRp(p.harga)"></span>
                                <span class="pc-card-tag" x-show="categoryShort(p)" x-text="categoryShort(p)" x-cloak></span>
                            </div>
                        </button>
                    </template>
                </div>
                <p
                    class="py-12 text-center text-sm"
                    style="color: var(--brown-light, #64748b)"
                    x-show="filteredProducts.length === 0"
                    x-cloak
                >
                    Tidak ada produk yang cocok.
                </p>
            </div>

            <aside class="pc-right pc-right-sidebar hidden lg:flex" aria-label="Keranjang">
                @include('cashier._cart-panel', ['mobile' => false])
            </aside>
        </div>

        @include('cashier._pay-modal')

        {{-- Drawer mobile --}}
        <div class="fixed inset-0 z-50 lg:hidden" x-show="cartOpen" x-transition.opacity.duration.200ms x-cloak>
            <div class="absolute inset-0 bg-stone-900/50 backdrop-blur-sm" x-on:click="cartOpen = false"></div>
            <div
                class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col border-l shadow-2xl"
                style="background: var(--warm-white, #f8fafc); border-color: rgba(15, 23, 42, 0.1)"
                x-show="cartOpen"
                x-transition:enter="transition duration-200 ease-out"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition duration-150 ease-in"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
            >
                <div class="relative flex min-h-0 flex-1 flex-col overflow-hidden pt-12">
                    <button
                        type="button"
                        class="absolute right-4 top-3 z-10 flex h-10 w-10 items-center justify-center rounded-full border text-xl font-medium"
                        style="border-color: rgba(15, 23, 42, 0.12); color: var(--brown-mid, #334155); background: var(--cream, #eff6ff)"
                        aria-label="Tutup"
                        x-on:click="cartOpen = false"
                    >
                        ×
                    </button>
                    @include('cashier._cart-panel', ['mobile' => true])
                </div>
            </div>
        </div>
    </div>
@endsection

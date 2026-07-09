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
        'deleteOpenBillUrlTemplate' => route('cashier.open-bills.destroy', ['transaction' => '__ID__']),
        'openBillEditDataUrlTemplate' => route('cashier.open-bills.edit-data', ['transaction' => '__ID__']),
        'updateOpenBillUrlTemplate' => route('cashier.open-bills.update', ['transaction' => '__ID__']),
        'invoiceUrlTemplate' => route('cashier.invoice', ['transaction' => '__ID__']),
        'addonsCatalog' => $addonsCatalog,
        'csrf' => csrf_token(),
        'isAdmin' => auth()->user()->isAdmin(),
        'userName' => auth()->user()->name,
    ];
@endphp

@section('content')
    @include('cashier._pos-coffee-styles')

    <div
        class="pos-kasir flex min-h-0 flex-1 flex-col"
        x-data="StarrichPos({{ \Illuminate\Support\Js::from($posPayload) }})"
        x-on:keydown.escape.window="if (addonModalOpen) { closeAddonModal(); } else if (varianModalOpen) { closeVarianModal(); } else if (payModalOpen) { closePaymentModal(); }"
    >
        <div class="sr-app">
            <div class="menu-side">
                <div
                    class="sr-edit-banner"
                    x-show="editingOpenBillId"
                    x-cloak
                    role="status"
                >
                    <div>
                        <strong>Mengedit open bill</strong>
                        <span x-text="'#' + String(editingOpenBillId).padStart(5, '0') + (openBillName ? ' / ' + openBillName : '')"></span>
                    </div>
                    <div class="sr-edit-actions">
                        <button type="button" class="is-primary" x-on:click="saveOpenBillEdits()" :disabled="paying || cart.length === 0">
                            Simpan
                        </button>
                        <button type="button" x-on:click="cancelOpenBillEdit()" :disabled="paying">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="controls">
                    <div class="search-wrap">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input
                            type="search"
                            x-model="search"
                            placeholder="Cari menu..."
                            autocomplete="off"
                        />
                    </div>
                    @include('cashier._category-dock')
                </div>

                <div class="menu-grid">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <button type="button" class="item-card" x-on:click="addProduct(p)">
                            <div class="item-media">
                                <div
                                    class="qty-badge"
                                    :class="{ show: productCartQty(p.id) > 0 }"
                                    x-text="productCartQty(p.id)"
                                    x-show="productCartQty(p.id) > 0"
                                    x-cloak
                                ></div>
                                <div class="item-icon" :class="{ 'is-fallback': !productShowsImage(p) }">
                                    <img
                                        x-show="productShowsImage(p)"
                                        x-cloak
                                        :src="p.gambar"
                                        alt=""
                                        loading="lazy"
                                        x-on:error="onProductImageError(p.id)"
                                    />
                                    <span class="item-emoji" x-show="!productShowsImage(p)" x-text="emojiIcon(p)" x-cloak></span>
                                </div>
                            </div>
                            <div class="item-body">
                                <div class="item-name" x-text="p.nama_produk"></div>
                                <div class="item-meta">
                                    <span class="item-price" x-text="formatRp(p.harga)"></span>
                                </div>
                            </div>
                        </button>
                    </template>
                    <div class="empty-results" x-show="filteredProducts.length === 0" x-cloak>
                        Menu tidak ditemukan.
                    </div>
                </div>
            </div>

            @include('cashier._cart-panel')
        </div>

        @include('cashier._cashier-bottom-nav', [
            'active' => 'kasir',
            'openBillsCount' => $openBillsCount,
            'alpine' => true,
        ])

        @include('cashier._pay-modal')
        @include('cashier._varian-suhu-modal')
        @include('cashier._addon-modal')
    </div>
@endsection

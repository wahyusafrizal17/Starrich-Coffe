import './bootstrap';

import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        open: false,
        message: '',
        variant: 'success',
        _t: null,
        show(message, variant = 'success') {
            this.message = message;
            this.variant = variant;
            this.open = true;
            if (this._t) {
                clearTimeout(this._t);
            }
            this._t = setTimeout(() => {
                this.open = false;
            }, 3200);
        },
    });
});

window.StarrichPos = function StarrichPos(payload) {
    return {
        products: payload.products,
        categories: payload.categories ?? [],
        checkoutUrl: payload.checkoutUrl,
        csrf: payload.csrf,
        search: '',
        categoryId: '',
        cart: [],
        cartOpen: false,
        paying: false,
        orderType: 'dine',
        payModalOpen: false,
        paymentSplits: [{ metode: 'cash', jumlah: '' }],

        init() {
            const mq = window.matchMedia('(min-width: 1024px)');
            const sync = () => {
                if (mq.matches) {
                    this.cartOpen = true;
                }
            };
            sync();
            mq.addEventListener('change', sync);

            const flash = document.querySelector('[data-flash-success]');
            if (flash?.dataset.flashSuccess) {
                Alpine.store('toast').show(flash.dataset.flashSuccess, 'success');
            }
            const flashErr = document.querySelector('[data-flash-error]');
            if (flashErr?.dataset.flashError) {
                Alpine.store('toast').show(flashErr.dataset.flashError, 'error');
            }
        },

        get filteredProducts() {
            const q = this.search.trim().toLowerCase();
            const cat =
                this.categoryId === '' || this.categoryId === null
                    ? null
                    : Number(this.categoryId);
            return this.products.filter((p) => {
                if (cat && Number(p.kategori_id) !== cat) {
                    return false;
                }
                if (! q) {
                    return true;
                }
                return String(p.nama_produk).toLowerCase().includes(q);
            });
        },

        get cartTotal() {
            return this.cart.reduce((s, i) => s + i.harga * i.qty, 0);
        },

        get splitPaidTotal() {
            return this.paymentSplits.reduce((s, r) => s + this.parseRupiahInput(r.jumlah), 0);
        },

        get splitKembalian() {
            return Math.max(0, this.splitPaidTotal - this.cartTotal);
        },

        openPaymentModal() {
            if (this.cart.length === 0) {
                return;
            }
            const t = this.cartTotal;
            this.paymentSplits = [
                {
                    metode: 'cash',
                    jumlah: t > 0 ? this.formatNominalInput(t) : '',
                },
            ];
            this.payModalOpen = true;
        },

        closePaymentModal() {
            this.payModalOpen = false;
        },

        addSplitRow() {
            this.paymentSplits.push({ metode: 'cash', jumlah: '' });
        },

        removeSplitRow(idx) {
            if (this.paymentSplits.length <= 1) {
                return;
            }
            this.paymentSplits.splice(idx, 1);
        },

        emojiIcon(row) {
            const n = String(row.nama_produk || '').toLowerCase();
            if (/(kopi|latte|cappuccino|espresso|americano|brew|tubruk)/.test(n)) {
                return '☕';
            }
            if (/(jeruk|jus|es |ice|teh|matcha|soda|milk)/.test(n)) {
                return '🥤';
            }
            if (/(nasi|mie|bakso|ayam|sandwich|roti|kentang|cake|keripik|wafer|snack)/.test(n)) {
                return '🥐';
            }
            if (/(air| mineral)/.test(n)) {
                return '💧';
            }
            return '📦';
        },

        categoryShort(p) {
            const c = this.categories.find((x) => Number(x.id) === Number(p.kategori_id));
            if (! c) {
                return '';
            }
            const w = String(c.nama_kategori || '').trim().split(/\s+/)[0] || '';
            return w ? w.slice(0, 8).toUpperCase() : '';
        },

        addProduct(p) {
            if (p.stok <= 0) {
                Alpine.store('toast').show('Stok habis.', 'error');
                return;
            }
            const found = this.cart.find((c) => c.product_id === p.id);
            if (found) {
                if (found.qty >= p.stok) {
                    Alpine.store('toast').show('Qty melebihi stok.', 'error');
                    return;
                }
                found.qty += 1;
            } else {
                this.cart.push({
                    product_id: p.id,
                    nama_produk: p.nama_produk,
                    harga: p.harga,
                    stok: p.stok,
                    qty: 1,
                    gambar: p.gambar,
                });
            }
            if (window.innerWidth < 1024) {
                this.cartOpen = true;
            }
        },

        inc(item) {
            const p = this.products.find((x) => x.id === item.product_id);
            if (! p) {
                return;
            }
            if (item.qty >= p.stok) {
                Alpine.store('toast').show('Qty melebihi stok.', 'error');
                return;
            }
            item.qty += 1;
        },

        dec(item) {
            item.qty -= 1;
            if (item.qty <= 0) {
                this.cart = this.cart.filter((c) => c.product_id !== item.product_id);
            }
        },

        removeItem(item) {
            this.cart = this.cart.filter((c) => c.product_id !== item.product_id);
        },

        formatRp(n) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }).format(n);
        },

        /** Hanya angka → string dengan pemisah ribuan id-ID (tanpa "Rp") untuk input nominal */
        formatNominalInput(num) {
            const n = Math.floor(Math.max(0, Number(num)));
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(n);
        },

        parseRupiahInput(val) {
            const d = String(val ?? '').replace(/\D/g, '');
            return d === '' ? 0 : Number(d);
        },

        onSplitNominalInput(row, event) {
            const digits = event.target.value.replace(/\D/g, '');
            if (digits === '') {
                row.jumlah = '';
                return;
            }
            row.jumlah = this.formatNominalInput(Number(digits));
        },

        async submitCheckout() {
            if (this.cart.length === 0) {
                return;
            }
            const splits = this.paymentSplits
                .map((row) => ({
                    metode: row.metode,
                    jumlah: Math.round(this.parseRupiahInput(row.jumlah)),
                }))
                .filter((row) => row.jumlah > 0);
            if (splits.length === 0) {
                Alpine.store('toast').show('Isi minimal satu nominal pembayaran.', 'error');
                return;
            }
            const paid = splits.reduce((s, r) => s + r.jumlah, 0);
            if (paid < this.cartTotal) {
                Alpine.store('toast').show('Total pembayaran kurang dari tagihan.', 'error');
                return;
            }

            this.paying = true;
            try {
                const res = await fetch(this.checkoutUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        items: this.cart.map((c) => ({
                            product_id: c.product_id,
                            qty: c.qty,
                        })),
                        payment_splits: splits,
                    }),
                });
                let data = {};
                try {
                    data = await res.json();
                } catch {
                    data = {};
                }
                if (! res.ok) {
                    const msg =
                        data?.message ||
                        (data?.errors ? Object.values(data.errors).flat().join(' ') : null) ||
                        'Transaksi gagal.';
                    Alpine.store('toast').show(msg, 'error');
                    return;
                }
                this.cart.forEach((line) => {
                    const p = this.products.find((x) => x.id === line.product_id);
                    if (p) {
                        p.stok -= line.qty;
                    }
                });
                this.cart = [];
                this.closePaymentModal();
                this.paymentSplits = [{ metode: 'cash', jumlah: '' }];
                Alpine.store('toast').show(data.message || 'Transaksi berhasil.', 'success');
            } catch {
                Alpine.store('toast').show('Koneksi bermasalah.', 'error');
            } finally {
                this.paying = false;
            }
        },
    };
};

/** @deprecated nama lama */
window.XiwayPos = window.StarrichPos;

window.Alpine = Alpine;

Alpine.start();

if (typeof window !== 'undefined' && 'serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register(`${window.location.origin}/sw.js`, { scope: '/' }).catch(() => {});
    });
}

import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Swal = Swal;

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
        openBillsUrl: payload.openBillsUrl || '',
        payOpenBillUrlTemplate: payload.payOpenBillUrlTemplate || '',
        deleteOpenBillUrlTemplate: payload.deleteOpenBillUrlTemplate || '',
        openBillEditDataUrlTemplate: payload.openBillEditDataUrlTemplate || '',
        updateOpenBillUrlTemplate: payload.updateOpenBillUrlTemplate || '',
        invoiceUrlTemplate: payload.invoiceUrlTemplate || '',
        openBills: payload.openBills ?? [],
        settlingBill: null,
        editingOpenBillId: null,
        csrf: payload.csrf,
        search: '',
        categoryId: '',
        cart: [],
        cartOpen: false,
        paying: false,
        orderType: 'dine',
        payModalOpen: false,
        varianModalOpen: false,
        varianModalProduct: null,
        addonsCatalog: payload.addonsCatalog ?? [],
        addonModalOpen: false,
        addonModalProduct: null,
        addonModalSelected: {},
        pendingLineProduct: null,
        pendingLineSuhu: null,
        openBillName: '',
        cashierName: '',
        isAdmin: Boolean(payload.isAdmin),
        userName: payload.userName || '',
        paymentSplits: [{ metode: 'cash', jumlah: '' }],
        productImageErrors: {},

        init() {
            queueMicrotask(() => {
                const params = new URLSearchParams(window.location.search);
                const editId = params.get('edit');
                if (
                    editId &&
                    /^\d+$/.test(editId) &&
                    this.openBillEditDataUrlTemplate &&
                    Array.isArray(this.products) &&
                    this.products.length > 0
                ) {
                    this.loadOpenBillForEdit(Number(editId));
                }
            });

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

        productCartQty(productId) {
            return this.cart
                .filter((c) => Number(c.product_id) === Number(productId))
                .reduce((sum, item) => sum + item.qty, 0);
        },

        productShowsImage(p) {
            return Boolean(p.gambar) && !this.productImageErrors[p.id];
        },

        onProductImageError(productId) {
            this.productImageErrors[productId] = true;
        },

        cashierEcho() {
            if (this.isAdmin) {
                return this.cashierName.trim() || '—';
            }

            return this.userName || '—';
        },

        get addonModalExtraPreview() {
            let sum = 0;
            for (const opt of this.addonsCatalog || []) {
                if (this.addonModalSelected[opt.code]) {
                    sum += Number(opt.harga) || 0;
                }
            }

            return sum;
        },

        get splitPaidTotal() {
            return this.paymentSplits.reduce((s, r) => s + this.parseRupiahInput(r.jumlah), 0);
        },

        get payModalTotal() {
            if (this.settlingBill) {
                return Number(this.settlingBill.total) || 0;
            }
            return this.cartTotal;
        },

        get splitKembalian() {
            return Math.max(0, this.splitPaidTotal - this.payModalTotal);
        },

        openPaymentModal() {
            if (this.cart.length === 0) {
                return;
            }
            if (this.isAdmin && ! this.cashierName.trim()) {
                Alpine.store('toast').show('Isi nama kasir yang melayani.', 'error');
                if (window.innerWidth < 1024) {
                    this.cartOpen = true;
                }
                return;
            }
            this.settlingBill = null;
            if (! this.editingOpenBillId) {
                this.openBillName = '';
            }
            this.initPaymentSplits(this.cartTotal);
            this.payModalOpen = true;
        },

        openCartDock() {
            const orderPanel = document.querySelector('.pos-kasir .order-side');
            if (orderPanel) {
                orderPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            if (this.cart.length === 0) {
                Alpine.store('toast').show('Keranjang masih kosong.', 'error');
                return;
            }
            if (window.innerWidth >= 980) {
                this.openPaymentModal();
            }
        },

        openSettleModal(bill) {
            this.settlingBill = bill;
            this.initPaymentSplits(bill.total);
            this.payModalOpen = true;
        },

        async confirmDeleteOpenBill(bill) {
            if (! bill?.id || ! this.deleteOpenBillUrlTemplate) {
                return;
            }
            const Swal = window.Swal;
            let confirmed = false;
            if (Swal) {
                const esc = (s) =>
                    String(s)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;');
                const nama = bill.nama_pelanggan ? esc(bill.nama_pelanggan) : '';
                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus open bill?',
                    html:
                        '<p style="margin:0;text-align:left;font-size:14px;color:#334155">Tagihan <strong>#' +
                        String(bill.id).padStart(5, '0') +
                        '</strong>' +
                        (nama ? ' · ' + nama : '') +
                        ' akan dihapus permanen beserta itemnya.</p>',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal-btn-danger',
                        cancelButton: 'swal-btn-ghost',
                        popup: 'swal-popup-pos',
                    },
                    focusCancel: true,
                });
                confirmed = result.isConfirmed === true;
            } else {
                confirmed = window.confirm('Hapus open bill #' + String(bill.id).padStart(5, '0') + '?');
            }
            if (! confirmed) {
                return;
            }
            this.paying = true;
            try {
                const url = this.deleteOpenBillUrlTemplate.replace('__ID__', String(bill.id));
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: this.jsonHeaders(),
                });
                const data = await this.parseJsonResponse(res);
                if (! res.ok) {
                    Alpine.store('toast').show(this.errorMessage(data, 'Gagal menghapus.'), 'error');
                    return;
                }
                if (this.editingOpenBillId && Number(this.editingOpenBillId) === Number(bill.id)) {
                    this.cancelOpenBillEdit();
                }
                this.syncOpenBills(data.open_bills);
                Alpine.store('toast').show(data.message || 'Open bill dihapus.', 'success');
            } catch {
                Alpine.store('toast').show('Koneksi bermasalah.', 'error');
            } finally {
                this.paying = false;
            }
        },

        initPaymentSplits(total) {
            const t = Number(total) || 0;
            this.paymentSplits = [
                {
                    metode: 'cash',
                    jumlah: t > 0 ? this.formatNominalInput(t) : '',
                },
            ];
        },

        closePaymentModal() {
            this.payModalOpen = false;
            this.settlingBill = null;
            if (! this.editingOpenBillId) {
                this.openBillName = '';
            }
        },

        closeVarianModal() {
            this.varianModalOpen = false;
            this.varianModalProduct = null;
        },

        pickVarianSuhu(suhu) {
            if (! this.varianModalProduct) {
                return;
            }
            const prod = this.varianModalProduct;
            const suhuNorm = suhu === 'ice' || suhu === 'hot' ? suhu : null;
            this.pendingLineProduct = prod;
            this.pendingLineSuhu = suhuNorm;
            this.closeVarianModal();
            if (prod.addon_pilihan && (this.addonsCatalog || []).length > 0) {
                this.openAddonModal(prod);
            } else {
                this.pushCartLine(prod, suhuNorm, []);
                this.clearPendingLine();
            }
        },

        openAddonModal(p) {
            this.addonModalProduct = p;
            const sel = {};
            for (const opt of this.addonsCatalog || []) {
                sel[opt.code] = false;
            }
            this.addonModalSelected = sel;
            this.addonModalOpen = true;
        },

        closeAddonModal() {
            this.addonModalOpen = false;
            this.addonModalProduct = null;
            this.addonModalSelected = {};
            this.clearPendingLine();
        },

        clearPendingLine() {
            this.pendingLineProduct = null;
            this.pendingLineSuhu = null;
        },

        confirmAddonModal() {
            const codes = [];
            for (const opt of this.addonsCatalog || []) {
                if (this.addonModalSelected[opt.code]) {
                    codes.push(opt.code);
                }
            }
            codes.sort();
            const prod = this.pendingLineProduct || this.addonModalProduct;
            if (! prod) {
                this.closeAddonModal();
                return;
            }
            const suhu = this.pendingLineSuhu;
            this.pushCartLine(prod, suhu, codes);
            this.addonModalOpen = false;
            this.addonModalProduct = null;
            this.addonModalSelected = {};
            this.clearPendingLine();
        },

        /** Baris untuk checkout / update open bill (suhu & add-on). */
        cartItemsForApi() {
            return this.cart.map((c) => {
                const row = { product_id: c.product_id, qty: c.qty };
                if (c.suhu === 'ice' || c.suhu === 'hot') {
                    row.suhu = c.suhu;
                }
                if (c.addons && c.addons.length > 0) {
                    row.addons = [...c.addons].sort();
                }

                return row;
            });
        },

        addonsKey(item) {
            const a = item.addons;
            if (! a || ! a.length) {
                return '';
            }

            return [...a].sort().join(',');
        },

        cartLineMatch(a, b) {
            return (
                Number(a.product_id) === Number(b.product_id) &&
                (a.suhu || null) === (b.suhu || null) &&
                this.addonsKey(a) === this.addonsKey(b)
            );
        },

        addonExtraForCodes(codes) {
            if (! codes?.length) {
                return 0;
            }
            let sum = 0;
            for (const code of codes) {
                const opt = (this.addonsCatalog || []).find((o) => o.code === code);
                if (opt) {
                    sum += Number(opt.harga) || 0;
                }
            }

            return sum;
        },

        formatAddonsLine(item) {
            const codes = item.addons;
            if (! codes?.length) {
                return '';
            }
            const parts = [];
            for (const code of codes) {
                const opt = (this.addonsCatalog || []).find((o) => o.code === code);
                parts.push(opt ? opt.label : code);
            }

            return parts.join(', ');
        },

        async loadOpenBillForEdit(id) {
            if (! this.openBillEditDataUrlTemplate) {
                return;
            }
            try {
                const url = this.openBillEditDataUrlTemplate.replace('__ID__', String(id));
                const res = await fetch(url, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await this.parseJsonResponse(res);
                if (! res.ok) {
                    Alpine.store('toast').show(this.errorMessage(data, 'Tidak dapat memuat open bill.'), 'error');
                    return;
                }
                const d = data.data;
                this.editingOpenBillId = d.id;
                this.openBillName = d.nama_pelanggan || '';
                this.cashierName = d.nama_kasir || '';
                this.orderType = d.order_type === 'take' ? 'take' : 'dine';
                this.cart = (d.items || []).map((row) => {
                    const pid = Number(row.product_id);
                    const product = this.products.find((p) => Number(p.id) === pid);
                    let suhu = row.suhu === 'ice' || row.suhu === 'hot' ? row.suhu : null;
                    if (product?.suhu_pilihan && ! suhu) {
                        suhu = 'hot';
                    }
                    let addons = Array.isArray(row.addons) ? row.addons.filter((x) => typeof x === 'string') : [];
                    addons = [...addons].sort();
                    if (product && ! product.addon_pilihan) {
                        addons = [];
                    }

                    return {
                        product_id: row.product_id,
                        nama_produk: row.nama_produk,
                        harga: Number(row.harga) || 0,
                        qty: Number(row.qty) || 1,
                        gambar: row.gambar || null,
                        suhu,
                        addons,
                    };
                });
                if (window.innerWidth < 1024) {
                    this.cartOpen = true;
                }
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, '', window.location.pathname);
                }
                Alpine.store('toast').show(
                    'Open bill dimuat. Tambah atau ubah item, lalu simpan atau bayar.',
                    'success',
                );
            } catch {
                Alpine.store('toast').show('Koneksi bermasalah.', 'error');
            }
        },

        validateCashierName() {
            if (! this.isAdmin) {
                return true;
            }
            if (! this.cashierName.trim()) {
                Alpine.store('toast').show('Isi nama kasir yang melayani.', 'error');
                return false;
            }
            return true;
        },

        cashierPayload() {
            if (! this.isAdmin) {
                return {};
            }
            return { nama_kasir: this.cashierName.trim() };
        },

        async saveOpenBillEdits() {
            if (! this.editingOpenBillId || this.cart.length === 0) {
                Alpine.store('toast').show('Keranjang kosong.', 'error');
                return;
            }
            if (! this.validateCashierName()) {
                return;
            }
            if (! this.updateOpenBillUrlTemplate) {
                return;
            }
            this.paying = true;
            try {
                const url = this.updateOpenBillUrlTemplate.replace('__ID__', String(this.editingOpenBillId));
                const res = await fetch(url, {
                    method: 'PUT',
                    headers: this.jsonHeaders(),
                    body: JSON.stringify({
                        order_type: this.orderType,
                        items: this.cartItemsForApi(),
                        ...this.cashierPayload(),
                    }),
                });
                const data = await this.parseJsonResponse(res);
                if (! res.ok) {
                    Alpine.store('toast').show(this.errorMessage(data, 'Gagal menyimpan.'), 'error');
                    return;
                }
                this.syncOpenBills(data.open_bills);
                Alpine.store('toast').show(data.message || 'Perubahan disimpan.', 'success');
            } catch {
                Alpine.store('toast').show('Koneksi bermasalah.', 'error');
            } finally {
                this.paying = false;
            }
        },

        cancelOpenBillEdit() {
            this.editingOpenBillId = null;
            this.cart = [];
            this.openBillName = '';
            this.cashierName = '';
            this.orderType = 'dine';
            this.payModalOpen = false;
            this.settlingBill = null;
            this.closeVarianModal();
            this.closeAddonModal();
            this.paymentSplits = [{ metode: 'cash', jumlah: '' }];
        },

        async checkoutEditedOpenBill(splits, total) {
            const billId = this.editingOpenBillId;
            if (! billId || ! this.updateOpenBillUrlTemplate || ! this.payOpenBillUrlTemplate) {
                return;
            }
            if (! this.validateCashierName()) {
                return;
            }
            this.paying = true;
            try {
                const updateUrl = this.updateOpenBillUrlTemplate.replace('__ID__', String(billId));
                const ures = await fetch(updateUrl, {
                    method: 'PUT',
                    headers: this.jsonHeaders(),
                    body: JSON.stringify({
                        order_type: this.orderType,
                        items: this.cartItemsForApi(),
                        ...this.cashierPayload(),
                    }),
                });
                const udata = await this.parseJsonResponse(ures);
                if (! ures.ok) {
                    Alpine.store('toast').show(this.errorMessage(udata, 'Gagal memperbarui tagihan.'), 'error');
                    return;
                }
                const newTotal = Number(udata?.data?.total) || total;
                const paidOk = splits.reduce((s, r) => s + r.jumlah, 0);
                if (paidOk < newTotal) {
                    this.closePaymentModal();
                    this.initPaymentSplits(newTotal);
                    this.payModalOpen = true;
                    Alpine.store('toast').show(
                        'Total berubah setelah disimpan. Sesuaikan nominal pembayaran.',
                        'error',
                    );
                    return;
                }

                const payUrl = this.payOpenBillUrlTemplate.replace('__ID__', String(billId));
                const pres = await fetch(payUrl, {
                    method: 'POST',
                    headers: this.jsonHeaders(),
                    body: JSON.stringify({ payment_splits: splits }),
                });
                const pdata = await this.parseJsonResponse(pres);
                if (! pres.ok) {
                    Alpine.store('toast').show(this.errorMessage(pdata, 'Pembayaran gagal.'), 'error');
                    return;
                }
                const trxId = pdata?.data?.transaction_id ?? billId;
                const trxTotal = pdata?.data?.total ?? newTotal;
                const bayar = pdata?.data?.bayar ?? paidOk;
                const kembalian = pdata?.data?.kembalian ?? Math.max(0, bayar - trxTotal);
                this.editingOpenBillId = null;
                this.cart = [];
                this.closePaymentModal();
                this.paymentSplits = [{ metode: 'cash', jumlah: '' }];
                this.syncOpenBills(pdata.open_bills);
                this.showSuccessAlert({ trxId, total: trxTotal, bayar, kembalian });
            } catch {
                Alpine.store('toast').show('Koneksi bermasalah.', 'error');
            } finally {
                this.paying = false;
            }
        },

        syncOpenBills(list) {
            if (Array.isArray(list)) {
                this.openBills = list;
            }
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

        categoryLabel(p) {
            const c = this.categories.find((x) => Number(x.id) === Number(p.kategori_id));
            if (! c) {
                return '';
            }

            return String(c.nama_kategori || '').trim().toUpperCase();
        },

        categoryShort(p) {
            return this.categoryLabel(p);
        },

        addProduct(p) {
            if (p.suhu_pilihan) {
                this.varianModalProduct = p;
                this.varianModalOpen = true;

                return;
            }
            if (p.addon_pilihan && (this.addonsCatalog || []).length > 0) {
                this.pendingLineProduct = p;
                this.pendingLineSuhu = null;
                this.openAddonModal(p);

                return;
            }
            this.pushCartLine(p, null, []);
        },

        pushCartLine(p, suhu, addons) {
            const list = Array.isArray(addons) ? addons : [];
            const suhuNorm = suhu === 'ice' || suhu === 'hot' ? suhu : null;
            const addonsNorm = [...new Set(list.filter((x) => typeof x === 'string'))].sort();
            const unitExtra = this.addonExtraForCodes(addonsNorm);
            const unitHarga = Number(p.harga) + unitExtra;
            const found = this.cart.find(
                (c) =>
                    Number(c.product_id) === Number(p.id) &&
                    (c.suhu || null) === suhuNorm &&
                    this.addonsKey(c) === addonsNorm.join(','),
            );
            if (found) {
                found.qty += 1;
            } else {
                this.cart.push({
                    product_id: p.id,
                    nama_produk: p.nama_produk,
                    harga: unitHarga,
                    qty: 1,
                    gambar: p.gambar,
                    suhu: suhuNorm,
                    addons: addonsNorm,
                });
            }
            if (window.innerWidth < 1024) {
                this.cartOpen = true;
            }
        },

        inc(item) {
            item.qty += 1;
        },

        dec(item) {
            item.qty -= 1;
            if (item.qty <= 0) {
                this.cart = this.cart.filter((c) => ! this.cartLineMatch(c, item));
            }
        },

        removeItem(item) {
            this.cart = this.cart.filter((c) => ! this.cartLineMatch(c, item));
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

        buildPaymentSplits() {
            return this.paymentSplits
                .map((row) => ({
                    metode: row.metode,
                    jumlah: Math.round(this.parseRupiahInput(row.jumlah)),
                }))
                .filter((row) => row.jumlah > 0);
        },

        validatePaymentSplits(splits, total) {
            if (splits.length === 0) {
                Alpine.store('toast').show('Isi minimal satu nominal pembayaran.', 'error');
                return false;
            }
            const paid = splits.reduce((s, r) => s + r.jumlah, 0);
            if (paid < total) {
                Alpine.store('toast').show('Total pembayaran kurang dari tagihan.', 'error');
                return false;
            }
            return true;
        },

        async submitOpenBill() {
            if (this.editingOpenBillId) {
                return;
            }
            if (this.settlingBill || this.cart.length === 0) {
                return;
            }

            const nama = this.openBillName.trim();
            if (! nama) {
                Alpine.store('toast').show('Isi nama pelanggan untuk open bill.', 'error');
                return;
            }
            if (! this.validateCashierName()) {
                return;
            }

            this.paying = true;
            try {
                const res = await fetch(this.checkoutUrl, {
                    method: 'POST',
                    headers: this.jsonHeaders(),
                    body: JSON.stringify({
                        action: 'open_bill',
                        order_type: this.orderType,
                        nama_pelanggan: nama,
                        items: this.cartItemsForApi(),
                        ...this.cashierPayload(),
                    }),
                });
                const data = await this.parseJsonResponse(res);
                if (! res.ok) {
                    Alpine.store('toast').show(this.errorMessage(data, 'Gagal menyimpan open bill.'), 'error');
                    return;
                }
                this.cart = [];
                this.closePaymentModal();
                this.openBillName = '';
                this.paymentSplits = [{ metode: 'cash', jumlah: '' }];
                this.syncOpenBills(data.open_bills);
                Alpine.store('toast').show(data.message || 'Open bill disimpan.', 'success');
            } catch {
                Alpine.store('toast').show('Koneksi bermasalah.', 'error');
            } finally {
                this.paying = false;
            }
        },

        async submitCheckout() {
            const splits = this.buildPaymentSplits();
            const total = this.payModalTotal;

            if (! this.validatePaymentSplits(splits, total)) {
                return;
            }

            if (! this.validateCashierName()) {
                return;
            }

            const paid = splits.reduce((s, r) => s + r.jumlah, 0);

            if (this.settlingBill) {
                await this.paySettlingBill(splits, total, paid);
                return;
            }

            if (this.editingOpenBillId) {
                await this.checkoutEditedOpenBill(splits, total);
                return;
            }

            if (this.cart.length === 0) {
                return;
            }

            this.paying = true;
            try {
                const res = await fetch(this.checkoutUrl, {
                    method: 'POST',
                    headers: this.jsonHeaders(),
                    body: JSON.stringify({
                        action: 'pay',
                        order_type: this.orderType,
                        items: this.cartItemsForApi(),
                        payment_splits: splits,
                        ...this.cashierPayload(),
                    }),
                });
                const data = await this.parseJsonResponse(res);
                if (! res.ok) {
                    Alpine.store('toast').show(this.errorMessage(data, 'Transaksi gagal.'), 'error');
                    return;
                }
                const trxId = data?.data?.transaction_id;
                const trxTotal = data?.data?.total ?? total;
                const bayar = data?.data?.bayar ?? paid;
                const kembalian = data?.data?.kembalian ?? Math.max(0, bayar - trxTotal);
                this.cart = [];
                this.closePaymentModal();
                this.paymentSplits = [{ metode: 'cash', jumlah: '' }];
                this.syncOpenBills(data.open_bills);
                this.showSuccessAlert({ trxId, total: trxTotal, bayar, kembalian });
            } catch {
                Alpine.store('toast').show('Koneksi bermasalah.', 'error');
            } finally {
                this.paying = false;
            }
        },

        async paySettlingBill(splits, total, paid) {
            const billId = this.settlingBill?.id;
            if (! billId || ! this.payOpenBillUrlTemplate) {
                return;
            }

            this.paying = true;
            try {
                const url = this.payOpenBillUrlTemplate.replace('__ID__', String(billId));
                const res = await fetch(url, {
                    method: 'POST',
                    headers: this.jsonHeaders(),
                    body: JSON.stringify({ payment_splits: splits }),
                });
                const data = await this.parseJsonResponse(res);
                if (! res.ok) {
                    Alpine.store('toast').show(this.errorMessage(data, 'Pembayaran gagal.'), 'error');
                    return;
                }
                const trxId = data?.data?.transaction_id ?? billId;
                const trxTotal = data?.data?.total ?? total;
                const bayar = data?.data?.bayar ?? paid;
                const kembalian = data?.data?.kembalian ?? Math.max(0, bayar - trxTotal);
                this.closePaymentModal();
                this.paymentSplits = [{ metode: 'cash', jumlah: '' }];
                this.openBills = this.openBills.filter((b) => b.id !== billId);
                this.syncOpenBills(data.open_bills);
                this.showSuccessAlert({ trxId, total: trxTotal, bayar, kembalian });
            } catch {
                Alpine.store('toast').show('Koneksi bermasalah.', 'error');
            } finally {
                this.paying = false;
            }
        },

        jsonHeaders() {
            return {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'X-Requested-With': 'XMLHttpRequest',
            };
        },

        async parseJsonResponse(res) {
            try {
                return await res.json();
            } catch {
                return {};
            }
        },

        errorMessage(data, fallback) {
            return (
                data?.message ||
                (data?.errors ? Object.values(data.errors).flat().join(' ') : null) ||
                fallback
            );
        },

        showSuccessAlert({ trxId, total, bayar, kembalian }) {
            const Swal = window.Swal;
            if (! Swal) {
                Alpine.store('toast').show('Transaksi berhasil.', 'success');
                return;
            }
            const fmt = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(n) || 0);
            const invoiceUrl = trxId && this.invoiceUrlTemplate
                ? this.invoiceUrlTemplate.replace('__ID__', trxId)
                : null;

            Swal.fire({
                icon: 'success',
                title: 'Transaksi Berhasil',
                html: `
                    <div style="text-align:left;margin-top:8px;font-size:14px;color:#334155">
                        ${trxId ? `<div style="display:flex;justify-content:space-between;padding:4px 0"><span>No. Transaksi</span><strong>#${String(trxId).padStart(5, '0')}</strong></div>` : ''}
                        <div style="display:flex;justify-content:space-between;padding:4px 0;border-top:1px dashed #cbd5e1;margin-top:4px">
                            <span>Total</span><strong>${fmt(total)}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:4px 0"><span>Bayar</span><strong>${fmt(bayar)}</strong></div>
                        <div style="display:flex;justify-content:space-between;padding:4px 0;border-top:1px dashed #cbd5e1;margin-top:4px">
                            <span>Kembalian</span><strong style="color:#16a34a">${fmt(kembalian)}</strong>
                        </div>
                    </div>
                `,
                showCancelButton: !! invoiceUrl,
                showCloseButton: true,
                confirmButtonText: 'Selesai',
                cancelButtonText: 'Cetak invoice',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'swal-btn-primary',
                    cancelButton: 'swal-btn-ghost',
                    popup: 'swal-popup-pos',
                },
                allowEnterKey: true,
                focusConfirm: true,
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel && invoiceUrl) {
                    window.open(invoiceUrl + '?print=1', '_blank', 'noopener,width=420,height=720');
                }
            });
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

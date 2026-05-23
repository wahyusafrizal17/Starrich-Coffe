/**
 * RawBT thermal print (ESC/POS base64) — untuk WebView Android + Panda Bluetooth.
 *
 * Laravel mengirim `escpos_base64` (byte ESC/POS sudah di-encode).
 * Tidak memakai window.print(), PDF, atau render HTML.
 */

const RAWBT_SCHEME = 'rawbt:base64,';
const MIN_GAP_MS = 450;
const RETRY_DELAY_MS = 900;
const MAX_RETRIES = 1;

let printQueue = Promise.resolve();
let lastPrintAt = 0;

/**
 * Deteksi lingkungan Android WebView (kasir tablet).
 */
export function isRawBtEnvironment() {
    if (typeof navigator === 'undefined') {
        return false;
    }
    const ua = navigator.userAgent || '';
    const isAndroid = /Android/i.test(ua);
    if (! isAndroid) {
        return false;
    }
    if (window.StarrichAndroidRawBt === true) {
        return true;
    }
    if (typeof window.AndroidBridge !== 'undefined') {
        return true;
    }

    return /;\s*wv\)|WebView/i.test(ua);
}

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

/**
 * Kirim base64 ESC/POS ke RawBT.
 * Utama: iframe tersembunyi (halaman POS tidak pindah).
 * Cadangan: window.location.href (sesuai dokumentasi RawBT).
 */
function dispatchRawBt(base64Payload, useNavigate = false) {
    const payload = String(base64Payload || '').replace(/^rawbt:base64,/, '');
    if (! payload) {
        return false;
    }

    const url = RAWBT_SCHEME + payload;

    if (useNavigate) {
        window.location.href = url;

        return true;
    }

    try {
        const iframe = document.createElement('iframe');
        iframe.setAttribute('aria-hidden', 'true');
        iframe.style.cssText = 'position:fixed;width:0;height:0;border:0;opacity:0;pointer-events:none';
        iframe.src = url;
        document.body.appendChild(iframe);
        window.setTimeout(() => {
            try {
                iframe.remove();
            } catch {
                /* ignore */
            }
        }, 2000);

        return true;
    } catch {
        window.location.href = url;

        return true;
    }
}

/**
 * Cetak struk dari base64 ESC/POS (antrian + jeda Bluetooth + 1x retry).
 *
 * @param {string} escposBase64
 * @param {{ navigate?: boolean }} [options]
 * @returns {Promise<boolean>}
 */
export function printReceipt(escposBase64, options = {}) {
    if (! escposBase64) {
        return Promise.resolve(false);
    }

    printQueue = printQueue
        .then(() => sendWithQueue(escposBase64, options))
        .catch((err) => {
            console.warn('[RawBT] print failed', err);

            return false;
        });

    return printQueue;
}

async function sendWithQueue(escposBase64, options) {
    const gap = Math.max(0, MIN_GAP_MS - (Date.now() - lastPrintAt));
    if (gap > 0) {
        await sleep(gap);
    }

    let ok = dispatchRawBt(escposBase64, Boolean(options.navigate));
    lastPrintAt = Date.now();

    if (! ok && MAX_RETRIES > 0) {
        await sleep(RETRY_DELAY_MS);
        ok = dispatchRawBt(escposBase64, true);
        lastPrintAt = Date.now();
    }

    return ok;
}

/**
 * Ambil ESC/POS dari API Laravel lalu cetak.
 *
 * @param {string} url
 * @returns {Promise<boolean>}
 */
export async function fetchAndPrintReceipt(url) {
    if (! url) {
        return false;
    }
    try {
        const res = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        const data = await res.json();
        if (! res.ok || ! data?.escpos_base64) {
            return false;
        }

        return printReceipt(data.escpos_base64);
    } catch (err) {
        console.warn('[RawBT] fetch receipt failed', err);

        return false;
    }
}

/**
 * Setelah bayar: cetak otomatis jika di Android WebView.
 *
 * @param {{ escpos_base64?: string, transaction_id?: number }} receipt
 * @param {{ receiptEscPosUrlTemplate?: string, autoPrint?: boolean }} ctx
 */
export async function autoPrintAfterPayment(receipt, ctx = {}) {
    const auto = ctx.autoPrint !== false;
    if (! auto) {
        return false;
    }

    if (receipt?.escpos_base64) {
        return printReceipt(receipt.escpos_base64);
    }

    const trxId = receipt?.transaction_id;
    const tpl = ctx.receiptEscPosUrlTemplate;
    if (trxId && tpl) {
        const url = tpl.replace('__ID__', String(trxId));

        return fetchAndPrintReceipt(url);
    }

    return false;
}

window.StarrichRawBt = {
    isRawBtEnvironment,
    printReceipt,
    fetchAndPrintReceipt,
    autoPrintAfterPayment,
};

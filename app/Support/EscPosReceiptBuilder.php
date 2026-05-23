<?php

namespace App\Support;

use App\Models\Transaction;
use App\Models\TransactionDetail;

/**
 * Membangun byte ESC/POS untuk thermal printer (RawBT / Panda 58–80mm).
 */
class EscPosReceiptBuilder
{
    private string $buffer = '';

    public function __construct(
        private readonly int $charsPerLine = 32,
        private readonly string $storeName = 'Starrich',
        private readonly string $footerLine = 'Terima kasih sudah ngopi!',
    ) {}

    public static function fromConfig(): self
    {
        $width = config('receipt.paper_width', '58');

        return new self(
            charsPerLine: $width === '80' ? 48 : 32,
            storeName: (string) config('receipt.store_name', config('app.name', 'Starrich')),
            footerLine: (string) config('receipt.footer_line', 'Terima kasih!'),
        );
    }

    public function build(Transaction $transaction): string
    {
        $transaction->loadMissing(['user', 'details.product']);

        $this->buffer = '';
        $this->init();

        $this->alignCenter();
        $this->bold(true);
        $this->textLine($this->storeName);
        $this->bold(false);
        $this->lf();

        $this->separator();
        $this->alignLeft();

        $this->twoCols('No. Transaksi', '#'.str_pad((string) $transaction->id, 5, '0', STR_PAD_LEFT));
        $this->twoCols('Tanggal', $transaction->created_at?->format('d/m/Y H:i') ?? '-');
        $this->twoCols('Kasir', $transaction->user?->name ?? '-');

        if ($transaction->nama_pelanggan) {
            $this->twoCols('Pelanggan', $transaction->nama_pelanggan);
        }

        if ($transaction->order_type) {
            $tipe = $transaction->order_type === 'take' ? 'Take Away' : 'Dine In';
            $this->twoCols('Tipe', $tipe);
        }

        $this->separator();

        foreach ($transaction->details as $detail) {
            $this->printDetailLine($detail);
        }

        $this->separator();

        $this->twoCols('Subtotal', $this->money($transaction->total));

        $this->alignCenter();
        $this->bold(true);
        $this->twoCols('TOTAL', $this->money($transaction->total, true));
        $this->bold(false);
        $this->alignLeft();

        if ($transaction->isOpen()) {
            $this->lf();
            $this->alignCenter();
            $this->bold(true);
            $this->textLine('BELUM LUNAS');
            $this->textLine('(OPEN BILL)');
            $this->bold(false);
            $this->alignLeft();
        } else {
            $this->twoCols('Bayar', $this->money($transaction->bayar, true));
            $this->twoCols('Kembalian', $this->money($transaction->kembalian, true));

            if (is_array($transaction->payment_splits) && $transaction->payment_splits !== []) {
                $this->separator();
                $this->textLine('Metode pembayaran:');
                foreach ($transaction->payment_splits as $split) {
                    $metode = ucfirst((string) ($split['metode'] ?? '-'));
                    $jumlah = (int) ($split['jumlah'] ?? 0);
                    $this->twoCols($metode, $this->money($jumlah, true));
                }
            }
        }

        $this->separator();
        $this->alignCenter();
        $this->textLine($this->footerLine);
        $this->textLine('Sampai jumpa lagi.');
        $this->lf(2);

        $this->cut();

        return $this->buffer;
    }

    public function buildBase64(Transaction $transaction): string
    {
        return base64_encode($this->build($transaction));
    }

    private function printDetailLine(TransactionDetail $detail): void
    {
        $name = $detail->product?->nama_produk ?? 'Produk';
        if ($detail->suhu === 'ice') {
            $name .= ' (Ice)';
        } elseif ($detail->suhu === 'hot') {
            $name .= ' (Hot)';
        }

        $addonLine = OrderAddonCatalog::labelsLine($detail->addons ?? []);
        if ($addonLine !== '') {
            $name .= ' + '.$addonLine;
        }

        foreach ($this->wrapText($name) as $line) {
            $this->textLine($line);
        }

        $qty = (int) $detail->qty;
        $harga = (int) $detail->harga;
        $sub = (int) $detail->subtotal;
        $this->twoCols(
            $qty.' x '.$this->moneyPlain($harga),
            $this->moneyPlain($sub)
        );
        $this->lf();
    }

    private function init(): void
    {
        $this->buffer .= "\x1B\x40";
    }

    private function alignLeft(): void
    {
        $this->buffer .= "\x1B\x61\x00";
    }

    private function alignCenter(): void
    {
        $this->buffer .= "\x1B\x61\x01";
    }

    private function bold(bool $on): void
    {
        $this->buffer .= $on ? "\x1B\x45\x01" : "\x1B\x45\x00";
    }

    private function text(string $text): void
    {
        $this->buffer .= $this->sanitize($text);
    }

    private function textLine(string $text): void
    {
        $this->text($text);
        $this->buffer .= "\n";
    }

    private function lf(int $lines = 1): void
    {
        $this->buffer .= str_repeat("\n", max(1, $lines));
    }

    private function separator(): void
    {
        $this->textLine(str_repeat('-', $this->charsPerLine));
    }

    /** Partial cut + feed (umum di Panda / ESC/POS). */
    private function cut(): void
    {
        $this->buffer .= "\x1D\x56\x41\x03";
    }

    private function twoCols(string $left, string $right): void
    {
        $left = $this->sanitize($left);
        $right = $this->sanitize($right);
        $leftLen = $this->displayWidth($left);
        $rightLen = $this->displayWidth($right);
        $space = $this->charsPerLine - $leftLen - $rightLen;

        if ($space < 1) {
            $this->textLine($left);
            $this->textLine($right);

            return;
        }

        $this->textLine($left.str_repeat(' ', $space).$right);
    }

    /** @return list<string> */
    private function wrapText(string $text): array
    {
        $text = $this->sanitize($text);
        $words = preg_split('/\s+/', $text) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            if ($this->displayWidth($candidate) <= $this->charsPerLine) {
                $current = $candidate;
            } else {
                if ($current !== '') {
                    $lines[] = $current;
                }
                $current = $this->displayWidth($word) > $this->charsPerLine
                    ? mb_substr($word, 0, $this->charsPerLine)
                    : $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines === [] ? [''] : $lines;
    }

    private function money(int $amount, bool $withPrefix = false): string
    {
        $formatted = number_format($amount, 0, ',', '.');

        return $withPrefix ? 'Rp '.$formatted : $formatted;
    }

    private function moneyPlain(int $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }

    private function sanitize(string $text): string
    {
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);
        $text = preg_replace('/[^\P{C}\n]+/u', '', $text) ?? $text;

        return trim($text);
    }

    private function displayWidth(string $text): int
    {
        return mb_strlen($text);
    }
}

<?php

namespace App\Support;

use App\Models\Discount;
use App\Models\Product;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DiscountResolver
{
    /** @var Collection<int, Discount>|null */
    private ?Collection $cache = null;

    public function warm(?CarbonInterface $at = null): self
    {
        $this->cache = Discount::query()
            ->active()
            ->with(['product', 'category'])
            ->orderByDesc('id')
            ->get()
            ->filter(fn (Discount $d) => $d->isValidAt($at ?? now()))
            ->values();

        return $this;
    }

    /** @return Collection<int, Discount> */
    private function discounts(?CarbonInterface $at = null): Collection
    {
        if ($this->cache === null) {
            $this->warm($at);
        }

        return $this->cache ?? collect();
    }

    /** Potongan per unit untuk harga produk (tanpa addon). */
    public function itemDiscountAmount(Product $product, ?CarbonInterface $at = null): int
    {
        $base = (int) $product->harga;
        $best = 0;

        foreach ($this->discounts($at) as $discount) {
            if (! $discount->isItemLevel()) {
                continue;
            }
            if (! $discount->appliesToProduct($product)) {
                continue;
            }

            $best = max($best, $discount->computeAmount($base));
        }

        return min($base, $best);
    }

    public function hargaJual(Product $product, ?CarbonInterface $at = null): int
    {
        return max(0, (int) $product->harga - $this->itemDiscountAmount($product, $at));
    }

    /**
     * Promo keranjang terbaik yang memenuhi syarat.
     *
     * @return array{id: int, nama: string, jenis: string, jumlah: int}|null
     */
    public function bestCartPromo(int $subtotal, ?CarbonInterface $at = null): ?array
    {
        $subtotal = max(0, $subtotal);
        $best = null;
        $bestAmount = 0;

        foreach ($this->discounts($at) as $discount) {
            if (! $discount->isCartLevel()) {
                continue;
            }

            $min = (int) ($discount->min_belanja ?? 0);
            if ($min > 0 && $subtotal < $min) {
                continue;
            }

            $amount = $discount->computeAmount($subtotal);
            if ($amount > $bestAmount) {
                $bestAmount = $amount;
                $best = [
                    'id' => $discount->id,
                    'nama' => $discount->nama,
                    'jenis' => $discount->jenis,
                    'jumlah' => $amount,
                ];
            }
        }

        return $best;
    }

    /**
     * Katalog promo keranjang untuk frontend kasir.
     *
     * @return list<array{id: int, nama: string, jenis: string, jenis_label: string, tipe_nilai: string, jumlah: int, min_belanja: int|null}>
     */
    public function cartPromoCatalog(?CarbonInterface $at = null): array
    {
        return $this->discounts($at)
            ->filter(fn (Discount $d) => $d->isCartLevel())
            ->map(fn (Discount $d) => [
                'id' => $d->id,
                'nama' => $d->nama,
                'jenis' => $d->jenis,
                'jenis_label' => $d->jenisLabel(),
                'tipe_nilai' => $d->tipe_nilai,
                'jumlah' => (int) $d->jumlah,
                'min_belanja' => $d->min_belanja !== null ? (int) $d->min_belanja : null,
            ])
            ->values()
            ->all();
    }
}

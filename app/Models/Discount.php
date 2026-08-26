<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Discount extends Model
{
    public const JENIS_PRODUCT = 'product';

    public const JENIS_CATEGORY = 'category';

    public const JENIS_MIN_PURCHASE = 'min_purchase';

    public const JENIS_EVENT = 'event';

    public const JENIS_HAPPY_HOUR = 'happy_hour';

    public const TIPE_AMOUNT = 'amount';

    public const TIPE_PERCENT = 'percent';

    /** @var array<string, string> */
    public const JENIS_LABELS = [
        self::JENIS_PRODUCT => 'Diskon produk',
        self::JENIS_CATEGORY => 'Diskon kategori',
        self::JENIS_MIN_PURCHASE => 'Minimal belanja',
        self::JENIS_EVENT => 'Diskon event',
        self::JENIS_HAPPY_HOUR => 'Happy hour',
    ];

    protected $fillable = [
        'nama',
        'jenis',
        'tipe_nilai',
        'product_id',
        'category_id',
        'jumlah',
        'min_belanja',
        'starts_at',
        'ends_at',
        'jam_mulai',
        'jam_selesai',
        'hari_aktif',
        'is_active',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'min_belanja' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'hari_aktif' => 'array',
        ];
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @param Builder<Discount> $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function jenisLabel(): string
    {
        return self::JENIS_LABELS[$this->jenis] ?? $this->jenis;
    }

    public function isItemLevel(): bool
    {
        return match ($this->jenis) {
            self::JENIS_PRODUCT, self::JENIS_CATEGORY => true,
            self::JENIS_HAPPY_HOUR, self::JENIS_EVENT => $this->product_id !== null || $this->category_id !== null,
            default => false,
        };
    }

    public function isCartLevel(): bool
    {
        return match ($this->jenis) {
            self::JENIS_MIN_PURCHASE => true,
            self::JENIS_HAPPY_HOUR, self::JENIS_EVENT => $this->product_id === null && $this->category_id === null,
            default => false,
        };
    }

    public function isValidAt(?CarbonInterface $at = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $at = $at ? Carbon::instance($at) : now();

        if ($this->starts_at && $at->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $at->gt($this->ends_at)) {
            return false;
        }

        if ($this->jenis === self::JENIS_HAPPY_HOUR || ($this->jam_mulai && $this->jam_selesai)) {
            $days = $this->hari_aktif;
            if (is_array($days) && $days !== []) {
                // Carbon: 0=Sunday … 6=Saturday — simpan sama di form.
                if (! in_array((int) $at->dayOfWeek, array_map('intval', $days), true)) {
                    return false;
                }
            }

            if ($this->jam_mulai && $this->jam_selesai) {
                $time = $at->format('H:i:s');
                $start = $this->normalizeTime($this->jam_mulai);
                $end = $this->normalizeTime($this->jam_selesai);

                if ($start <= $end) {
                    if ($time < $start || $time > $end) {
                        return false;
                    }
                } elseif ($time < $start && $time > $end) {
                    // Melewati tengah malam, mis. 22:00–02:00
                    return false;
                }
            }
        }

        return true;
    }

    public function appliesToProduct(Product $product): bool
    {
        if ($this->product_id !== null) {
            return (int) $this->product_id === (int) $product->id;
        }

        if ($this->category_id !== null) {
            return (int) $this->category_id === (int) $product->kategori_id;
        }

        if ($this->jenis === self::JENIS_CATEGORY) {
            return false;
        }

        // Happy hour / event tanpa target = semua produk (item-level hanya jika scoped; cart-level handled separately)
        return $this->jenis === self::JENIS_PRODUCT ? false : true;
    }

    /** Hitung potongan dari basis harga/subtotal. */
    public function computeAmount(int $base): int
    {
        $base = max(0, $base);
        if ($base === 0 || $this->jumlah <= 0) {
            return 0;
        }

        if ($this->tipe_nilai === self::TIPE_PERCENT) {
            $pct = min(100, max(0, $this->jumlah));

            return (int) floor($base * $pct / 100);
        }

        return min($base, max(0, $this->jumlah));
    }

    public function nilaiLabel(): string
    {
        if ($this->tipe_nilai === self::TIPE_PERCENT) {
            return $this->jumlah.'%';
        }

        return 'Rp '.number_format($this->jumlah, 0, ',', '.');
    }

    private function normalizeTime(mixed $value): string
    {
        if ($value instanceof CarbonInterface) {
            return $value->format('H:i:s');
        }

        $raw = trim((string) $value);
        if (preg_match('/^\d{2}:\d{2}$/', $raw)) {
            return $raw.':00';
        }

        return $raw;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    public const STATUS_PAID = 'paid';

    public const STATUS_OPEN = 'open';

    protected $fillable = [
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'payment_splits',
        'user_id',
        'status',
        'order_type',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'bayar' => 'integer',
            'kembalian' => 'integer',
            'payment_splits' => 'array',
        ];
    }

    protected $attributes = [
        'status' => self::STATUS_PAID,
    ];

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /** @param Builder<Transaction> $query */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /** @param Builder<Transaction> $query */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<TransactionDetail, $this> */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /** @return array<string, mixed> */
    public function toOpenBillArray(): array
    {
        $this->loadMissing('details.product');

        return [
            'id' => $this->id,
            'total' => $this->total,
            'order_type' => $this->order_type,
            'created_at' => $this->created_at?->toIso8601String(),
            'items_count' => (int) $this->details->sum('qty'),
            'items_preview' => $this->details
                ->take(3)
                ->map(fn (TransactionDetail $d) => $d->product?->nama_produk ?? '—')
                ->values()
                ->all(),
        ];
    }
}

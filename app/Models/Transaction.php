<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'payment_splits',
        'user_id',
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
}

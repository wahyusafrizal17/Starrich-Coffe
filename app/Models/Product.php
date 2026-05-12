<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'nama_produk',
        'harga',
        'kategori_id',
        'gambar',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
        ];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    /** @return HasMany<TransactionDetail, $this> */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'product_id');
    }

    public function imageUrl(): ?string
    {
        return $this->gambar ? asset('uploads/'.$this->gambar) : null;
    }
}

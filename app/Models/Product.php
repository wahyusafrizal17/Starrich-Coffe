<?php

namespace App\Models;

use App\Support\DiscountResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /** Diskon khusus jenis produk (satu per produk). */
    /** @return HasOne<Discount, $this> */
    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class)->where('jenis', Discount::JENIS_PRODUCT);
    }

    /** @return HasOne<Discount, $this> */
    public function activeDiscount(): HasOne
    {
        return $this->hasOne(Discount::class)
            ->where('jenis', Discount::JENIS_PRODUCT)
            ->where('is_active', true);
    }

    public function imageUrl(): ?string
    {
        return $this->gambar ? asset('uploads/'.$this->gambar) : null;
    }

    public function diskonAmount(): int
    {
        return app(DiscountResolver::class)->itemDiscountAmount($this);
    }

    /** Harga jual setelah diskon item aktif (minimal 0). */
    public function hargaJual(): int
    {
        return app(DiscountResolver::class)->hargaJual($this);
    }

    public function hasDiskon(): bool
    {
        return $this->diskonAmount() > 0;
    }

    /** Menu minuman/kopi: perlu pilih Ice/Hot di kasir. */
    public function requiresSuhuPilihan(): bool
    {
        $nama = mb_strtolower($this->category?->nama_kategori ?? '');

        return (bool) preg_match(
            '/minuman|kopi|coffee|teh|latte|espresso|brew|jus|juice|soda|matcha|milk|drink|mocktail|mocha|frappe|shake|americano|cappuccino|bubble/i',
            $nama
        );
    }

    /** Biji / susu tambahan — sama cakupan kategori dengan suhu (minuman/kopi). */
    public function allowsOrderAddons(): bool
    {
        return $this->requiresSuhuPilihan();
    }
}

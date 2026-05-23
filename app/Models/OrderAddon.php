<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrderAddon extends Model
{
    protected $fillable = [
        'kode',
        'label',
        'harga',
        'urutan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @param Builder<OrderAddon> $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** @param Builder<OrderAddon> $query */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('label');
    }
}

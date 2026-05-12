<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    public const CATEGORY_RENT = 'rent';
    public const CATEGORY_MAINTENANCE = 'maintenance';
    public const CATEGORY_UTILITY = 'utility';
    public const CATEGORY_SALARY = 'salary';
    public const CATEGORY_SUPPLIES = 'supplies';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'tanggal',
        'kategori',
        'nama',
        'jumlah',
        'catatan',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'integer',
        ];
    }

    /** @return array<string, string> */
    public static function categories(): array
    {
        return [
            self::CATEGORY_RENT => 'Sewa Tempat',
            self::CATEGORY_MAINTENANCE => 'Maintenance Mesin',
            self::CATEGORY_UTILITY => 'Utilitas (Listrik/Air/Internet)',
            self::CATEGORY_SALARY => 'Gaji Karyawan',
            self::CATEGORY_SUPPLIES => 'Bahan Baku & Perlengkapan',
            self::CATEGORY_OTHER => 'Lainnya',
        ];
    }

    public function getKategoriLabelAttribute(): string
    {
        return self::categories()[$this->kategori] ?? $this->kategori;
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

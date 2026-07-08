<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapitalInflow extends Model
{
    public const CATEGORY_CARRYOVER = 'carryover';
    public const CATEGORY_ADDITION = 'addition';
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
            self::CATEGORY_CARRYOVER => 'Modal bulan sebelumnya',
            self::CATEGORY_ADDITION => 'Tambahan modal',
            self::CATEGORY_OTHER => 'Pemasukan lainnya',
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

<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = [
        'nama',
        'tanggal_perolehan',
        'harga_perolehan',
        'nilai_sisa',
        'masa_manfaat_bulan',
        'catatan',
        'user_id',
    ];

    /**
     * Default nilai sisa 0 dan masa manfaat 60 bulan (5 tahun)
     * untuk perhitungan depresiasi garis lurus standar peralatan kafe.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'nilai_sisa' => 0,
        'masa_manfaat_bulan' => 60,
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perolehan' => 'date',
            'harga_perolehan' => 'integer',
            'nilai_sisa' => 'integer',
            'masa_manfaat_bulan' => 'integer',
        ];
    }

    /**
     * Depresiasi per bulan menggunakan metode garis lurus:
     * (Harga Perolehan - Nilai Sisa) / Masa Manfaat (bulan).
     */
    public function monthlyDepreciation(): int
    {
        $months = max(1, (int) $this->masa_manfaat_bulan);
        $base = max(0, (int) $this->harga_perolehan - (int) $this->nilai_sisa);

        return (int) floor($base / $months);
    }

    /**
     * Total depresiasi untuk rentang tanggal tertentu.
     * Hanya menghitung bulan yang masuk dalam periode aktif aset
     * (mulai dari bulan perolehan hingga akhir masa manfaat).
     */
    public function depreciationFor(CarbonInterface $from, CarbonInterface $to): int
    {
        $perMonth = $this->monthlyDepreciation();
        if ($perMonth <= 0) {
            return 0;
        }

        $acquisition = Carbon::parse($this->tanggal_perolehan)->startOfMonth();
        $endOfUsefulLife = $acquisition->copy()->addMonths((int) $this->masa_manfaat_bulan)->subDay();

        $rangeStart = Carbon::parse($from)->startOfMonth();
        $rangeEnd = Carbon::parse($to)->endOfMonth();

        $effectiveStart = $rangeStart->greaterThan($acquisition) ? $rangeStart : $acquisition;
        $effectiveEnd = $rangeEnd->lessThan($endOfUsefulLife) ? $rangeEnd : $endOfUsefulLife;

        if ($effectiveStart->greaterThan($effectiveEnd)) {
            return 0;
        }

        $months = $effectiveStart->diffInMonths($effectiveEnd->copy()->endOfMonth()) + 1;

        $accumulatedCap = max(0, (int) $this->harga_perolehan - (int) $this->nilai_sisa);
        $total = $perMonth * $months;

        return (int) min($total, $accumulatedCap);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

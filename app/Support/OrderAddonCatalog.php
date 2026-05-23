<?php

namespace App\Support;

use App\Models\OrderAddon;
use Illuminate\Support\Facades\Schema;

class OrderAddonCatalog
{
    /** @return array<string, array{label: string, harga: int}> */
    public static function definitions(): array
    {
        if (! Schema::hasTable('order_addons')) {
            return config('order_addons.items', []);
        }

        return OrderAddon::query()
            ->active()
            ->ordered()
            ->get()
            ->mapWithKeys(fn (OrderAddon $a) => [
                $a->kode => [
                    'label' => $a->label,
                    'harga' => (int) $a->harga,
                ],
            ])
            ->all();
    }

    /** @return list<string> */
    public static function validCodes(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * @param  array<int, mixed>|null  $codes
     * @return list<string>
     */
    public static function normalize(?array $codes): array
    {
        if ($codes === null || $codes === []) {
            return [];
        }

        $allowed = self::validCodes();
        $out = [];
        foreach ($codes as $c) {
            if (is_string($c) && in_array($c, $allowed, true) && ! in_array($c, $out, true)) {
                $out[] = $c;
            }
        }
        sort($out);

        return $out;
    }

    /** @param  list<string>  $normalizedCodes */
    public static function extraPriceForCodes(array $normalizedCodes): int
    {
        $sum = 0;
        foreach ($normalizedCodes as $c) {
            $sum += self::priceForCode($c);
        }

        return $sum;
    }

    /** @param  list<string>  $normalizedCodes */
    public static function labelsLine(array $normalizedCodes): string
    {
        $parts = [];
        foreach ($normalizedCodes as $c) {
            $label = self::labelForCode($c);
            if ($label !== null) {
                $parts[] = $label;
            }
        }

        return implode(', ', $parts);
    }

    public static function labelForCode(string $code): ?string
    {
        if (Schema::hasTable('order_addons')) {
            $row = OrderAddon::query()->where('kode', $code)->first();
            if ($row) {
                return $row->label;
            }
        }

        $defs = config('order_addons.items', []);

        return $defs[$code]['label'] ?? null;
    }

    public static function priceForCode(string $code): int
    {
        if (Schema::hasTable('order_addons')) {
            $row = OrderAddon::query()->where('kode', $code)->first();
            if ($row) {
                return (int) $row->harga;
            }
        }

        $defs = config('order_addons.items', []);

        return (int) ($defs[$code]['harga'] ?? 0);
    }

}

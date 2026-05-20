<?php

namespace App\Support;

class OrderAddonCatalog
{
    /** @return array<string, array{label: string, harga: int}> */
    public static function definitions(): array
    {
        return config('order_addons.items', []);
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
        $defs = self::definitions();
        $sum = 0;
        foreach ($normalizedCodes as $c) {
            $sum += (int) ($defs[$c]['harga'] ?? 0);
        }

        return $sum;
    }

    /** @param  list<string>  $normalizedCodes */
    public static function labelsLine(array $normalizedCodes): string
    {
        $defs = self::definitions();
        $parts = [];
        foreach ($normalizedCodes as $c) {
            if (isset($defs[$c]['label'])) {
                $parts[] = $defs[$c]['label'];
            }
        }

        return implode(', ', $parts);
    }
}

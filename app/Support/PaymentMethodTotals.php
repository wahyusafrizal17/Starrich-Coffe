<?php

namespace App\Support;

use App\Models\Transaction;
use Carbon\CarbonInterface;

class PaymentMethodTotals
{
    /**
     * @return array{cash: int, transfer: int, qris: int}
     */
    public static function forRange(CarbonInterface $rangeStart, CarbonInterface $rangeEnd): array
    {
        $totals = [
            'cash' => 0,
            'transfer' => 0,
            'qris' => 0,
        ];

        Transaction::paid()
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->select(['id', 'total', 'metode_pembayaran', 'payment_splits'])
            ->chunkById(500, function ($rows) use (&$totals) {
                foreach ($rows as $transaction) {
                    $splits = $transaction->payment_splits;

                    if (is_array($splits) && $splits !== []) {
                        foreach ($splits as $split) {
                            $method = $split['metode'] ?? null;
                            if (is_string($method) && array_key_exists($method, $totals)) {
                                $totals[$method] += (int) ($split['jumlah'] ?? 0);
                            }
                        }

                        continue;
                    }

                    $method = $transaction->metode_pembayaran ?? 'cash';
                    if (array_key_exists($method, $totals)) {
                        $totals[$method] += (int) $transaction->total;
                    }
                }
            });

        return $totals;
    }
}

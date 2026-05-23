<?php

namespace Database\Seeders;

use App\Models\OrderAddon;
use Illuminate\Database\Seeder;

class OrderAddonSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['kode' => 'arabica', 'label' => 'Biji Arabika', 'harga' => 2000, 'urutan' => 1],
            ['kode' => 'oatside_milk', 'label' => 'Susu Oatside', 'harga' => 5000, 'urutan' => 2],
        ];

        foreach ($defaults as $row) {
            OrderAddon::query()->updateOrCreate(
                ['kode' => $row['kode']],
                [
                    'label' => $row['label'],
                    'harga' => $row['harga'],
                    'urutan' => $row['urutan'],
                    'is_active' => true,
                ]
            );
        }
    }
}

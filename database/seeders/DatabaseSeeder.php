<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        TransactionDetail::query()->delete();
        Transaction::query()->delete();
        Product::query()->delete();
        Category::query()->delete();
        User::query()->delete();

        User::create([
            'name' => 'Administrator Starrich',
            'email' => 'admin@starrich.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Kasir Demo Starrich',
            'email' => 'kasir@starrich.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'email_verified_at' => now(),
        ]);

        $makanan = Category::create(['nama_kategori' => 'Makanan']);
        $minuman = Category::create(['nama_kategori' => 'Minuman']);
        $snack = Category::create(['nama_kategori' => 'Snack']);

        $rows = [
            ['nama_produk' => 'Nasi Goreng Spesial', 'harga' => 25000, 'stok' => 40, 'kategori_id' => $makanan->id],
            ['nama_produk' => 'Mie Ayam Bakso', 'harga' => 22000, 'stok' => 35, 'kategori_id' => $makanan->id],
            ['nama_produk' => 'Ayam Geprek', 'harga' => 22000, 'stok' => 20, 'kategori_id' => $makanan->id],
            ['nama_produk' => 'Kopi Hitam', 'harga' => 8000, 'stok' => 100, 'kategori_id' => $minuman->id],
            ['nama_produk' => 'Es Teh Manis', 'harga' => 6000, 'stok' => 120, 'kategori_id' => $minuman->id],
            ['nama_produk' => 'Jus Jeruk', 'harga' => 12000, 'stok' => 45, 'kategori_id' => $minuman->id],
            ['nama_produk' => 'Air Mineral', 'harga' => 4000, 'stok' => 200, 'kategori_id' => $minuman->id],
            ['nama_produk' => 'Keripik Singkong', 'harga' => 10000, 'stok' => 50, 'kategori_id' => $snack->id],
            ['nama_produk' => 'Wafer Coklat', 'harga' => 8000, 'stok' => 60, 'kategori_id' => $snack->id],
        ];

        foreach ($rows as $r) {
            Product::create($r);
        }
    }
}

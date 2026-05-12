<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Expense;
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
        Expense::query()->delete();
        Asset::query()->delete();
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
            ['nama_produk' => 'Nasi Goreng Spesial', 'harga' => 25000, 'kategori_id' => $makanan->id],
            ['nama_produk' => 'Mie Ayam Bakso', 'harga' => 22000, 'kategori_id' => $makanan->id],
            ['nama_produk' => 'Ayam Geprek', 'harga' => 22000, 'kategori_id' => $makanan->id],
            ['nama_produk' => 'Kopi Hitam', 'harga' => 8000, 'kategori_id' => $minuman->id],
            ['nama_produk' => 'Es Teh Manis', 'harga' => 6000, 'kategori_id' => $minuman->id],
            ['nama_produk' => 'Jus Jeruk', 'harga' => 12000, 'kategori_id' => $minuman->id],
            ['nama_produk' => 'Air Mineral', 'harga' => 4000, 'kategori_id' => $minuman->id],
            ['nama_produk' => 'Keripik Singkong', 'harga' => 10000, 'kategori_id' => $snack->id],
            ['nama_produk' => 'Wafer Coklat', 'harga' => 8000, 'kategori_id' => $snack->id],
        ];

        foreach ($rows as $r) {
            Product::create($r);
        }

        $assets = [
            ['nama' => 'Mesin Espresso La Marzocco Linea Mini', 'tanggal_perolehan' => now()->subMonths(8)->toDateString(), 'harga_perolehan' => 75000000, 'catatan' => 'Mesin utama bar'],
            ['nama' => 'Grinder Mahlkonig E65S', 'tanggal_perolehan' => now()->subMonths(8)->toDateString(), 'harga_perolehan' => 28000000, 'catatan' => null],
            ['nama' => 'Kulkas Display Glass Door', 'tanggal_perolehan' => now()->subMonths(6)->toDateString(), 'harga_perolehan' => 12000000, 'catatan' => null],
            ['nama' => 'POS Tablet & Printer Thermal', 'tanggal_perolehan' => now()->subMonths(3)->toDateString(), 'harga_perolehan' => 8500000, 'catatan' => null],
        ];
        foreach ($assets as $a) {
            Asset::create($a);
        }

        $expenses = [
            ['tanggal' => now()->startOfMonth()->toDateString(), 'kategori' => Expense::CATEGORY_RENT, 'nama' => 'Sewa tempat bulan ini', 'jumlah' => 7500000, 'catatan' => 'Pembayaran kontrak ruko'],
            ['tanggal' => now()->subDays(5)->toDateString(), 'kategori' => Expense::CATEGORY_MAINTENANCE, 'nama' => 'Servis berkala mesin espresso', 'jumlah' => 850000, 'catatan' => 'Ganti gasket & kalibrasi'],
            ['tanggal' => now()->subDays(2)->toDateString(), 'kategori' => Expense::CATEGORY_UTILITY, 'nama' => 'Tagihan listrik & air', 'jumlah' => 1450000, 'catatan' => null],
            ['tanggal' => now()->subDays(10)->toDateString(), 'kategori' => Expense::CATEGORY_SUPPLIES, 'nama' => 'Restock biji kopi & susu', 'jumlah' => 3200000, 'catatan' => null],
        ];
        foreach ($expenses as $e) {
            Expense::create($e);
        }
    }
}

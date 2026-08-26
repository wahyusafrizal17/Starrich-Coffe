<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropUnique(['product_id']);
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->string('nama')->nullable()->after('id');
            $table->string('jenis', 32)->default('product')->after('nama');
            $table->string('tipe_nilai', 16)->default('amount')->after('jenis');
            $table->foreignId('category_id')->nullable()->after('product_id')->constrained('categories')->nullOnDelete();
            $table->unsignedBigInteger('min_belanja')->nullable()->after('jumlah');
            $table->dateTime('starts_at')->nullable()->after('min_belanja');
            $table->dateTime('ends_at')->nullable()->after('starts_at');
            $table->time('jam_mulai')->nullable()->after('ends_at');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
            $table->json('hari_aktif')->nullable()->after('jam_selesai');
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnUpdate()->nullOnDelete();
        });

        $rows = DB::table('discounts')->get();
        foreach ($rows as $row) {
            $productName = DB::table('products')->where('id', $row->product_id)->value('nama_produk');
            DB::table('discounts')->where('id', $row->id)->update([
                'nama' => $productName
                    ? 'Diskon '.$productName
                    : 'Diskon produk #'.$row->id,
                'jenis' => 'product',
                'tipe_nilai' => 'amount',
            ]);
        }

        Schema::table('discounts', function (Blueprint $table) {
            $table->string('nama')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        // Keep only product-type rows that still have a product_id.
        DB::table('discounts')->whereNull('product_id')->orWhere('jenis', '!=', 'product')->delete();

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn([
                'nama',
                'jenis',
                'tipe_nilai',
                'category_id',
                'min_belanja',
                'starts_at',
                'ends_at',
                'jam_mulai',
                'jam_selesai',
                'hari_aktif',
            ]);
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique('product_id');
        });
    }
};

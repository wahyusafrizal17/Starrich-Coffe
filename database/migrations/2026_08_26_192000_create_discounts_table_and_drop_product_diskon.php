<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('jumlah');
            $table->boolean('is_active')->default(true);
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->unique('product_id');
        });

        if (Schema::hasColumn('products', 'diskon')) {
            $rows = DB::table('products')
                ->where('diskon', '>', 0)
                ->select(['id', 'diskon'])
                ->get();

            $now = now();
            foreach ($rows as $row) {
                DB::table('discounts')->insert([
                    'product_id' => $row->id,
                    'jumlah' => (int) $row->diskon,
                    'is_active' => true,
                    'catatan' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('diskon');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('products', 'diskon')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('diskon')->default(0)->after('harga');
            });
        }

        if (Schema::hasTable('discounts')) {
            $rows = DB::table('discounts')->where('is_active', true)->get();
            foreach ($rows as $row) {
                DB::table('products')
                    ->where('id', $row->product_id)
                    ->update(['diskon' => (int) $row->jumlah]);
            }

            Schema::dropIfExists('discounts');
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'stok')) {
                $table->dropColumn('stok');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'stok')) {
                $table->unsignedInteger('stok')->default(0)->after('harga');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('subtotal')->default(0)->after('id');
            $table->unsignedBigInteger('diskon')->default(0)->after('subtotal');
        });

        DB::table('transactions')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('transactions')
                    ->where('id', $row->id)
                    ->update([
                        'subtotal' => (int) $row->total,
                        'diskon' => 0,
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'diskon']);
        });
    }
};

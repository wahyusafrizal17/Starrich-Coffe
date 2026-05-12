<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->date('tanggal_perolehan');
            $table->unsignedBigInteger('harga_perolehan');
            $table->unsignedBigInteger('nilai_sisa')->default(0);
            $table->unsignedSmallInteger('masa_manfaat_bulan');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal_perolehan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

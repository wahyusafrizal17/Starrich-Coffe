<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kategori', 32);
            $table->string('nama');
            $table->unsignedBigInteger('jumlah');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal');
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

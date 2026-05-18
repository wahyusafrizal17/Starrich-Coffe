<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'status')) {
                $table->string('status', 16)->default('paid')->after('user_id');
            }
            if (! Schema::hasColumn('transactions', 'order_type')) {
                $table->string('order_type', 16)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'order_type')) {
                $table->dropColumn('order_type');
            }
            if (Schema::hasColumn('transactions', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

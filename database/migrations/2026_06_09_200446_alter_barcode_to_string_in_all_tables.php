<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('barcode')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('barcode')->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('barcode')->change();
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->string('barcode')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse if needed (back to unsignedBigInteger)
        Schema::table('stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('barcode')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('barcode')->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('barcode')->change();
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->unsignedBigInteger('barcode')->change();
        });
    }
};
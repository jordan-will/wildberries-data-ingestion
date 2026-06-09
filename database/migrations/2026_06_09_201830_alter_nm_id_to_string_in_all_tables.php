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
            $table->string('nm_id')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('nm_id')->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('nm_id')->change();
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->string('nm_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_tables', function (Blueprint $table) {
            Schema::table('stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('nm_id')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('nm_id')->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('nm_id')->change();
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->unsignedBigInteger('nm_id')->change();
        });
        });
    }
};

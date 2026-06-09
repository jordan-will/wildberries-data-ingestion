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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('income_id');
            $table->string('number')->nullable();
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->unsignedBigInteger('barcode');
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->date('date_close');
            $table->string('warehouse_name');
            $table->unsignedBigInteger('nm_id');
            $table->timestamps();

            // Indexing for performance
            $table->index(['income_id', 'barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};

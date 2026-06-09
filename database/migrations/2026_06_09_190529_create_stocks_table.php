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
    Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->date('last_change_date');
        $table->string('supplier_article');
        $table->string('tech_size');
        $table->unsignedBigInteger('barcode');
        $table->integer('quantity');
        $table->boolean('is_supply');
        $table->boolean('is_realization');
        $table->integer('quantity_full');
        $table->string('warehouse_name');
        $table->integer('in_way_to_client');
        $table->integer('in_way_from_client');
        $table->unsignedBigInteger('nm_id');
        $table->string('subject');
        $table->string('category');
        $table->string('brand');
        $table->unsignedBigInteger('sc_code')->nullable();
        $table->decimal('price', 10, 2);
        $table->decimal('discount', 5, 2);
        $table->timestamps();

        // Index for performance optimization when looking up products
        $table->index('barcode');
        $table->index('nm_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shopen\Models\Product\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('ean')->nullable();
            $table->string('type')->default(Product::TYPE_SIMPLE);
            $table->boolean('visible_individually')->default(true);
            $table->foreignId('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
            $table->boolean('uses_stock')->default(false);
            $table->integer('stock_qty')->unsigned()->default(0);
            $table->boolean('in_stock')->default(false);
            $table->boolean('is_virtual')->default(false);
            $table->boolean('is_voucher')->default(false);
            $table->boolean('is_new')->default(false);
            $table->date('is_new_to')->nullable();
            $table->foreignId('promo_code_id')->nullable();
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');
            $table->integer('base_product_id')->unsigned()->nullable()->comment('Baselinker product ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

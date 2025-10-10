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
            $table->integer('visibility')->default(Product::VISIBILITY_NONE);
            $table->foreignId('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
            $table->boolean('uses_stock');
            $table->integer('stock_qty')->unsigned()->default(0);
            $table->boolean('in_stock')->default(false);
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

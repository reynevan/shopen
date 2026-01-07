<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_items')) {
            return;
        }
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained();
            $table->boolean('promo_code_coupon_email_sent')->default(false);
            $table->string('sku');
            $table->string('name');
            $table->integer('quantity');
            $table->integer('returned_quantity')->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('final_price', 10, 2);
            $table->decimal('promo_code_discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->string('tax_rate')->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

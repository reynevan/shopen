<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_promo_code_coupon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('promo_code_coupon_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index('order_item_id');
            $table->index('promo_code_coupon_id', 'order_item_promo_code_coupon_pcci_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_promo_code_coupon');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shopen\Enums\Order\OrderStatus;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return;
        }
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->string('status')->default(OrderStatus::NEW->value);
            $table->string('shipping_method');
            $table->string('delivery_point_code')->nullable();
            $table->string('shipping_tracking_code')->nullable();
            $table->string('payment_method');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount_returned', 10, 2)->default(0);
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('returned_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->foreignId('promo_code_coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('promo_code_discount_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

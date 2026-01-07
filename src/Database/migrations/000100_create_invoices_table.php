<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shopen\Enums\Order\OrderStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('base_invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->string('correction_reason')->nullable();
            $table->boolean('is_correction')->default(false);
            $table->date('payment_due_date')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('correction_payment_method')->nullable();
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_net_amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('left_to_pay_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['order_id', 'base_invoice_id']);
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

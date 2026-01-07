<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('base_invoice_item_id')->nullable()->constrained('invoice_items')->onDelete('cascade');
            $table->string('sku');
            $table->string('name');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('price_net', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('discount_amount_net', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('total_net', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->string('tax_rate')->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'base_invoice_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};

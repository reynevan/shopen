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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);

            $table->enum('discount_type', ['percentage', 'fixed_amount']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('max_discount_amount', 10, 2)->nullable()->comment('Max kwota zniżki dla kodów procentowych');

            $table->decimal('minimum_order_value', 10, 2)->nullable()->default(0);
            $table->enum('applies_to', ['cart', 'per_item'])->default('cart');
            $table->boolean('for_logged_users_only')->default(false);
            $table->boolean('applies_to_discounted')->default(false);

            $table->integer('usage_limit')->nullable()->comment('Globalny limit użyć, null = bez limitu');
            $table->integer('current_usage_count')->default(0);

            $table->datetime('valid_from')->nullable();
            $table->datetime('valid_to')->nullable();

            $table->json('conditions_serialized')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'valid_from', 'valid_to']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};

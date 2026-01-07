<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cart_addresses')) {
            return;
        }
        Schema::create('cart_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('company_nip')->nullable();
            $table->string('address_line');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->index(['cart_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_addresses');
    }
};

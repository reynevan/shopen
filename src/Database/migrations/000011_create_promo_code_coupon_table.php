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
        Schema::create('promo_code_coupons', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();

            $table->foreignId('promo_code_id');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');

            $table->integer('usage_count')->default(0);

            $table->timestamps();

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

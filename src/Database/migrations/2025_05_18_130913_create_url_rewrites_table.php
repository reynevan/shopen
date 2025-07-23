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
        Schema::create('url_rewrites', function (Blueprint $table) {
            $table->id();
            $table->string('request_path');
            $table->string('target_path');
            $table->string('entity_type');
            $table->foreignId('entity_id')->nullable();
            $table->foreignId('store_id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_rewrites');
    }
};

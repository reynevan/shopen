<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shopen\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ceneo_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('external_id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('path')->nullable();
            $table->integer('level')->default(0);
            $table->string('name')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ceneo_categories');
    }
};

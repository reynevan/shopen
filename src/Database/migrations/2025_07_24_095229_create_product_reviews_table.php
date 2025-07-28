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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->comment('Ocena w skali 1-5');
            $table->tinyInteger('rating_to_verify')->unsigned()->nullable();
            $table->text('comment');
            $table->text('comment_to_verify')->nullable();
            $table->string('status', 20)->default('pending')->comment('pending, approved, rejected');
            $table->boolean('is_verified_purchase')->default(false);
            $table->integer('helpful_votes_count')->default(0);
            $table->integer('unhelpful_votes_count')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};

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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('entity_type');
            $table->string('backend_type');
            $table->string('frontend_type');
            $table->integer('sort_order')->default(0);
            $table->string('code');
            $table->string('units')->nullable();
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_sortable')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_visible_in_details')->default(false);
            $table->boolean('is_used_in_list')->default(false);
            $table->boolean('is_color')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};

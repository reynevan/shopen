<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('ceneo_category_id')->after('parent_id')->nullable();
            $table->foreign('ceneo_category_id')->references('id')->on('ceneo_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['ceneo_category_id']);
            $table->dropColumn('ceneo_category_id');
        });
    }
};

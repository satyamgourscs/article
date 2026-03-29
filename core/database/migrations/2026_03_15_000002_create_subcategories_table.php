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
        if (!Schema::hasTable('subcategories')) {
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id');
                $table->string('name', 40)->nullable();
                $table->string('image', 255)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('is_featured')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};

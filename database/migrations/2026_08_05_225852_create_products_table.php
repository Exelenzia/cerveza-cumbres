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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('style')->nullable();
            $table->text('description')->nullable();
            $table->decimal('abv', 4, 1)->nullable();
            $table->unsignedSmallInteger('ibu')->nullable();
            $table->unsignedSmallInteger('volume_ml')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('compare_at_price', 8, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

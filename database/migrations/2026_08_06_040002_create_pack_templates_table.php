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
        Schema::create('pack_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['fixed_style', 'mix', 'gift']);
            $table->unsignedInteger('bottle_count');
            $table->decimal('base_price', 8, 2)->nullable();
            $table->foreignId('included_merch_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('delivery_cost', 8, 2)->nullable();
            $table->boolean('free_shipping_eligible')->default(true);
            $table->string('delivery_note')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('pack_templates');
    }
};

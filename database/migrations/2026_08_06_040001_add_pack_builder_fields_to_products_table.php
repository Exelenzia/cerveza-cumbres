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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('fixed_pack6_price', 8, 2)->nullable()->after('compare_at_price');
            $table->boolean('is_mix_premium')->default(false)->after('fixed_pack6_price');
            $table->decimal('mix_surcharge_per_unit', 8, 2)->nullable()->after('is_mix_premium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['fixed_pack6_price', 'is_mix_premium', 'mix_surcharge_per_unit']);
        });
    }
};

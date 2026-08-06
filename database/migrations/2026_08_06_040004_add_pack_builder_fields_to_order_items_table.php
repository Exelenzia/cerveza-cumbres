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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('pack_template_id')->nullable()->after('pack_id')->constrained()->nullOnDelete();
            $table->json('composition')->nullable()->after('pack_template_id');
            $table->foreignId('variant_id')->nullable()->after('composition')->constrained('product_variants')->nullOnDelete();
            $table->string('variant_label')->nullable()->after('variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pack_template_id');
            $table->dropConstrainedForeignId('variant_id');
            $table->dropColumn(['composition', 'variant_label']);
        });
    }
};

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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_zone_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('payment_reference');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_zone_id');
            $table->dropColumn(['coupon_code', 'discount_amount']);
        });
    }
};

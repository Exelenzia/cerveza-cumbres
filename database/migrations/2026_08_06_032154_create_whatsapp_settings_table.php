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
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number_id');
            $table->text('access_token')->nullable();
            $table->string('business_account_id')->nullable();
            $table->string('wa_link_phone')->nullable();
            $table->string('template_language')->default('es');
            $table->string('template_confirmacion')->nullable();
            $table->string('template_enviado')->nullable();
            $table->string('template_entregado')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};

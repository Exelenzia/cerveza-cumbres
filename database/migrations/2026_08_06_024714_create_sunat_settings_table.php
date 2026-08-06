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
        Schema::create('sunat_settings', function (Blueprint $table) {
            $table->id();
            $table->string('ruc');
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();
            $table->string('direccion')->nullable();
            $table->string('sol_usuario')->nullable();
            $table->text('sol_clave')->nullable();
            $table->string('certificado_path')->nullable();
            $table->text('certificado_password')->nullable();
            $table->string('modo')->default('beta');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sunat_settings');
    }
};

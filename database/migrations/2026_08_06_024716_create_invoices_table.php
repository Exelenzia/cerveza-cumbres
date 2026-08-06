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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_series_id')->constrained('document_series');
            $table->string('tipo_comprobante');
            $table->string('serie');
            $table->unsignedInteger('correlativo');
            $table->date('fecha_emision');
            $table->string('moneda', 3)->default('PEN');
            $table->decimal('op_gravada', 10, 2)->default(0);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('pendiente');
            $table->string('sunat_response_code')->nullable();
            $table->text('sunat_response_description')->nullable();
            $table->string('hash')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('cdr_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->unique(['tipo_comprobante', 'serie', 'correlativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

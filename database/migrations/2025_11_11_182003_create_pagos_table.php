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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onUpdate('cascade')->onDelete('cascade');
            $table->string('metodo', 100)->nullable();
            $table->string('referencia_transaccion', 200)->nullable();
            $table->decimal('monto', 12, 2);
            $table->string('moneda', 10)->default('COP');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'reembolsado'])->default('pendiente');
            $table->timestamp('pagado_en')->nullable();
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

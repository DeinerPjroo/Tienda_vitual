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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones')->onUpdate('cascade')->onDelete('set null');
            $table->decimal('total', 12, 2);
            $table->decimal('costo_envio', 10, 2)->default(0);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'pagado', 'procesando', 'enviado', 'entregado', 'cancelado', 'reembolsado'])->default('pendiente');
            $table->timestamp('fecha_pedido')->useCurrent();
            $table->timestamp('actualizado_en')->useCurrent()->useCurrentOnUpdate();
            $table->text('nota')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};

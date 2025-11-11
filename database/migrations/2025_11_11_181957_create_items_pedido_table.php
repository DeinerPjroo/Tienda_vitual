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
        Schema::create('items_pedido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('variacion_id')->constrained('variaciones')->onUpdate('cascade')->onDelete('restrict');
            $table->string('nombre_prenda_snapshot', 255);
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 12, 2)->virtualAs('precio_unitario * cantidad');
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_pedido');
    }
};

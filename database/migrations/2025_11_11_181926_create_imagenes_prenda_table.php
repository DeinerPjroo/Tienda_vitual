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
        Schema::create('imagenes_prenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prenda_id')->constrained('prendas')->onUpdate('cascade')->onDelete('cascade');
            $table->string('url', 500);
            $table->string('texto_alternativo', 255)->nullable();
            $table->integer('posicion')->default(0);
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_prenda');
    }
};

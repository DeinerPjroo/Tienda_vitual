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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nombre_completo', 200);
            $table->string('telefono', 30)->nullable();
            $table->string('direccion_linea1', 255);
            $table->string('direccion_linea2', 255)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->string('pais', 100)->nullable();
            $table->boolean('predeterminada')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};

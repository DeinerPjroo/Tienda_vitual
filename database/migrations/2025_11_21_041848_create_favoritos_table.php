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
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('prenda_id')->constrained('prendas')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            
            // Evitar duplicados: un usuario solo puede tener un producto una vez en favoritos
            $table->unique(['usuario_id', 'prenda_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};

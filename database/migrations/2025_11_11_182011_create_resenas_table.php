<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('prenda_id')->constrained('prendas')->onUpdate('cascade')->onDelete('restrict');
            $table->tinyInteger('calificacion');
            $table->string('titulo', 255)->nullable();
            $table->text('comentario')->nullable();
            $table->boolean('aprobado')->default(false);
            $table->timestamp('creado_en')->useCurrent();
        });

        // Agregar constraint CHECK para calificacion
        DB::statement('ALTER TABLE resenas ADD CONSTRAINT chk_calificacion CHECK (calificacion >= 1 AND calificacion <= 5)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};

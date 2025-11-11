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
        Schema::create('prendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onUpdate('cascade')->onDelete('set null');
            $table->string('sku', 100)->unique()->nullable();
            $table->string('nombre', 200);
            $table->string('descripcion_corta', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->decimal('descuento', 5, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prendas');
    }
};

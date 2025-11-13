<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('rol_id')->constrained('roles')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('nombre', 100);
            $table->string('apellido', 100)->nullable();
            $table->string('correo', 150)->unique();
            $table->string('password', 255);
            $table->string('telefono', 30)->nullable();
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};

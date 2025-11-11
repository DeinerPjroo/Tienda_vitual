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
        Schema::create('variaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prenda_id')->constrained('prendas')->onUpdate('cascade')->onDelete('cascade');
            $table->string('sku', 120)->nullable();
            $table->string('color', 80)->nullable();
            $table->string('talla', 30)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('precio_adicional', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('variaciones', function (Blueprint $table) {
            $table->index('prenda_id');
            $table->index('color');
            $table->index('talla');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variaciones');
    }
};

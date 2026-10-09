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
        Schema::create('dishes', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->enum('tipo', ['principal', 'acompanamiento', 'bebida', 'postre', 'complemento']);
    $table->unsignedInteger('tiempo_preparacion_min');
    $table->decimal('costo_aproximado', 8, 2)->default(0);
    $table->foreignId('style_id')->constrained()->cascadeOnDelete();
    $table->enum('objetivo', ['economica', 'rapida', 'ligera', 'alta_proteina'])->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};

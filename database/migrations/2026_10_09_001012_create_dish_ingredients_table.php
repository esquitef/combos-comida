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
        Schema::create('dish_ingredients', function (Blueprint $table) {
    $table->id();
    $table->foreignId('dish_id')->constrained()->cascadeOnDelete();
    $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
    $table->decimal('cantidad', 8, 2)->default(1);
    $table->boolean('es_opcional')->default(false);
    $table->unique(['dish_id', 'ingredient_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dish_ingredients');
    }
};

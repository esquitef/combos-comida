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
        Schema::create('combo_dishes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('combo_id')->constrained()->cascadeOnDelete();
    $table->foreignId('dish_id')->constrained()->cascadeOnDelete();
    $table->string('rol');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_dishes');
    }
};

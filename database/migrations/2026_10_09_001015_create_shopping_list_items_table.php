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
    Schema::create('shopping_list_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('shopping_list_id')->constrained()->cascadeOnDelete();
    $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
    $table->decimal('cantidad', 8, 2)->default(1);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};

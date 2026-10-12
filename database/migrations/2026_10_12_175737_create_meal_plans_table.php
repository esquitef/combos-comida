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
    Schema::create('meal_plans', function (Blueprint $table) {
        $table->id();
        $table->string('session_id')->index();
        $table->timestamps();
    });

    Schema::table('combos', function (Blueprint $table) {
        $table->foreignId('meal_plan_id')->nullable()->constrained()->cascadeOnDelete();
        $table->unsignedTinyInteger('dia')->nullable();
    });
}

public function down(): void
{
    Schema::table('combos', function (Blueprint $table) {
        $table->dropConstrainedForeignId('meal_plan_id');
        $table->dropColumn('dia');
    });

    Schema::dropIfExists('meal_plans');
}
};

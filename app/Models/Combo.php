<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Combo extends Model
{
    protected $fillable = ['session_id', 'score', 'meal_plan_id', 'dia'];

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'combo_dishes')
            ->withPivot('rol');
    }

    public function shoppingList(): HasOne
    {
        return $this->hasOne(ShoppingList::class);
    }

    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }
}
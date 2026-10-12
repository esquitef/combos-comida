<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    protected $fillable = ['session_id'];

    public function combos(): HasMany
    {
        return $this->hasMany(Combo::class)->orderBy('dia');
    }
}
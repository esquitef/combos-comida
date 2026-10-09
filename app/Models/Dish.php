<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dish extends Model
{
    protected $fillable = [
        'nombre', 'tipo', 'tiempo_preparacion_min',
        'costo_aproximado', 'style_id', 'objetivo',
    ];

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'dish_ingredients')
            ->withPivot('cantidad', 'es_opcional');
    }

    public function combos(): BelongsToMany
    {
        return $this->belongsToMany(Combo::class, 'combo_dishes')
            ->withPivot('rol');
    }
}
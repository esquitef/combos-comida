<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Style extends Model
{
    protected $fillable = ['nombre', 'pais_origen'];

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }
}
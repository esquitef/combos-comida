<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ShoppingList extends Model
{
    protected $fillable = ['combo_id'];

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'shopping_list_items')
            ->withPivot('cantidad');
    }
}

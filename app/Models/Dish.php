<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Dish extends Model
{
    /** Formatos de foto aceptados, en orden de preferencia. */
    public const EXTENSIONES_FOTO = ['webp', 'jpg', 'jpeg', 'png'];

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

    /**
     * Nombre del archivo de la foto, sin extensión.
     * Ejemplo: "Huevos motuleños" → "huevos-motulenos".
     */
    protected function slug(): Attribute
    {
        return Attribute::get(fn () => Str::slug($this->nombre));
    }

    /**
     * URL de la foto del plato (public/img/platos/{slug}.webp, .jpg, .jpeg o .png),
     * o null si todavía no existe. Mientras no haya foto, las vistas muestran un símbolo.
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(function () {
            foreach (self::EXTENSIONES_FOTO as $extension) {
                $ruta = "img/platos/{$this->slug}.{$extension}";

                if (is_file(public_path($ruta))) {
                    return asset($ruta);
                }
            }

            return null;
        });
    }
}

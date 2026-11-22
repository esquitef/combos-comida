<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\Style;
use Illuminate\View\View;

class AcercaController extends Controller
{
    /** Página «Acerca del proyecto»: explica qué es ComiBox y muestra las cifras reales del catálogo. */
    public function __invoke(): View
    {
        return view('acerca', [
            'totalIngredientes' => Ingredient::count(),
            'totalCategorias'   => Category::count(),
            'totalPlatos'       => Dish::count(),
            'totalEstilos'      => Style::count(),
        ]);
    }
}

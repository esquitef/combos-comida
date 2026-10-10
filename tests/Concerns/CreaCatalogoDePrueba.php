<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\Style;

trait CreaCatalogoDePrueba
{
    /** @var array<string, Ingredient> */
    protected array $ing = [];

    /** @var array<string, Style> */
    protected array $estilos = [];

    protected function crearMundo(): void
    {
        $categoria = Category::create(['nombre' => 'Básicos']);

        foreach (['Pollo', 'Arroz', 'Tomate', 'Cebolla', 'Huevo', 'Limón', 'Azúcar', 'Pasta'] as $nombre) {
            $this->ing[$nombre] = Ingredient::create([
                'nombre'        => $nombre,
                'category_id'   => $categoria->id,
                'unidad_medida' => 'pieza',
                'es_perecedero' => false,
            ]);
        }

        $this->estilos['Mexicano'] = Style::create(['nombre' => 'Mexicano', 'pais_origen' => 'México']);
        $this->estilos['Italiano'] = Style::create(['nombre' => 'Italiano', 'pais_origen' => 'Italia']);

        //                  nombre             tipo             min  $   estilo      objetivo         esenciales         opcionales
        $this->crearPlato('Arroz con pollo', 'principal',      30, 60, 'Mexicano', 'alta_proteina', ['Arroz', 'Pollo'], ['Cebolla']);
        $this->crearPlato('Huevos rancheros', 'principal',     10, 30, 'Mexicano', 'rapida',        ['Huevo', 'Tomate']);
        $this->crearPlato('Pasta al tomate', 'principal',      20, 40, 'Italiano', 'economica',     ['Pasta', 'Tomate']);
        $this->crearPlato('Ensalada', 'acompanamiento',         5, 15, 'Mexicano', 'ligera',        ['Tomate']);
        $this->crearPlato('Agua de limón', 'bebida',            5, 10, 'Mexicano', 'rapida',        ['Limón', 'Azúcar']);
        $this->crearPlato('Flan', 'postre',                    30, 25, 'Mexicano', 'economica',     ['Huevo', 'Azúcar']);
    }

    protected function crearPlato(
        string $nombre,
        string $tipo,
        int $minutos,
        float $costo,
        string $estilo,
        ?string $objetivo,
        array $esenciales,
        array $opcionales = []
    ): Dish {
        $plato = Dish::create([
            'nombre'                 => $nombre,
            'tipo'                   => $tipo,
            'tiempo_preparacion_min' => $minutos,
            'costo_aproximado'       => $costo,
            'style_id'               => $this->estilos[$estilo]->id,
            'objetivo'               => $objetivo,
        ]);

        $pivot = [];
        foreach ($esenciales as $n) {
            $pivot[$this->ing[$n]->id] = ['cantidad' => 1, 'es_opcional' => false];
        }
        foreach ($opcionales as $n) {
            $pivot[$this->ing[$n]->id] = ['cantidad' => 1, 'es_opcional' => true];
        }
        $plato->ingredients()->sync($pivot);

        return $plato;
    }

    /** Convierte nombres de ingredientes en sus IDs. */
    protected function ids(array $nombres): array
    {
        return array_map(fn (string $n) => $this->ing[$n]->id, $nombres);
    }
}
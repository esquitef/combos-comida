<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\Style;
use App\Services\ComboGeneratorService;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CatalogSeeder::class);
    }

    public function test_cada_plato_tiene_al_menos_un_ingrediente_esencial(): void
    {
        foreach (Dish::with('ingredients')->get() as $plato) {
            $this->assertTrue(
                $plato->ingredients->contains(fn ($i) => ! $i->pivot->es_opcional),
                "El plato '{$plato->nombre}' no tiene ningún ingrediente esencial."
            );
        }
    }

    public function test_cada_estilo_tiene_al_menos_un_plato_principal(): void
    {
        foreach (Style::all() as $estilo) {
            $this->assertTrue(
                $estilo->dishes()->where('tipo', 'principal')->exists(),
                "El estilo '{$estilo->nombre}' no tiene ningún plato principal."
            );
        }
    }

    public function test_los_platos_tienen_tiempo_y_costo_validos(): void
    {
        $invalidos = Dish::where('tiempo_preparacion_min', '<=', 0)
            ->orWhere('costo_aproximado', '<=', 0)
            ->pluck('nombre')
            ->all();

        $this->assertSame([], $invalidos, 'Platos con tiempo o costo en cero: ' . implode(', ', $invalidos));
    }

    public function test_el_seeder_se_puede_ejecutar_dos_veces_sin_duplicar_datos(): void
    {
        $contar = fn () => [
            Ingredient::count(),
            Dish::count(),
            Style::count(),
            DB::table('dish_ingredients')->count(),
        ];

        $antes = $contar();
        $this->seed(CatalogSeeder::class);

        $this->assertSame($antes, $contar());
    }

    public function test_con_ingredientes_comunes_el_catalogo_real_genera_un_combo(): void
    {
        $nombres = ['Pollo', 'Arroz', 'Tomate', 'Cebolla', 'Huevo', 'Limón', 'Zanahoria', 'Ajo', 'Papa', 'Queso', 'Tortilla', 'Aguacate'];
        $ids = Ingredient::whereIn('nombre', $nombres)->pluck('id')->all();

        $this->assertCount(count($nombres), $ids, 'Algún ingrediente de la lista no existe en el catálogo.');

        $resultado = app(ComboGeneratorService::class)->generar($ids);

        $this->assertNotNull($resultado);
        $this->assertSame('principal', $resultado['platos'][0]['rol']);
    }
}
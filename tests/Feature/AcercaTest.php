<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcercaTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_acerca_del_proyecto_carga(): void
    {
        $this->get('/acerca')
            ->assertOk()
            ->assertSee('Acerca del proyecto')
            ->assertSee('Gael Pérez Saldaña')
            ->assertSee('José Julián Vásquez Aguirre')
            ->assertSee('Lo que ComiBox no hace');
    }

    public function test_las_cifras_salen_del_catalogo_real(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->get('/acerca')
            ->assertOk()
            ->assertSee('<strong>' . Ingredient::count() . '</strong>', false);
    }

    public function test_el_pie_de_pagina_enlaza_a_acerca_desde_cualquier_pantalla(): void
    {
        foreach (['/', '/historial', '/acerca'] as $ruta) {
            $this->get($ruta)
                ->assertOk()
                ->assertSee('Acerca del proyecto')
                ->assertSee(route('acerca'));
        }
    }
}

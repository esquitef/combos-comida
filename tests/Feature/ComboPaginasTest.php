<?php

namespace Tests\Feature;

use App\Models\Combo;
use App\Models\Dish;
use App\Services\ComboGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\CreaCatalogoDePrueba;
use Tests\TestCase;

class ComboPaginasTest extends TestCase
{
    use RefreshDatabase, CreaCatalogoDePrueba;

    protected function setUp(): void
    {
        parent::setUp();

        $this->crearMundo();
    }

    public function test_la_pantalla_principal_muestra_ingredientes_y_estilos(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Básicos')
            ->assertSee('Pollo')
            ->assertSee('Mexicano');
    }

    public function test_exige_al_menos_un_ingrediente(): void
    {
        $this->post('/combos', [])->assertSessionHasErrors('ingredientes');
        $this->assertDatabaseCount('combos', 0);
    }

    public function test_rechaza_ingredientes_que_no_existen(): void
    {
        $this->post('/combos', ['ingredientes' => [999999]])->assertSessionHasErrors('ingredientes.0');
    }

    public function test_rechaza_un_presupuesto_negativo(): void
    {
        $this->post('/combos', [
            'ingredientes' => $this->ids(['Pollo']),
            'presupuesto'  => -5,
        ])->assertSessionHasErrors('presupuesto');
    }

    public function test_genera_un_combo_lo_guarda_y_redirige_a_su_pantalla(): void
    {
        $respuesta = $this->post('/combos', ['ingredientes' => $this->ids(['Huevo', 'Tomate'])]);

        $this->assertDatabaseCount('combos', 1);
        $respuesta->assertRedirect(route('combos.show', Combo::first()));
    }

    public function test_sorprendeme_tambien_genera_un_combo(): void
    {
        $this->post('/combos', [
            'ingredientes' => $this->ids(['Huevo', 'Tomate']),
            'sorprende'    => '1',
        ]);

        $this->assertDatabaseCount('combos', 1);
    }

    public function test_avisa_cuando_no_hay_ningun_plato_viable(): void
    {
        $this->post('/combos', ['ingredientes' => $this->ids(['Limón'])])
            ->assertSessionHasErrors('combo');

        $this->assertDatabaseCount('combos', 0);
    }

    public function test_muestra_el_combo_y_su_lista_de_compras_en_la_misma_sesion(): void
    {
        $sesion = Str::random(40);
        $servicio = app(ComboGeneratorService::class);
        $combo = $servicio->guardar($servicio->generar($this->ids(['Arroz', 'Cebolla'])), $sesion);

        $this->withCookie(config('session.cookie'), $sesion)
            ->get(route('combos.show', $combo))
            ->assertOk()
            ->assertSee('Arroz con pollo')
            ->assertSee('Lista de compras')
            ->assertSee('Pollo');
    }

    public function test_no_deja_ver_el_combo_de_otra_sesion(): void
    {
        $combo = Combo::create(['session_id' => 'sesion-ajena', 'score' => 50]);

        $this->get(route('combos.show', $combo))->assertNotFound();
    }

    public function test_el_historial_muestra_solo_los_combos_de_la_sesion(): void
    {
        $sesion = Str::random(40);
        $servicio = app(ComboGeneratorService::class);
        $servicio->guardar($servicio->generar($this->ids(['Huevo', 'Tomate'])), $sesion);

        $ajeno = Combo::create(['session_id' => 'sesion-ajena', 'score' => 50]);
        $ajeno->dishes()->attach(Dish::where('nombre', 'Pasta al tomate')->first()->id, ['rol' => 'principal']);

        $this->withCookie(config('session.cookie'), $sesion)
            ->get('/historial')
            ->assertOk()
            ->assertSee('Huevos rancheros')
            ->assertDontSee('Pasta al tomate');
    }

    public function test_el_historial_vacio_invita_a_generar_el_primero(): void
    {
        $this->get('/historial')
            ->assertOk()
            ->assertSee('Todavía no has generado');
    }
}
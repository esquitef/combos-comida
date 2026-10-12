<?php

namespace Tests\Feature;

use App\Models\Combo;
use App\Models\MealPlan;
use App\Services\PlanSemanalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\CreaCatalogoDePrueba;
use Tests\TestCase;

class PlanPaginasTest extends TestCase
{
    use RefreshDatabase, CreaCatalogoDePrueba;

    /** Alcanza para 3 principales distintos: el catálogo de prueba no da para 5 días. */
    private const INGREDIENTES = ['Pollo', 'Arroz', 'Tomate', 'Cebolla', 'Huevo', 'Pasta'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->crearMundo();
    }

    private function crearPlan(string $sesion, array $ingredientes, int $dias = 5): MealPlan
    {
        $servicio = app(PlanSemanalService::class);

        return $servicio->guardar($servicio->generar($this->ids($ingredientes), [], $dias), $sesion);
    }

    public function test_la_pantalla_principal_ofrece_el_plan_semanal(): void
    {
        $this->get('/')->assertOk()->assertSee('Plan de 5 días');
    }

    public function test_exige_al_menos_un_ingrediente(): void
    {
        $this->post('/planes', [])->assertSessionHasErrors('ingredientes');
        $this->assertDatabaseCount('meal_plans', 0);
    }

    public function test_genera_el_plan_lo_guarda_y_redirige_a_su_pantalla(): void
    {
        $respuesta = $this->post('/planes', ['ingredientes' => $this->ids(self::INGREDIENTES)]);

        $this->assertDatabaseCount('meal_plans', 1);
        $this->assertSame(3, Combo::count());
        $respuesta->assertRedirect(route('planes.show', MealPlan::first()));
    }

    public function test_avisa_cuando_el_plan_queda_mas_corto_de_lo_pedido(): void
    {
        $this->post('/planes', ['ingredientes' => $this->ids(self::INGREDIENTES)])
            ->assertSessionHas('aviso');
    }

    public function test_avisa_cuando_no_hay_ningun_principal_viable(): void
    {
        $this->post('/planes', ['ingredientes' => $this->ids(['Limón'])])
            ->assertSessionHasErrors('combo');

        $this->assertDatabaseCount('meal_plans', 0);
    }

    public function test_muestra_los_dias_y_la_lista_de_compras_de_la_semana(): void
    {
        $sesion = Str::random(40);
        // Sin Pollo, Pasta ni Azúcar: faltan cosas casi todos los días.
        $plan = $this->crearPlan($sesion, ['Arroz', 'Cebolla', 'Huevo', 'Tomate']);

        $this->withCookie(config('session.cookie'), $sesion)
            ->get(route('planes.show', $plan))
            ->assertOk()
            ->assertSee('Día 1')
            ->assertSee('Día 3')
            ->assertDontSee('Día 4')
            ->assertSee('Lista de compras de la semana')
            ->assertSee('Pollo')
            ->assertSee('Pasta')
            ->assertSee('Azúcar');
    }

    public function test_no_deja_ver_el_plan_de_otra_sesion(): void
    {
        $plan = MealPlan::create(['session_id' => 'sesion-ajena']);

        $this->get(route('planes.show', $plan))->assertNotFound();
    }

    public function test_el_historial_lista_solo_los_planes_de_la_sesion(): void
    {
        $sesion = Str::random(40);
        $this->crearPlan($sesion, self::INGREDIENTES, 3);
        $this->crearPlan('sesion-ajena', self::INGREDIENTES, 2);

        $this->withCookie(config('session.cookie'), $sesion)
            ->get('/historial')
            ->assertOk()
            ->assertSee('Plan de 3 días')
            ->assertDontSee('Plan de 2 días');
    }
}
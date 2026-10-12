<?php

namespace Tests\Feature;

use App\Models\Combo;
use App\Models\Dish;
use App\Services\ComboGeneratorService;
use App\Services\PlanSemanalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreaCatalogoDePrueba;
use Tests\TestCase;

class PlanSemanalServiceTest extends TestCase
{
    use RefreshDatabase, CreaCatalogoDePrueba;

    private const TIENE_TODO = ['Pollo', 'Arroz', 'Tomate', 'Cebolla', 'Huevo', 'Limón', 'Azúcar', 'Pasta'];

    private PlanSemanalService $planificador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->crearMundo();
        $this->planificador = app(PlanSemanalService::class);
    }

    private function nombrePrincipal(array $dia): string
    {
        return $dia['resultado']['platos'][0]['dish']->nombre;
    }

    /** Nombre del plato con ese rol en el día indicado (1, 2, ...), o null si ese día no lo tiene. */
    private function platoDelDia(array $plan, int $dia, string $rol): ?string
    {
        foreach ($plan['dias'][$dia - 1]['resultado']['platos'] as $p) {
            if ($p['rol'] === $rol) {
                return $p['dish']->nombre;
            }
        }

        return null;
    }

    public function test_arma_un_dia_por_cada_principal_distinto_y_avisa_que_quedo_incompleto(): void
    {
        $plan = $this->planificador->generar($this->ids(self::TIENE_TODO));

        // El catálogo de prueba solo tiene 3 principales: no alcanzan para 5 días.
        $this->assertSame(5, $plan['solicitados']);
        $this->assertCount(3, $plan['dias']);
        $this->assertFalse($plan['completo']);
        $this->assertSame([1, 2, 3], array_column($plan['dias'], 'dia'));

        $principales = array_map(fn ($d) => $this->nombrePrincipal($d), $plan['dias']);
        $this->assertCount(3, array_unique($principales), 'Se repitió un plato principal.');
        $this->assertEqualsCanonicalizing(['Arroz con pollo', 'Huevos rancheros', 'Pasta al tomate'], $principales);
    }

    public function test_respeta_la_cantidad_de_dias_pedida(): void
    {
        $plan = $this->planificador->generar($this->ids(self::TIENE_TODO), [], 2);

        $this->assertCount(2, $plan['dias']);
        $this->assertTrue($plan['completo']);
    }

    public function test_devuelve_un_plan_vacio_si_no_hay_ningun_principal_viable(): void
    {
        $plan = $this->planificador->generar($this->ids(['Limón']));

        $this->assertSame([], $plan['dias']);
        $this->assertFalse($plan['completo']);
    }

    public function test_prefiere_acompanamientos_que_no_uso_antes(): void
    {
        $this->crearPlato('Arroz blanco', 'acompanamiento', 20, 20, 'Mexicano', 'economica', ['Arroz']);

        $plan = $this->planificador->generar($this->ids(self::TIENE_TODO), [], 2);

        $this->assertSame('Ensalada', $this->platoDelDia($plan, 1, 'acompanamiento'));
        $this->assertSame('Arroz blanco', $this->platoDelDia($plan, 2, 'acompanamiento'));
    }

    public function test_los_extras_se_repiten_si_no_hay_alternativa(): void
    {
        // En el catálogo de prueba hay una sola bebida y un solo postre.
        $plan = $this->planificador->generar($this->ids(self::TIENE_TODO), [], 3);

        $this->assertCount(3, $plan['dias']);

        foreach ([1, 2, 3] as $dia) {
            $this->assertSame('Agua de limón', $this->platoDelDia($plan, $dia, 'bebida'));
            $this->assertSame('Flan', $this->platoDelDia($plan, $dia, 'postre'));
        }
    }

    public function test_respeta_el_presupuesto_de_cada_dia(): void
    {
        $plan = $this->planificador->generar($this->ids(self::TIENE_TODO), ['presupuesto' => 50]);

        // Arroz con pollo cuesta 60 y no cabe nunca: solo se arman 2 días.
        $this->assertCount(2, $plan['dias']);

        foreach ($plan['dias'] as $dia) {
            $this->assertLessThanOrEqual(50, $dia['resultado']['costo_total']);
            $this->assertNotSame('Arroz con pollo', $this->nombrePrincipal($dia));
        }
    }

    public function test_el_modo_aprovechamiento_prioriza_en_los_primeros_dias(): void
    {
        $ingredientes = $this->ids(['Pollo', 'Arroz', 'Huevo', 'Tomate']);

        $normal = $this->planificador->generar($ingredientes);
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($normal['dias'][0]));

        $aprovechando = $this->planificador->generar($ingredientes, [
            'prioritarios' => $this->ids(['Pollo', 'Arroz']),
        ]);
        $this->assertSame('Arroz con pollo', $this->nombrePrincipal($aprovechando['dias'][0]));
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($aprovechando['dias'][1]));
    }

    public function test_un_prioritario_ya_aprovechado_deja_de_empujar_los_dias_siguientes(): void
    {
        $this->crearPlato('Pollo asado', 'principal', 20, 20, 'Mexicano', 'alta_proteina', ['Pollo']);

        $plan = $this->planificador->generar(
            $this->ids(['Huevo', 'Tomate', 'Pasta', 'Pollo', 'Arroz', 'Cebolla']),
            ['prioritarios' => $this->ids(['Tomate'])]
        );

        // Día 1: Huevos rancheros y Pasta al tomate empatan con el bonus del tomate; gana el más barato.
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($plan['dias'][0]));

        // Día 2: el tomate ya se aprovechó, así que Pasta al tomate pierde su bonus
        // y gana el más barato de los que empatan.
        $this->assertSame('Pollo asado', $this->nombrePrincipal($plan['dias'][1]));
    }

    public function test_guardar_persiste_el_plan_y_no_aparece_en_el_historial(): void
    {
        $plan = $this->planificador->generar($this->ids(self::TIENE_TODO), [], 3);
        $mealPlan = $this->planificador->guardar($plan, 'sesion-plan');

        $this->assertDatabaseHas('meal_plans', ['id' => $mealPlan->id, 'session_id' => 'sesion-plan']);
        $this->assertEquals([1, 2, 3], $mealPlan->combos()->pluck('dia')->all());
        $this->assertSame(3, Combo::where('meal_plan_id', $mealPlan->id)->count());

        $normal = Combo::create(['session_id' => 'sesion-plan', 'score' => 50]);
        $historial = app(ComboGeneratorService::class)->historial('sesion-plan');

        $this->assertCount(1, $historial);
        $this->assertEquals($normal->id, $historial->first()->id);
    }

    public function test_la_lista_de_compras_general_consolida_los_faltantes_de_la_semana(): void
    {
        // Sin Pollo, Pasta, Limón ni Azúcar: faltan cosas cada día.
        $plan = $this->planificador->generar($this->ids(['Arroz', 'Cebolla', 'Huevo', 'Tomate']));
        $mealPlan = $this->planificador->guardar($plan, 'sesion-plan');

        $this->assertCount(3, $plan['dias']);

        $lista = $this->planificador->listaComprasGeneral($mealPlan);

        // Cada ingrediente aparece una sola vez y la lista sale ordenada por nombre.
        $this->assertSame(['Azúcar', 'Pasta', 'Pollo'], $lista->pluck('ingrediente.nombre')->all());

        // El azúcar falta para el flan los 3 días: una sola línea con 3 usos.
        $azucar = $lista->firstWhere('ingrediente.nombre', 'Azúcar');
        $this->assertEquals([1, 2, 3], array_column($azucar['usos'], 'dia'));
        $this->assertSame(['Flan'], array_values(array_unique(array_column($azucar['usos'], 'plato'))));

        $pasta = $lista->firstWhere('ingrediente.nombre', 'Pasta');
        $this->assertEquals([['dia' => 2, 'plato' => 'Pasta al tomate']], $pasta['usos']);

        $pollo = $lista->firstWhere('ingrediente.nombre', 'Pollo');
        $this->assertEquals([['dia' => 3, 'plato' => 'Arroz con pollo']], $pollo['usos']);
    }

    // --- Opciones del generador que usa el planificador ---

    public function test_excluir_quita_platos_del_resultado(): void
    {
        $generador = app(ComboGeneratorService::class);
        $sinPasta = $this->ids(array_values(array_diff(self::TIENE_TODO, ['Pasta'])));
        $huevos = Dish::where('nombre', 'Huevos rancheros')->value('id');

        $this->assertSame('Huevos rancheros', $generador->generar($sinPasta)['platos'][0]['dish']->nombre);
        $this->assertSame('Arroz con pollo', $generador->generar($sinPasta, ['excluir' => [$huevos]])['platos'][0]['dish']->nombre);
    }

    public function test_penalizar_baja_el_puntaje_sin_descartar_el_plato(): void
    {
        $generador = app(ComboGeneratorService::class);
        $sinPasta = $this->ids(array_values(array_diff(self::TIENE_TODO, ['Pasta'])));
        $ensalada = Dish::where('nombre', 'Ensalada')->value('id');

        $base = $generador->generar($sinPasta)['platos'][1];
        $penalizado = $generador->generar($sinPasta, ['penalizar' => [$ensalada]])['platos'][1];

        // El único acompañamiento sigue apareciendo, pero con 30 puntos menos.
        $this->assertSame('Ensalada', $base['dish']->nombre);
        $this->assertSame('Ensalada', $penalizado['dish']->nombre);
        $this->assertEqualsWithDelta(30, $base['score'] - $penalizado['score'], 0.01);
    }
}
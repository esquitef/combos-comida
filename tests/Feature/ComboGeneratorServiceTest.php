<?php

namespace Tests\Feature;

use App\Models\Combo;
use App\Services\ComboGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreaCatalogoDePrueba;
use Tests\TestCase;

class ComboGeneratorServiceTest extends TestCase
{
    use RefreshDatabase, CreaCatalogoDePrueba;

    /** Todo menos la pasta. */
    private const TIENE_TODO = ['Pollo', 'Arroz', 'Cebolla', 'Tomate', 'Huevo', 'Limón', 'Azúcar'];

    private ComboGeneratorService $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->crearMundo();
        $this->servicio = app(ComboGeneratorService::class);
    }

    private function roles(array $resultado): array
    {
        return array_column($resultado['platos'], 'rol');
    }

    private function nombrePrincipal(?array $resultado): ?string
    {
        return $resultado['platos'][0]['dish']->nombre ?? null;
    }

    public function test_genera_un_combo_completo_cuando_tiene_todo(): void
    {
        $r = $this->servicio->generar($this->ids(self::TIENE_TODO));

        $this->assertNotNull($r);
        $this->assertSame(['principal', 'acompanamiento', 'bebida', 'postre'], $this->roles($r));
        $this->assertTrue($r['lista_compras']->isEmpty());

        foreach ($r['platos'] as $plato) {
            $this->assertTrue($plato['preparable'], "{$plato['dish']->nombre} debería poder prepararse.");
        }

        $sumaCostos = array_sum(array_map(fn ($p) => $p['dish']->costo_aproximado, $r['platos']));
        $this->assertEquals($sumaCostos, $r['costo_total']);
        $this->assertGreaterThan(0, $r['score']);
        $this->assertLessThanOrEqual(100, $r['score']);
    }

    public function test_respeta_el_presupuesto(): void
    {
        $r = $this->servicio->generar($this->ids(self::TIENE_TODO), ['presupuesto' => 50]);

        $this->assertNotNull($r);
        $this->assertLessThanOrEqual(50, $r['costo_total']);
        // Arroz con pollo cuesta 60: no cabe, aunque tenga todos los ingredientes.
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($r));
        // Después del principal y la ensalada quedan $5: no alcanza para bebida ni postre.
        $this->assertNotContains('bebida', $this->roles($r));
        $this->assertNotContains('postre', $this->roles($r));
    }

    public function test_devuelve_null_si_el_presupuesto_no_alcanza(): void
    {
        $this->assertNull($this->servicio->generar($this->ids(self::TIENE_TODO), ['presupuesto' => 10]));
    }

    public function test_filtra_por_tiempo_maximo(): void
    {
        $r = $this->servicio->generar($this->ids(self::TIENE_TODO), ['tiempo_max' => 15]);

        $this->assertNotNull($r);
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($r));
        $this->assertNotContains('postre', $this->roles($r)); // el flan tarda 30 min

        foreach ($r['platos'] as $plato) {
            $this->assertLessThanOrEqual(15, $plato['dish']->tiempo_preparacion_min);
        }
    }

    public function test_devuelve_null_si_no_hay_principal_viable(): void
    {
        // Solo hay limón: ningún plato principal tiene al menos la mitad de sus esenciales.
        $this->assertNull($this->servicio->generar($this->ids(['Limón'])));
    }

    public function test_modo_aprovechamiento_sube_los_platos_con_ingredientes_prioritarios(): void
    {
        $ingredientes = $this->ids(['Pollo', 'Arroz', 'Huevo', 'Tomate']);

        $normal = $this->servicio->generar($ingredientes);
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($normal));

        $aprovechando = $this->servicio->generar($ingredientes, [
            'prioritarios' => $this->ids(['Pollo', 'Arroz']),
        ]);
        $this->assertSame('Arroz con pollo', $this->nombrePrincipal($aprovechando));
    }

    public function test_el_estilo_preferido_desempata_a_su_favor(): void
    {
        $ingredientes = $this->ids(['Huevo', 'Tomate', 'Pasta']); // los dos principales quedan empatados

        $italiano = $this->servicio->generar($ingredientes, ['style_id' => $this->estilos['Italiano']->id]);
        $mexicano = $this->servicio->generar($ingredientes, ['style_id' => $this->estilos['Mexicano']->id]);

        $this->assertSame('Pasta al tomate', $this->nombrePrincipal($italiano));
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($mexicano));
    }

    public function test_el_estilo_no_descarta_platos_de_otros_estilos(): void
    {
        // Pidiendo italiano sin pasta, igual debe salir algo (estilo = bonus, no filtro).
        $r = $this->servicio->generar($this->ids(['Huevo', 'Tomate']), ['style_id' => $this->estilos['Italiano']->id]);

        $this->assertNotNull($r);
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($r));
    }

    public function test_el_objetivo_funciona_como_bonus(): void
    {
        $ingredientes = $this->ids(['Huevo', 'Tomate', 'Pasta']);

        $economica = $this->servicio->generar($ingredientes, ['objetivo' => 'economica']);
        $rapida = $this->servicio->generar($ingredientes, ['objetivo' => 'rapida']);

        $this->assertSame('Pasta al tomate', $this->nombrePrincipal($economica));
        $this->assertSame('Huevos rancheros', $this->nombrePrincipal($rapida));
    }

    public function test_la_lista_de_compras_incluye_solo_esenciales_faltantes(): void
    {
        // Tiene arroz y cebolla; le falta el pollo (esencial). La cebolla es opcional y la tiene.
        $r = $this->servicio->generar($this->ids(['Arroz', 'Cebolla']));

        $this->assertNotNull($r);
        $this->assertCount(1, $r['platos']);
        $this->assertSame('Arroz con pollo', $this->nombrePrincipal($r));
        $this->assertFalse($r['platos'][0]['preparable']);
        $this->assertSame(['Pollo'], $r['platos'][0]['faltantes']->pluck('nombre')->all());

        $this->assertCount(1, $r['lista_compras']);
        $item = $r['lista_compras']->first();
        $this->assertSame('Pollo', $item['ingrediente']->nombre);
        $this->assertSame(['Arroz con pollo'], $item['platos']);
    }

    public function test_sorprendeme_varia_el_resultado(): void
    {
        $ingredientes = $this->ids([...self::TIENE_TODO, 'Pasta']); // tres principales empatados

        $principales = [];
        for ($i = 0; $i < 40; $i++) {
            $r = $this->servicio->generar($ingredientes, [], true);
            $this->assertNotNull($r);
            $principales[] = $this->nombrePrincipal($r);
        }

        $this->assertGreaterThan(1, count(array_unique($principales)), 'Sorpréndeme devolvió siempre el mismo principal.');
        $this->assertEmpty(array_diff($principales, ['Arroz con pollo', 'Huevos rancheros', 'Pasta al tomate']));
    }

    public function test_sin_sorprendeme_el_resultado_es_determinista(): void
    {
        $ingredientes = $this->ids([...self::TIENE_TODO, 'Pasta']);

        $nombres = [];
        for ($i = 0; $i < 5; $i++) {
            $nombres[] = $this->nombrePrincipal($this->servicio->generar($ingredientes));
        }

        $this->assertCount(1, array_unique($nombres));
    }

    public function test_guardar_persiste_combo_platos_y_lista_de_compras(): void
    {
        $r = $this->servicio->generar($this->ids(['Arroz', 'Cebolla']));
        $combo = $this->servicio->guardar($r, 'sesion-prueba');

        $this->assertDatabaseHas('combos', ['id' => $combo->id, 'session_id' => 'sesion-prueba']);
        $this->assertDatabaseHas('combo_dishes', [
            'combo_id' => $combo->id,
            'dish_id'  => $r['platos'][0]['dish']->id,
            'rol'      => 'principal',
        ]);
        $this->assertNotNull($combo->shoppingList);
        $this->assertDatabaseHas('shopping_list_items', [
            'shopping_list_id' => $combo->shoppingList->id,
            'ingredient_id'    => $this->ing['Pollo']->id,
        ]);
    }

    public function test_no_crea_lista_de_compras_si_no_falta_nada(): void
    {
        $r = $this->servicio->generar($this->ids(self::TIENE_TODO));
        $combo = $this->servicio->guardar($r, 'sesion-prueba');

        $this->assertNull($combo->fresh()->shoppingList);
    }

    public function test_el_historial_devuelve_solo_los_ultimos_de_la_sesion(): void
    {
        for ($i = 0; $i < 7; $i++) {
            Combo::create(['session_id' => 'sesion-a', 'score' => 50]);
        }
        for ($i = 0; $i < 2; $i++) {
            Combo::create(['session_id' => 'sesion-b', 'score' => 50]);
        }

        $a = $this->servicio->historial('sesion-a');
        $this->assertCount(5, $a);
        $this->assertTrue($a->every(fn ($c) => $c->session_id === 'sesion-a'));
        $this->assertCount(3, $this->servicio->historial('sesion-a', 3));
        $this->assertCount(2, $this->servicio->historial('sesion-b'));
        $this->assertCount(0, $this->servicio->historial('sesion-c'));
    }
}
<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Services\ComboGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\Concerns\CreaCatalogoDePrueba;
use Tests\TestCase;

class FotosPlatosTest extends TestCase
{
    use RefreshDatabase, CreaCatalogoDePrueba;

    // Plato inventado para las pruebas: así nunca se toca una foto real de public/img/platos.
    private const PLATO = 'Plato de prueba fotos';
    private const SLUG = 'plato-de-prueba-fotos';

    /** @var string[] Archivos creados por la prueba, para borrarlos al terminar. */
    private array $archivos = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->crearMundo();
        $this->crearPlato(self::PLATO, 'principal', 10, 10, 'Mexicano', 'rapida', ['Pollo']);
        File::ensureDirectoryExists(public_path('img/platos'));
    }

    protected function tearDown(): void
    {
        foreach ($this->archivos as $archivo) {
            File::delete($archivo);
        }

        parent::tearDown();
    }

    private function ponerFoto(string $extension = 'jpg'): void
    {
        $ruta = public_path('img/platos/' . self::SLUG . '.' . $extension);
        File::put($ruta, 'foto de prueba');
        $this->archivos[] = $ruta;
    }

    private function plato(): Dish
    {
        return Dish::where('nombre', self::PLATO)->firstOrFail();
    }

    public function test_el_nombre_del_archivo_se_arma_con_el_nombre_del_plato(): void
    {
        $this->assertSame(self::SLUG, $this->plato()->slug);

        $acentos = $this->crearPlato('Ñoquis de papa', 'principal', 45, 45, 'Italiano', 'economica', ['Pasta']);
        $this->assertSame('noquis-de-papa', $acentos->slug);
    }

    public function test_sin_archivo_no_hay_foto(): void
    {
        $this->assertNull($this->plato()->foto_url);
    }

    public function test_con_archivo_devuelve_su_url(): void
    {
        $this->ponerFoto('jpg');

        $this->assertSame(asset('img/platos/' . self::SLUG . '.jpg'), $this->plato()->foto_url);
    }

    public function test_webp_tiene_prioridad_sobre_jpg(): void
    {
        $this->ponerFoto('jpg');
        $this->ponerFoto('webp');

        $this->assertSame(asset('img/platos/' . self::SLUG . '.webp'), $this->plato()->foto_url);
    }

    public function test_la_pantalla_del_combo_muestra_la_foto_si_existe(): void
    {
        $this->ponerFoto('jpg');

        $sesion = Str::random(40);
        $servicio = app(ComboGeneratorService::class);
        $combo = $servicio->guardar($servicio->generar($this->ids(['Pollo'])), $sesion);

        $this->withCookie(config('session.cookie'), $sesion)
            ->get(route('combos.show', $combo))
            ->assertOk()
            ->assertSee(self::PLATO)
            ->assertSee('img/platos/' . self::SLUG . '.jpg', false)
            ->assertSee('class="plato-foto"', false)
            ->assertDontSee('plato-sin-foto', false);
    }

    public function test_la_pantalla_del_combo_funciona_sin_foto(): void
    {
        $sesion = Str::random(40);
        $servicio = app(ComboGeneratorService::class);
        $combo = $servicio->guardar($servicio->generar($this->ids(['Pollo'])), $sesion);

        $this->withCookie(config('session.cookie'), $sesion)
            ->get(route('combos.show', $combo))
            ->assertOk()
            ->assertSee(self::PLATO)
            ->assertSee('plato-sin-foto', false)
            ->assertDontSee('class="plato-foto"', false);
    }

    public function test_el_comando_lista_los_platos_sin_foto(): void
    {
        $this->artisan('platos:fotos')
            ->expectsOutputToContain(self::SLUG)
            ->expectsOutputToContain('arroz-con-pollo')
            ->assertExitCode(0);
    }

    public function test_el_comando_no_lista_los_que_ya_tienen_foto(): void
    {
        $this->ponerFoto('jpg');

        $this->artisan('platos:fotos')
            ->expectsOutputToContain('arroz-con-pollo')
            ->doesntExpectOutputToContain(self::SLUG . '.webp')
            ->assertExitCode(0);
    }

    public function test_el_comando_filtra_por_tipo(): void
    {
        $this->artisan('platos:fotos', ['--tipo' => 'bebida'])
            ->expectsOutputToContain('agua-de-limon')
            ->doesntExpectOutputToContain('arroz-con-pollo')
            ->assertExitCode(0);
    }
}

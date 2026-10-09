<?php

namespace App\Services;

use App\Models\Combo;
use App\Models\Dish;
use App\Models\ShoppingList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Genera combos de comida (principal + acompañamiento + bebida + postre)
 * a partir de los ingredientes disponibles y las preferencias del usuario.
 */
class ComboGeneratorService
{
    /** Fracción mínima de ingredientes esenciales que debe tener el usuario para considerar un plato. */
    private const MIN_DISPONIBILIDAD = 0.5;

    // Pesos del puntaje de cada plato (máximo 100).
    private const PESO_ESENCIALES = 70;
    private const PESO_OPCIONALES = 10;
    private const BONUS_PRIORITARIO = 10;   // por cada ingrediente prioritario que el plato usa
    private const TOPE_PRIORITARIOS = 25;
    private const BONUS_OBJETIVO = 5;
    private const BONUS_ESTILO = 5;
    private const BONUS_COMPATIBILIDAD = 10; // acompañamiento/bebida/postre del mismo estilo que el principal

    /** En modo "Sorpréndeme" se elige al azar entre las N mejores opciones. */
    private const TOP_ALEATORIO = 3;

    private const ROLES_EXTRA = ['acompanamiento', 'bebida', 'postre'];

    /**
     * Genera un combo completo.
     *
     * @param  int[]  $ingredientesIds  IDs de los ingredientes que el usuario tiene.
     * @param  array  $preferencias     tiempo_max, presupuesto, style_id, objetivo, prioritarios (todas opcionales).
     * @param  bool   $aleatorio        true = modo "Sorpréndeme".
     * @return array|null  null si no hay ningún principal viable.
     */
    public function generar(array $ingredientesIds, array $preferencias = [], bool $aleatorio = false): ?array
    {
        $prefs = $this->normalizarPreferencias($preferencias);
        $disponibles = array_flip(array_map('intval', $ingredientesIds));

        // 1) Filtrar candidatos (filtro duro: tiempo) y 2) puntuar cada plato.
        $puntuados = $this->platosCandidatos($prefs['tiempo_max'])
            ->map(fn (Dish $d) => $this->puntuarPlato($d, $disponibles, $prefs));

        // 3) Elegir el plato principal.
        $principal = $this->elegir(
            $puntuados
                ->where('tipo', 'principal')
                ->filter(fn ($i) => $this->esViable($i, $prefs['presupuesto']))
                ->sortBy($this->orden()),
            $aleatorio
        );

        if ($principal === null) {
            return null;
        }

        // 4) Completar el combo con acompañamiento, bebida y postre "cuando sea posible".
        $platosCombo = [$principal];
        $costoTotal = $principal['costo'];

        foreach (self::ROLES_EXTRA as $rol) {
            $restante = $prefs['presupuesto'] === null ? null : $prefs['presupuesto'] - $costoTotal;

            $opciones = $puntuados
                ->where('tipo', $rol)
                ->filter(fn ($i) => $this->esViable($i, $restante))
                ->map(fn ($i) => $this->aplicarCompatibilidad($i, $principal))
                ->sortBy($this->orden());

            $extra = $this->elegir($opciones, $aleatorio);

            if ($extra !== null) {
                $platosCombo[] = $extra;
                $costoTotal += $extra['costo'];
            }
        }

        // 5) Comparar disponibilidad y armar la lista de compras.
        return [
            'platos' => array_map(fn ($i) => [
                'rol'        => $i['tipo'],
                'dish'       => $i['dish'],
                'score'      => round($i['score'], 2),
                'preparable' => $i['faltantes']->isEmpty(),
                'faltantes'  => $i['faltantes'],
            ], $platosCombo),
            'score'         => $this->puntajeCombo($platosCombo),
            'costo_total'   => $costoTotal,
            'tiempo_total'  => array_sum(array_map(fn ($i) => $i['dish']->tiempo_preparacion_min, $platosCombo)),
            'lista_compras' => $this->armarListaCompras($platosCombo),
        ];
    }

    /**
     * Guarda el combo generado (con su lista de compras) para el historial.
     */
    public function guardar(array $resultado, string $sessionId): Combo
    {
        return DB::transaction(function () use ($resultado, $sessionId) {
            $combo = Combo::create([
                'session_id' => $sessionId,
                'score'      => $resultado['score'],
            ]);

            foreach ($resultado['platos'] as $item) {
                $combo->dishes()->attach($item['dish']->id, ['rol' => $item['rol']]);
            }

            if ($resultado['lista_compras']->isNotEmpty()) {
                $lista = ShoppingList::create(['combo_id' => $combo->id]);

                foreach ($resultado['lista_compras'] as $item) {
                    $lista->ingredients()->attach($item['ingrediente']->id, ['cantidad' => $item['cantidad']]);
                }
            }

            return $combo;
        });
    }

    /**
     * Últimos combos generados por una sesión.
     */
    public function historial(string $sessionId, int $cantidad = 5): Collection
    {
        return Combo::with('dishes')
            ->where('session_id', $sessionId)
            ->latest()
            ->take($cantidad)
            ->get();
    }

    // ------------------------------------------------------------------
    // Métodos internos
    // ------------------------------------------------------------------

    private function normalizarPreferencias(array $p): array
    {
        $lleno = fn (string $k) => isset($p[$k]) && $p[$k] !== '';

        return [
            'tiempo_max'   => $lleno('tiempo_max') ? (int) $p['tiempo_max'] : null,
            'presupuesto'  => $lleno('presupuesto') ? (float) $p['presupuesto'] : null,
            'style_id'     => $lleno('style_id') ? (int) $p['style_id'] : null,
            'objetivo'     => $lleno('objetivo') ? $p['objetivo'] : null,
            'prioritarios' => array_map('intval', $p['prioritarios'] ?? []),
        ];
    }

    private function platosCandidatos(?int $tiempoMax): Collection
    {
        return Dish::with(['ingredients', 'style'])
            ->when($tiempoMax, fn ($q) => $q->where('tiempo_preparacion_min', '<=', $tiempoMax))
            ->get();
    }

    /**
     * Calcula el puntaje (0-100) de un plato y qué ingredientes esenciales le faltan.
     */
    private function puntuarPlato(Dish $plato, array $disponibles, array $prefs): array
    {
        $esenciales = $plato->ingredients->filter(fn ($i) => ! (bool) $i->pivot->es_opcional);
        $opcionales = $plato->ingredients->filter(fn ($i) => (bool) $i->pivot->es_opcional);

        $tengo = fn ($i) => isset($disponibles[$i->id]);

        $dispEsenciales = $esenciales->isEmpty() ? 1 : $esenciales->filter($tengo)->count() / $esenciales->count();
        $dispOpcionales = $opcionales->isEmpty() ? 1 : $opcionales->filter($tengo)->count() / $opcionales->count();

        $score = $dispEsenciales * self::PESO_ESENCIALES + $dispOpcionales * self::PESO_OPCIONALES;

        // Modo aprovechamiento: premia los platos que usan los ingredientes prioritarios.
        if (! empty($prefs['prioritarios'])) {
            $prioritarios = array_flip($prefs['prioritarios']);
            $usados = $plato->ingredients
                ->filter(fn ($i) => isset($prioritarios[$i->id]) && isset($disponibles[$i->id]))
                ->count();

            $score += min(self::TOPE_PRIORITARIOS, $usados * self::BONUS_PRIORITARIO);
        }

        if ($prefs['objetivo'] !== null && $plato->objetivo === $prefs['objetivo']) {
            $score += self::BONUS_OBJETIVO;
        }

        if ($prefs['style_id'] !== null && $plato->style_id == $prefs['style_id']) {
            $score += self::BONUS_ESTILO;
        }

        return [
            'dish'           => $plato,
            'tipo'           => $plato->tipo,
            'costo'          => (float) $plato->costo_aproximado,
            'score'          => min(100, $score),
            'disponibilidad' => $dispEsenciales,
            'faltantes'      => $esenciales->reject($tengo)->values(),
        ];
    }

    /** Un plato es viable si el usuario tiene suficientes esenciales y cabe en el presupuesto. */
    private function esViable(array $item, ?float $presupuestoRestante): bool
    {
        return $item['disponibilidad'] >= self::MIN_DISPONIBILIDAD
            && ($presupuestoRestante === null || $item['costo'] <= $presupuestoRestante);
    }

    /** Compatibilidad de estilo: factor de puntaje, no regla dura. */
    private function aplicarCompatibilidad(array $item, array $principal): array
    {
        if ($item['dish']->style_id == $principal['dish']->style_id) {
            $item['score'] = min(100, $item['score'] + self::BONUS_COMPATIBILIDAD);
        }

        return $item;
    }

    private function orden(): array
    {
        return [['score', 'desc'], ['costo', 'asc']];
    }

    private function elegir(Collection $ordenados, bool $aleatorio): ?array
    {
        if ($ordenados->isEmpty()) {
            return null;
        }

        return $aleatorio
            ? $ordenados->take(self::TOP_ALEATORIO)->random()
            : $ordenados->first();
    }

    /** El principal pesa 55% del puntaje total; el resto se reparte entre los demás platos. */
    private function puntajeCombo(array $platos): float
    {
        $principal = $platos[0]['score'];
        $extras = array_slice($platos, 1);

        if (empty($extras)) {
            return round($principal, 2);
        }

        $promedio = array_sum(array_column($extras, 'score')) / count($extras);

        return round($principal * 0.55 + $promedio * 0.45, 2);
    }

    /** Une los ingredientes esenciales faltantes de todos los platos en una sola lista. */
    private function armarListaCompras(array $platosCombo): Collection
    {
        $lista = [];

        foreach ($platosCombo as $item) {
            foreach ($item['faltantes'] as $ing) {
                $lista[$ing->id] ??= ['ingrediente' => $ing, 'cantidad' => 0.0, 'platos' => []];
                $lista[$ing->id]['cantidad'] += (float) $ing->pivot->cantidad;
                $lista[$ing->id]['platos'][] = $item['dish']->nombre;
            }
        }

        return collect(array_values($lista));
    }
}
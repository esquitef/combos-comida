<?php

namespace App\Services;

use App\Models\MealPlan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Arma un plan de varios días reutilizando el generador de combos:
 * no repite platos principales, procura no repetir acompañamientos, bebidas ni postres,
 * y reparte los ingredientes que el usuario quiere aprovechar a lo largo de la semana.
 *
 * Supuesto: los ingredientes marcados están disponibles durante todo el plan
 * (el sistema no lleva inventario de cantidades).
 */
class PlanSemanalService
{
    public const DIAS_POR_DEFECTO = 5;
    private const MAX_DIAS = 7;

    public function __construct(private ComboGeneratorService $generador)
    {
    }

    /**
     * Genera el plan en memoria (todavía no lo guarda).
     *
     * @param  int[]  $ingredientesIds  IDs de los ingredientes disponibles.
     * @param  array  $preferencias     Las mismas del generador. El presupuesto se aplica por día.
     * @param  int    $dias             Cantidad de días a planear (1 a 7).
     * @return array{dias: array, solicitados: int, completo: bool}
     *         'dias' es una lista de ['dia' => n, 'resultado' => <resultado de ComboGeneratorService>].
     *         Si no alcanzan los platos, trae menos días de los solicitados y 'completo' es false.
     */
    public function generar(array $ingredientesIds, array $preferencias = [], int $dias = self::DIAS_POR_DEFECTO): array
    {
        $dias = max(1, min(self::MAX_DIAS, $dias));

        $principalesUsados = [];
        $extrasUsados = [];
        $prioritarios = array_map('intval', $preferencias['prioritarios'] ?? []);
        $plan = [];

        for ($dia = 1; $dia <= $dias; $dia++) {
            $resultado = $this->generador->generar($ingredientesIds, array_merge($preferencias, [
                'prioritarios' => $prioritarios,
                'excluir'      => $principalesUsados,
                'penalizar'    => $extrasUsados,
            ]));

            // Sin principales nuevos viables no se puede seguir: el plan queda más corto.
            if ($resultado === null) {
                break;
            }

            $plan[] = ['dia' => $dia, 'resultado' => $resultado];

            foreach ($resultado['platos'] as $p) {
                if ($p['rol'] === 'principal') {
                    $principalesUsados[] = $p['dish']->id;
                } else {
                    $extrasUsados[] = $p['dish']->id;
                }
            }

            // Un ingrediente prioritario ya aprovechado deja de empujar los días siguientes.
            $aprovechados = collect($resultado['platos'])
                ->flatMap(fn ($p) => $p['dish']->ingredients->pluck('id'))
                ->all();
            $prioritarios = array_values(array_diff($prioritarios, $aprovechados));
        }

        return [
            'dias'        => $plan,
            'solicitados' => $dias,
            'completo'    => count($plan) === $dias,
        ];
    }

    /** Guarda el plan: un combo por día, todos ligados al mismo MealPlan. */
    public function guardar(array $plan, string $sessionId): MealPlan
    {
        return DB::transaction(function () use ($plan, $sessionId) {
            $mealPlan = MealPlan::create(['session_id' => $sessionId]);

            foreach ($plan['dias'] as $d) {
                $this->generador->guardar($d['resultado'], $sessionId, $mealPlan->id, $d['dia']);
            }

            return $mealPlan;
        });
    }

    /**
     * Lista de compras de toda la semana: cada ingrediente faltante aparece una sola vez,
     * con los días y los platos que lo necesitan.
     *
     * @return Collection<int, array{ingrediente: \App\Models\Ingredient, usos: array<int, array{dia: int, plato: string}>}>
     */
    public function listaComprasGeneral(MealPlan $plan): Collection
    {
        $plan->load(['combos.dishes.ingredients', 'combos.shoppingList.ingredients']);

        $lista = [];

        foreach ($plan->combos as $combo) {
            $faltantes = $combo->shoppingList?->ingredients ?? collect();

            foreach ($faltantes as $ing) {
                $lista[$ing->id] ??= ['ingrediente' => $ing, 'usos' => []];

                // Platos de ese día que necesitan el ingrediente como esencial.
                foreach ($combo->dishes as $plato) {
                    $loNecesita = $plato->ingredients->contains(
                        fn ($i) => $i->id == $ing->id && ! $i->pivot->es_opcional
                    );

                    if ($loNecesita) {
                        $lista[$ing->id]['usos'][] = ['dia' => $combo->dia, 'plato' => $plato->nombre];
                    }
                }
            }
        }

        return collect($lista)
            ->sortBy(fn ($item) => $item['ingrediente']->nombre)
            ->values();
    }
}
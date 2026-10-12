<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Services\PlanSemanalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PlanController extends Controller
{
    private const OBJETIVOS = 'economica,rapida,ligera,alta_proteina';

    private const ROLES = [
        'principal'      => 'Plato principal',
        'acompanamiento' => 'Acompañamiento',
        'bebida'         => 'Bebida',
        'postre'         => 'Postre',
    ];

    public function __construct(private PlanSemanalService $planificador)
    {
    }

    /** Valida el formulario, arma el plan semanal, lo guarda y redirige a su pantalla. */
    public function generar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'ingredientes'   => ['required', 'array', 'min:1'],
            'ingredientes.*' => ['integer', 'exists:ingredients,id'],
            'prioritarios'   => ['nullable', 'array'],
            'prioritarios.*' => ['integer', 'exists:ingredients,id'],
            'tiempo_max'     => ['nullable', 'integer', 'min:5', 'max:240'],
            'presupuesto'    => ['nullable', 'numeric', 'min:1', 'max:100000'],
            'style_id'       => ['nullable', 'exists:styles,id'],
            'objetivo'       => ['nullable', 'in:' . self::OBJETIVOS],
        ], [
            'ingredientes.required' => 'Selecciona al menos un ingrediente.',
            'ingredientes.min'      => 'Selecciona al menos un ingrediente.',
            'tiempo_max.integer'    => 'El tiempo debe ser un número de minutos.',
            'presupuesto.numeric'   => 'El presupuesto debe ser un número.',
            'presupuesto.min'       => 'El presupuesto debe ser mayor a cero.',
        ]);

        $plan = $this->planificador->generar($datos['ingredientes'], $datos);

        if (empty($plan['dias'])) {
            return back()->withInput()->withErrors([
                'combo' => 'No encontramos ningún plato principal viable con esos ingredientes y preferencias. '
                    . 'Prueba con más ingredientes o relaja el tiempo, el presupuesto o el estilo.',
            ]);
        }

        $mealPlan = $this->planificador->guardar($plan, $request->session()->getId());
        $redireccion = redirect()->route('planes.show', $mealPlan);

        if (! $plan['completo']) {
            $redireccion->with('aviso', sprintf(
                'Solo pudimos armar %d de %d días: con esos ingredientes y preferencias no hay más platos principales distintos. '
                . 'Agrega más ingredientes o relaja alguna preferencia para completar la semana.',
                count($plan['dias']),
                $plan['solicitados']
            ));
        }

        return $redireccion;
    }

    /** Muestra el plan día por día y la lista de compras de toda la semana. */
    public function show(Request $request, MealPlan $plan): View
    {
        // Cada visitante solo puede ver los planes de su propia sesión.
        abort_unless($plan->session_id === $request->session()->getId(), 404);

        $plan->load(['combos.dishes.style', 'combos.dishes.ingredients', 'combos.shoppingList.ingredients']);

        $dias = $plan->combos->map(function ($combo) {
            // La lista de compras de cada día guarda los esenciales que le faltan al usuario.
            $idsFaltantes = ($combo->shoppingList?->ingredients ?? collect())->pluck('id');

            $platos = $this->ordenarPlatos($combo->dishes)->map(function ($dish) use ($idsFaltantes) {
                $faltan = $dish->ingredients
                    ->filter(fn ($i) => ! $i->pivot->es_opcional && $idsFaltantes->contains($i->id))
                    ->count();

                return [
                    'dish'     => $dish,
                    'etiqueta' => self::ROLES[$dish->pivot->rol] ?? ucfirst($dish->pivot->rol),
                    'faltan'   => $faltan,
                ];
            });

            return [
                'dia'    => $combo->dia,
                'combo'  => $combo,
                'platos' => $platos,
                'costo'  => $combo->dishes->sum('costo_aproximado'),
                'tiempo' => $combo->dishes->sum('tiempo_preparacion_min'),
            ];
        });

        return view('planes.show', [
            'plan'         => $plan,
            'dias'         => $dias,
            'costoTotal'   => $dias->sum('costo'),
            'listaCompras' => $this->planificador->listaComprasGeneral($plan),
        ]);
    }

    /** Ordena los platos de un día: principal, acompañamiento, bebida, postre. */
    private function ordenarPlatos(Collection $platos): Collection
    {
        $orden = array_keys(self::ROLES);

        return $platos->sortBy(fn ($d) => array_search($d->pivot->rol, $orden))->values();
    }
}
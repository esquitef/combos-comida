<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Combo;
use App\Models\MealPlan;
use App\Models\Style;
use App\Services\ComboGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ComboController extends Controller
{
    private const OBJETIVOS = [
        'economica'     => 'Económica',
        'rapida'        => 'Rápida',
        'ligera'        => 'Ligera',
        'alta_proteina' => 'Alta en proteína',
    ];

    private const ROLES = [
        'principal'      => 'Plato principal',
        'acompanamiento' => 'Acompañamiento',
        'bebida'         => 'Bebida',
        'postre'         => 'Postre',
    ];

    public function __construct(private ComboGeneratorService $servicio)
    {
    }

    /** Pantalla principal: selección de ingredientes y preferencias. */
    public function index(): View
    {
        return view('combos.index', [
            'categorias' => Category::with(['ingredients' => fn ($q) => $q->orderBy('nombre')])
                ->orderBy('id')
                ->get(),
            'estilos'   => Style::orderBy('nombre')->get(),
            'objetivos' => self::OBJETIVOS,
        ]);
    }

    /** Valida el formulario, genera el combo, lo guarda y redirige a su pantalla. */
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
            'objetivo'       => ['nullable', 'in:' . implode(',', array_keys(self::OBJETIVOS))],
        ], [
            'ingredientes.required' => 'Selecciona al menos un ingrediente.',
            'ingredientes.min'      => 'Selecciona al menos un ingrediente.',
            'tiempo_max.integer'    => 'El tiempo debe ser un número de minutos.',
            'presupuesto.numeric'   => 'El presupuesto debe ser un número.',
            'presupuesto.min'       => 'El presupuesto debe ser mayor a cero.',
        ]);

        // $datos ya trae las preferencias con los nombres que espera el servicio.
        $resultado = $this->servicio->generar(
            $datos['ingredientes'],
            $datos,
            $request->boolean('sorprende')
        );

        if ($resultado === null) {
            return back()->withInput()->withErrors([
                'combo' => 'No encontramos un plato principal viable con esos ingredientes y preferencias. '
                    . 'Prueba con más ingredientes o relaja el tiempo, el presupuesto o el estilo.',
            ]);
        }

        $combo = $this->servicio->guardar($resultado, $request->session()->getId());

        return redirect()->route('combos.show', $combo);
    }

    /** Muestra un combo guardado con su disponibilidad y su lista de compras. */
    public function show(Request $request, Combo $combo): View
    {
        // Cada visitante solo puede ver los combos de su propia sesión.
        abort_unless($combo->session_id === $request->session()->getId(), 404);

        $combo->load(['dishes.style', 'dishes.ingredients', 'shoppingList.ingredients']);

        // La lista de compras guarda los esenciales que le faltan al usuario.
        // Como la disponibilidad es por ingrediente, de ahí se deduce qué le falta a cada plato.
        $porComprar = $combo->shoppingList?->ingredients ?? collect();
        $idsFaltantes = $porComprar->pluck('id');

        $platos = $this->ordenarPlatos($combo->dishes)->map(function ($dish) use ($idsFaltantes) {
            $esenciales = $dish->ingredients->filter(fn ($i) => ! $i->pivot->es_opcional)->values();

            return [
                'dish'       => $dish,
                'etiqueta'   => self::ROLES[$dish->pivot->rol] ?? ucfirst($dish->pivot->rol),
                'esenciales' => $esenciales,
                'opcionales' => $dish->ingredients->filter(fn ($i) => (bool) $i->pivot->es_opcional)->values(),
                'faltantes'  => $esenciales->whereIn('id', $idsFaltantes),
            ];
        });

        $listaCompras = $porComprar->map(fn ($ing) => [
            'ingrediente' => $ing,
            'platos'      => $platos
                ->filter(fn ($p) => $p['faltantes']->contains('id', $ing->id))
                ->map(fn ($p) => $p['dish']->nombre)
                ->values(),
        ]);

        return view('combos.show', [
            'combo'        => $combo,
            'platos'       => $platos,
            'listaCompras' => $listaCompras,
            'costoTotal'   => $combo->dishes->sum('costo_aproximado'),
            'tiempoTotal'  => $combo->dishes->sum('tiempo_preparacion_min'),
            'objetivos'    => self::OBJETIVOS,
        ]);
    }

    /** Últimos 5 combos y últimos 5 planes generados en esta sesión. */
public function historial(Request $request): View
{
    $sessionId = $request->session()->getId();

    $combos = $this->servicio->historial($sessionId, 5)
        ->map(fn ($combo) => [
            'combo'  => $combo,
            'platos' => $this->ordenarPlatos($combo->dishes),
        ]);

    $planes = MealPlan::withCount('combos')
        ->where('session_id', $sessionId)
        ->latest()
        ->take(5)
        ->get();

    return view('combos.historial', ['combos' => $combos, 'planes' => $planes]);
}
    /** Ordena los platos de un combo: principal, acompañamiento, bebida, postre. */
    private function ordenarPlatos(Collection $platos): Collection
    {
        $orden = array_keys(self::ROLES);

        return $platos->sortBy(fn ($d) => array_search($d->pivot->rol, $orden))->values();
    }
}
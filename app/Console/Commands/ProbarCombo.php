<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use App\Models\Style;
use App\Services\ComboGeneratorService;
use Illuminate\Console\Command;

class ProbarCombo extends Command
{
    protected $signature = 'combo:probar
        {ingredientes* : Nombres de los ingredientes disponibles}
        {--tiempo= : Minutos máximos por plato}
        {--presupuesto= : Presupuesto máximo aproximado}
        {--estilo= : Nombre del estilo (ej. Mexicano)}
        {--objetivo= : economica, rapida, ligera o alta_proteina}
        {--aprovechar=* : Ingredientes a usar primero (modo aprovechamiento)}
        {--sorprende : Elegir al azar entre las mejores opciones}';

    protected $description = 'Prueba el generador de combos desde la terminal';

    public function handle(ComboGeneratorService $servicio): int
    {
        $ids = $this->idsPorNombre($this->argument('ingredientes'));
        $prioritarios = $this->idsPorNombre($this->option('aprovechar'));

        $styleId = null;
        if ($this->option('estilo')) {
            $estilo = Style::where('nombre', $this->option('estilo'))->first();
            if (! $estilo) {
                $this->warn("Estilo no encontrado: {$this->option('estilo')}");
            }
            $styleId = $estilo?->id;
        }

        $resultado = $servicio->generar($ids, [
            'tiempo_max'   => $this->option('tiempo'),
            'presupuesto'  => $this->option('presupuesto'),
            'style_id'     => $styleId,
            'objetivo'     => $this->option('objetivo'),
            'prioritarios' => $prioritarios,
        ], (bool) $this->option('sorprende'));

        if ($resultado === null) {
            $this->error('No se encontró ningún plato principal viable con esos ingredientes y preferencias.');

            return self::FAILURE;
        }

        $filas = [];
        foreach ($resultado['platos'] as $p) {
            $filas[] = [
                ucfirst($p['rol']),
                $p['dish']->nombre,
                $p['dish']->style->nombre,
                $p['dish']->tiempo_preparacion_min . ' min',
                '$' . $p['dish']->costo_aproximado,
                round($p['score']),
                $p['preparable'] ? 'Sí' : 'Faltan: ' . $p['faltantes']->pluck('nombre')->implode(', '),
            ];
        }

        $this->table(['Rol', 'Plato', 'Estilo', 'Tiempo', 'Costo', 'Score', 'Preparable'], $filas);
        $this->info("Score del combo: {$resultado['score']}  |  Costo total: \${$resultado['costo_total']}  |  Tiempo total: {$resultado['tiempo_total']} min");

        if ($resultado['lista_compras']->isEmpty()) {
            $this->info('Lista de compras: no falta nada.');
        } else {
            $this->line('Lista de compras:');
            foreach ($resultado['lista_compras'] as $item) {
                $ing = $item['ingrediente'];
                $this->line("  - {$ing->nombre} ({$item['cantidad']} {$ing->unidad_medida}) para: " . implode(', ', $item['platos']));
            }
        }

        return self::SUCCESS;
    }

    private function idsPorNombre(array $nombres): array
    {
        $ids = [];

        foreach ($nombres as $nombre) {
            $ingrediente = Ingredient::where('nombre', $nombre)->first();

            if ($ingrediente) {
                $ids[] = $ingrediente->id;
            } else {
                $this->warn("Ingrediente no encontrado: {$nombre}");
            }
        }

        return $ids;
    }
}
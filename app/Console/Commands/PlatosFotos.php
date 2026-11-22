<?php

namespace App\Console\Commands;

use App\Models\Dish;
use Illuminate\Console\Command;

class PlatosFotos extends Command
{
    protected $signature = 'platos:fotos
        {--estilo= : Solo los platos de este estilo (ej. Mexicano)}
        {--tipo= : Solo este tipo: principal, acompanamiento, bebida o postre}';

    protected $description = 'Muestra qué platos todavía no tienen foto en public/img/platos';

    public function handle(): int
    {
        $platos = Dish::with('style')
            ->when($this->option('estilo'), fn ($q, $estilo) => $q->whereHas('style', fn ($s) => $s->where('nombre', $estilo)))
            ->when($this->option('tipo'), fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->orderBy('style_id')
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->get();

        if ($platos->isEmpty()) {
            $this->warn('No hay platos con ese filtro.');

            return self::SUCCESS;
        }

        $faltan = $platos->filter(fn (Dish $plato) => $plato->foto_url === null);

        $this->info('Platos con foto: ' . ($platos->count() - $faltan->count()) . ' de ' . $platos->count());

        if ($faltan->isEmpty()) {
            $this->info('¡Ya están todas las fotos!');

            return self::SUCCESS;
        }

        $this->line('');
        $this->line('Guarda cada foto en public/img/platos/ con el nombre indicado (.webp, .jpg, .jpeg o .png):');

        foreach ($faltan->groupBy(fn (Dish $plato) => $plato->style->nombre) as $estilo => $grupo) {
            $this->line('');
            $this->info($estilo . ' (' . $grupo->count() . ')');

            foreach ($grupo as $plato) {
                $this->line("  - {$plato->nombre}  →  {$plato->slug}.webp");
            }
        }

        return self::SUCCESS;
    }
}

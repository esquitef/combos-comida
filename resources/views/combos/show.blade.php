@extends('layouts.app')

@section('titulo', 'Tu combo')

@section('contenido')
    <a class="volver" href="{{ route('combos.index') }}">← Generar otro combo</a>
    <h1>Tu combo</h1>

    <div class="resumen">
        <div><span>Puntaje</span><strong>{{ number_format($combo->score, 0) }}/100</strong></div>
        <div><span>Costo aproximado</span><strong>${{ number_format($costoTotal, 0) }}</strong></div>
        <div><span>Tiempo total</span><strong>{{ $tiempoTotal }} min</strong></div>
    </div>

    @foreach ($platos as $p)
        @php $completo = $p['faltantes']->isEmpty(); @endphp
        <article class="plato {{ $completo ? '' : 'plato-falta' }}">
            <div class="plato-cab">
                <span class="rol">{{ $p['etiqueta'] }}</span>
                <span class="estado">
                    {{ $completo ? '✓ Lo tienes todo' : 'Te falta ' . $p['faltantes']->count() . ' ingrediente(s)' }}
                </span>
            </div>
            <h2>{{ $p['dish']->nombre }}</h2>
            <p class="meta">
                {{ $p['dish']->style->nombre }} · {{ $p['dish']->tiempo_preparacion_min }} min · ${{ number_format($p['dish']->costo_aproximado, 0) }}
                @if ($p['dish']->objetivo)
                    · {{ $objetivos[$p['dish']->objetivo] ?? $p['dish']->objetivo }}
                @endif
            </p>
            <ul class="ings">
                @foreach ($p['esenciales'] as $ing)
                    <li class="{{ $p['faltantes']->contains('id', $ing->id) ? 'falta' : 'ok' }}">{{ $ing->nombre }}</li>
                @endforeach
                @foreach ($p['opcionales'] as $ing)
                    <li class="opc">{{ $ing->nombre }} (opcional)</li>
                @endforeach
            </ul>
        </article>
    @endforeach

    <section class="card">
        <h2>Lista de compras</h2>
        @if ($listaCompras->isEmpty())
            <p>No te falta nada: puedes preparar todo el combo con lo que ya tienes.</p>
        @else
            <ul class="compras">
                @foreach ($listaCompras as $item)
                    <li>
                        <label>
                            <input type="checkbox">
                            {{ $item['ingrediente']->nombre }}
                            <small>— para {{ $item['platos']->implode(', ') }}</small>
                        </label>
                    </li>
                @endforeach
            </ul>
            <button type="button" class="btn btn-alt" onclick="window.print()">Imprimir lista</button>
        @endif
    </section>
@endsection
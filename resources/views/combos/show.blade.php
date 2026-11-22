@extends('layouts.app')

@section('titulo', 'Tu combo')

@section('contenido')
    @php
        $iconosRol = config('comibox.emoji_roles');
    @endphp

    <a class="volver" href="{{ route('combos.index') }}">← Generar otro combo</a>
    <h1>Tu combo</h1>
    <p class="sub">Esto es lo que mejor se adapta a lo que tienes y a tus preferencias.</p>

    <div class="resumen">
        <div class="dato">
            <div class="anillo" style="--p: {{ min(100, max(0, (int) round($combo->score))) }}"><b>{{ number_format($combo->score, 0) }}</b></div>
            <div><span>Puntaje</span><strong>de 100</strong></div>
        </div>
        <div class="dato">
            <div class="dato-ico">💰</div>
            <div><span>Costo aproximado</span><strong>${{ number_format($costoTotal, 0) }}</strong></div>
        </div>
        <div class="dato">
            <div class="dato-ico">⏱️</div>
            <div><span>Tiempo total</span><strong>{{ $tiempoTotal }} min</strong></div>
        </div>
    </div>

    @foreach ($platos as $p)
        @php
            $completo = $p['faltantes']->isEmpty();
            $n = $p['faltantes']->count();
            $foto = $p['dish']->foto_url;
        @endphp
        <article class="plato {{ $completo ? '' : 'plato-falta' }}">
            @if ($foto)
                <div class="plato-foto">
                    <img src="{{ $foto }}" alt="{{ $p['dish']->nombre }}" width="168" height="126" loading="lazy">
                </div>
            @else
                <div class="plato-foto plato-sin-foto" aria-hidden="true">{{ $iconosRol[$p['dish']->pivot->rol] ?? '🍽️' }}</div>
            @endif
            <div class="plato-cuerpo">
            <div class="plato-cab">
                <span class="rol">{{ $iconosRol[$p['dish']->pivot->rol] ?? '🍽️' }} {{ $p['etiqueta'] }}</span>
                <span class="estado">
                    {{ $completo ? '✓ Lo tienes todo' : ($n === 1 ? 'Te falta 1 ingrediente' : 'Te faltan ' . $n . ' ingredientes') }}
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
            </div>
        </article>
    @endforeach

    <section class="lista">
        <h2>🛒 Lista de compras</h2>
        @if ($listaCompras->isEmpty())
            <p>No te falta nada: puedes preparar todo el combo con lo que ya tienes.</p>
        @else
            <ul class="compras">
                @foreach ($listaCompras as $item)
                    <li>
                        <label>
                            <input type="checkbox">
                            <span>
                                <strong>{{ $item['ingrediente']->nombre }}</strong>
                                <small>para {{ $item['platos']->implode(', ') }}</small>
                            </span>
                        </label>
                    </li>
                @endforeach
            </ul>
            <button type="button" class="btn btn-out" onclick="window.print()">🖨️ Imprimir lista</button>
        @endif
    </section>
@endsection

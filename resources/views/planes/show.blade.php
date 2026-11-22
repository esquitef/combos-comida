@extends('layouts.app')

@section('titulo', 'Plan semanal')

@section('contenido')
    @php
        $iconosRol = config('comibox.emoji_roles');
    @endphp

    <a class="volver" href="{{ route('combos.index') }}">← Generar otro</a>
    <h1>Tu plan de {{ $dias->count() }} {{ $dias->count() === 1 ? 'día' : 'días' }}</h1>
    <p class="sub">
        Supone que los ingredientes que marcaste los tienes disponibles toda la semana:
        el sistema no lleva inventario de cantidades.
    </p>

    @if (session('aviso'))
        <div class="alerta aviso">{{ session('aviso') }}</div>
    @endif

    <div class="resumen">
        <div class="dato">
            <div class="dato-ico">📅</div>
            <div><span>Días planeados</span><strong>{{ $dias->count() }}</strong></div>
        </div>
        <div class="dato">
            <div class="dato-ico">💰</div>
            <div><span>Costo aproximado</span><strong>${{ number_format($costoTotal, 0) }}</strong></div>
        </div>
        <div class="dato">
            <div class="dato-ico">🛒</div>
            <div><span>Por comprar</span><strong>{{ $listaCompras->count() }}</strong></div>
        </div>
    </div>

    @foreach ($dias as $d)
        <section class="card dia">
            <div class="dia-cab">
                <span class="dia-num">{{ $d['dia'] }}</span>
                <h2>Día {{ $d['dia'] }}</h2>
                <a href="{{ route('combos.show', $d['combo']) }}">Ver detalle →</a>
            </div>

            <ul class="dia-platos">
                @foreach ($d['platos'] as $p)
                    <li>
                        @php $foto = $p['dish']->foto_url; @endphp
                        @if ($foto)
                            <img class="mini-foto" src="{{ $foto }}" alt="" width="52" height="52" loading="lazy">
                        @else
                            <span class="mini-ico">{{ $iconosRol[$p['dish']->pivot->rol] ?? '🍽️' }}</span>
                        @endif
                        <span class="txt">
                            <span class="rol-txt">{{ $p['etiqueta'] }}</span>
                            <strong>{{ $p['dish']->nombre }}</strong>
                            <small>{{ $p['dish']->style->nombre }} · {{ $p['dish']->tiempo_preparacion_min }} min</small>
                        </span>
                        @if ($p['faltan'] > 0)
                            <span class="falta-tag">{{ $p['faltan'] === 1 ? 'te falta' : 'te faltan' }} {{ $p['faltan'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <p class="meta">${{ number_format($d['costo'], 0) }} aprox. · {{ $d['tiempo'] }} min en total</p>
        </section>
    @endforeach

    <section class="lista">
        <h2>🛒 Lista de compras de la semana</h2>

        @if ($listaCompras->isEmpty())
            <p>No te falta nada: puedes preparar toda la semana con lo que ya tienes.</p>
        @else
            <ul class="compras">
                @foreach ($listaCompras as $item)
                    <li>
                        <label>
                            <input type="checkbox">
                            <span>
                                <strong>{{ $item['ingrediente']->nombre }}</strong>
                                <small>{{ collect($item['usos'])->map(fn ($u) => 'Día ' . $u['dia'] . ': ' . $u['plato'])->implode(' · ') }}</small>
                            </span>
                        </label>
                    </li>
                @endforeach
            </ul>
            <button type="button" class="btn btn-out" onclick="window.print()">🖨️ Imprimir lista</button>
        @endif
    </section>
@endsection

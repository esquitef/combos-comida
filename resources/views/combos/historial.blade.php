@extends('layouts.app')

@section('titulo', 'Historial')

@section('contenido')
    <h1 style="margin-top:28px">Historial</h1>
    <p class="sub">Lo más reciente que has generado en esta sesión.</p>

    @if ($combos->isEmpty() && $planes->isEmpty())
        <div class="card vacio">
            <img src="{{ asset('img/logo.png') }}" alt="">
            <p>Todavía no has generado ningún combo.</p>
            <a class="btn btn-prim" href="{{ route('combos.index') }}">Genera el primero</a>
        </div>
    @endif

    @if ($combos->isNotEmpty())
        <div class="seccion-tit"><h2>🍽️ Combos</h2></div>
        @foreach ($combos as $item)
            <a class="item" href="{{ route('combos.show', $item['combo']) }}">
                <div class="item-ico">🍽️</div>
                <div class="item-txt">
                    <strong>{{ $item['platos']->pluck('nombre')->implode(' + ') }}</strong>
                    <small>{{ $item['combo']->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="anillo" style="--p: {{ min(100, max(0, (int) round($item['combo']->score))) }}"><b>{{ number_format($item['combo']->score, 0) }}</b></div>
            </a>
        @endforeach
    @elseif ($planes->isNotEmpty())
        <p class="ayuda">Todavía no has generado combos individuales.</p>
    @endif

    @if ($planes->isNotEmpty())
        <div class="seccion-tit"><h2>📅 Planes semanales</h2></div>
        @foreach ($planes as $plan)
            <a class="item" href="{{ route('planes.show', $plan) }}">
                <div class="item-ico">📅</div>
                <div class="item-txt">
                    <strong>Plan de {{ $plan->combos_count }} {{ $plan->combos_count === 1 ? 'día' : 'días' }}</strong>
                    <small>{{ $plan->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <span class="item-flecha">›</span>
            </a>
        @endforeach
    @endif
@endsection
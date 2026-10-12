@extends('layouts.app')

@section('titulo', 'Historial')

@section('contenido')
    <h1>Historial</h1>
    <p class="sub">Lo más reciente de esta sesión.</p>

    <h2>Combos</h2>
    @forelse ($combos as $item)
        <a class="card combo-item" href="{{ route('combos.show', $item['combo']) }}">
            <div>
                <strong>{{ $item['platos']->pluck('nombre')->implode(' + ') }}</strong>
                <small>{{ $item['combo']->created_at->format('d/m/Y H:i') }}</small>
            </div>
            <span class="puntaje">{{ number_format($item['combo']->score, 0) }}</span>
        </a>
    @empty
        @if ($planes->isEmpty())
            <p class="card">
                Todavía no has generado ningún combo. <a href="{{ route('combos.index') }}">Genera el primero</a>.
            </p>
        @else
            <p class="ayuda">Todavía no has generado combos individuales.</p>
        @endif
    @endforelse

    @if ($planes->isNotEmpty())
        <h2 style="margin-top:28px">Planes semanales</h2>
        @foreach ($planes as $plan)
            <a class="card combo-item" href="{{ route('planes.show', $plan) }}">
                <div>
                    <strong>Plan de {{ $plan->combos_count }} {{ $plan->combos_count === 1 ? 'día' : 'días' }}</strong>
                    <small>{{ $plan->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <span class="puntaje">📅</span>
            </a>
        @endforeach
    @endif
@endsection
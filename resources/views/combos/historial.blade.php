@extends('layouts.app')

@section('titulo', 'Historial')

@section('contenido')
    <h1>Últimos combos generados</h1>
    <p class="sub">Los 5 más recientes de esta sesión.</p>

    @forelse ($combos as $item)
        <a class="card combo-item" href="{{ route('combos.show', $item['combo']) }}">
            <div>
                <strong>{{ $item['platos']->pluck('nombre')->implode(' + ') }}</strong>
                <small>{{ $item['combo']->created_at->format('d/m/Y H:i') }}</small>
            </div>
            <span class="puntaje">{{ number_format($item['combo']->score, 0) }}</span>
        </a>
    @empty
        <p class="card">
            Todavía no has generado ningún combo. <a href="{{ route('combos.index') }}">Genera el primero</a>.
        </p>
    @endforelse
@endsection
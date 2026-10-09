@extends('layouts.app')

@section('titulo', 'Generar combo')

@section('contenido')
    <h1>¿Qué cocinamos hoy?</h1>
    <p class="sub">Marca los ingredientes que tienes, ajusta tus preferencias y arma un combo completo.</p>

    @if ($errors->any())
        <div class="alerta">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('combos.generar') }}">
        @csrf

        <section class="card">
            <h2>1. Tus ingredientes</h2>
            <p class="ayuda">
                Toca un ingrediente para seleccionarlo. Al marcarlo aparece una ⭐: actívala en los que quieras
                gastar primero (modo aprovechamiento).
            </p>

            @foreach ($categorias as $categoria)
                <fieldset>
                    <legend>{{ $categoria->nombre }}</legend>
                    <div class="chips">
                        @foreach ($categoria->ingredients as $ing)
                            <div class="chip">
                                <label class="chip-main">
                                    <input type="checkbox" class="sel" name="ingredientes[]" value="{{ $ing->id }}"
                                           @checked(in_array($ing->id, old('ingredientes', [])))>
                                    <span>{{ $ing->nombre }}</span>
                                </label>
                                <label class="prio" title="Usar primero (modo aprovechamiento)">
                                    <input type="checkbox" name="prioritarios[]" value="{{ $ing->id }}"
                                           @checked(in_array($ing->id, old('prioritarios', [])))>
                                    <span>⭐</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
        </section>

        <section class="card">
            <h2>2. Tus preferencias <small>(opcionales)</small></h2>
            <div class="grid-prefs">
                <label>Tiempo máximo por plato
                    <select name="tiempo_max">
                        <option value="">Sin límite</option>
                        @foreach ([15, 30, 45, 60, 90] as $min)
                            <option value="{{ $min }}" @selected(old('tiempo_max') == $min)>{{ $min }} min</option>
                        @endforeach
                    </select>
                </label>

                <label>Presupuesto máximo del combo (MXN)
                    <input type="number" name="presupuesto" min="1" step="1" placeholder="Ej. 150" value="{{ old('presupuesto') }}">
                </label>

                <label>Estilo gastronómico
                    <select name="style_id">
                        <option value="">Cualquiera</option>
                        @foreach ($estilos as $estilo)
                            <option value="{{ $estilo->id }}" @selected(old('style_id') == $estilo->id)>{{ $estilo->nombre }}</option>
                        @endforeach
                    </select>
                </label>

                <label>Objetivo de la comida
                    <select name="objetivo">
                        <option value="">Cualquiera</option>
                        @foreach ($objetivos as $valor => $texto)
                            <option value="{{ $valor }}" @selected(old('objetivo') == $valor)>{{ $texto }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <div class="acciones">
            <button type="submit" name="sorprende" value="0" class="btn">Generar combo</button>
            <button type="submit" name="sorprende" value="1" class="btn btn-alt">🎲 Sorpréndeme</button>
        </div>
    </form>
@endsection
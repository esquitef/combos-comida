@extends('layouts.app')

@section('titulo', 'Generar combo')

@section('contenido')
    @php
        // Los símbolos están en config/comibox.php
        $iconos = config('comibox.emoji_categorias');
        $emojis = config('comibox.emoji_ingredientes');
    @endphp

    <section class="hero">
        <div>
            <h1>¿Qué cocinamos hoy?</h1>
            <p>Marca lo que ya tienes en casa, cuéntanos cuánto tiempo y presupuesto tienes, y armamos un combo completo con la lista de lo que te falta.</p>
        </div>
        <img src="{{ asset('img/logo.png') }}" alt="" width="132" height="132">
    </section>

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
            <div class="paso-cab">
                <span class="num">1</span>
                <h2>Tus ingredientes</h2>
            </div>
            <p class="ayuda">
                Toca los ingredientes que tienes. Al marcar uno aparece una ⭐: actívala en los que quieras
                gastar primero y los tomaremos en cuenta antes que los demás.
            </p>

            @foreach ($categorias as $categoria)
                <fieldset class="cat">
                    <legend class="cat-tit">
                        <span class="cat-in">
                            <span class="cat-ico">{{ $iconos[$categoria->nombre] ?? '🍽️' }}</span>
                            {{ $categoria->nombre }}
                            <small>{{ $categoria->ingredients->count() }}</small>
                        </span>
                    </legend>
                    <div class="chips">
                        @foreach ($categoria->ingredients as $ing)
                            <div class="chip">
                                <label class="chip-main">
                                    <input type="checkbox" class="sel" name="ingredientes[]" value="{{ $ing->id }}"
                                           @checked(in_array($ing->id, old('ingredientes', [])))>
                                    @if (isset($emojis[$ing->nombre]))
                                        <span class="chip-emo" aria-hidden="true">{{ $emojis[$ing->nombre] }}</span>
                                    @endif
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
            <div class="paso-cab">
                <span class="num">2</span>
                <h2>Tus preferencias <span class="opcional">(opcionales)</span></h2>
            </div>
            <p class="ayuda">Si no eliges nada, buscamos la mejor opción con lo que marcaste.</p>

            <div class="grid-prefs">
                <label class="campo">⏱️ Tiempo máximo por plato
                    <select name="tiempo_max">
                        <option value="">Sin límite</option>
                        @foreach ([15, 30, 45, 60, 90] as $min)
                            <option value="{{ $min }}" @selected(old('tiempo_max') == $min)>{{ $min }} min</option>
                        @endforeach
                    </select>
                </label>

                <label class="campo">💰 Presupuesto del combo (MXN)
                    <input type="number" name="presupuesto" min="1" step="1" placeholder="Ej. 150" value="{{ old('presupuesto') }}">
                </label>

                <label class="campo">🌎 Estilo gastronómico
                    <select name="style_id">
                        <option value="">Cualquiera</option>
                        @foreach ($estilos as $estilo)
                            <option value="{{ $estilo->id }}" @selected(old('style_id') == $estilo->id)>{{ $estilo->nombre }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="campo">🎯 Objetivo de la comida
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
            <button type="submit" name="sorprende" value="0" class="btn btn-prim">Generar combo</button>
            <button type="submit" name="sorprende" value="1" class="btn btn-sec">🎲 Sorpréndeme</button>
            <button type="submit" formaction="{{ route('planes.generar') }}" class="btn btn-out">📅 Plan de 5 días</button>
        </div>
    </form>
@endsection

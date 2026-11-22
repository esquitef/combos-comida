@extends('layouts.app')

@section('titulo', 'Acerca del proyecto')

@section('contenido')
    <section class="hero">
        <div>
            <h1>Acerca del proyecto</h1>
            <p>ComiBox te ayuda a decidir qué comer con lo que ya tienes en casa: menos desperdicio, menos compras de más y comidas mejor planeadas.</p>
        </div>
        <img src="{{ asset('img/logo.png') }}" alt="Logotipo de ComiBox" width="132" height="132">
    </section>

    <section class="card">
        <h2>¿Qué es ComiBox?</h2>
        <p>
            Es un sistema web que arma <strong>combos de comida completos</strong> (plato principal, acompañamiento, bebida y postre)
            a partir de los ingredientes que marcas, tu tiempo, tu presupuesto, el estilo de cocina que se te antoja y el objetivo
            de la comida. Además te dice qué ingredientes te faltan y te prepara la lista de compras.
        </p>
        <div class="cifras">
            <div class="cifra"><strong>{{ $totalIngredientes }}</strong><span>Ingredientes</span></div>
            <div class="cifra"><strong>{{ $totalCategorias }}</strong><span>Categorías</span></div>
            <div class="cifra"><strong>{{ $totalPlatos }}</strong><span>Platos</span></div>
            <div class="cifra"><strong>{{ $totalEstilos }}</strong><span>Estilos de cocina</span></div>
        </div>
    </section>

    <section class="card">
        <h2>¿Cómo funciona?</h2>
        <div class="pasos">
            <div class="paso-card">
                <span class="num">1</span>
                <div>
                    <h3>Marca tus ingredientes</h3>
                    <p>Elige lo que tienes en casa. Con la ⭐ indicas lo que quieres gastar primero.</p>
                </div>
            </div>
            <div class="paso-card">
                <span class="num">2</span>
                <div>
                    <h3>Cuéntanos tus preferencias</h3>
                    <p>Tiempo, presupuesto, estilo y objetivo. Todo es opcional.</p>
                </div>
            </div>
            <div class="paso-card">
                <span class="num">3</span>
                <div>
                    <h3>Armamos tu combo</h3>
                    <p>Elegimos el mejor plato principal y lo completamos con acompañamiento, bebida y postre.</p>
                </div>
            </div>
            <div class="paso-card">
                <span class="num">4</span>
                <div>
                    <h3>Compra solo lo que falta</h3>
                    <p>Te mostramos qué ingredientes no tienes y los juntamos en una lista de compras.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <h2>¿Cómo se calcula el puntaje?</h2>
        <p>
            Primero descartamos los platos que pasan del tiempo o del presupuesto que indicaste y aquellos de los que tienes
            menos de la mitad de los ingredientes clave. A cada plato que queda le damos un puntaje sobre 100:
        </p>
        <ul class="puntos">
            <li><span>Ingredientes clave que ya tienes</span><b>hasta 70</b></li>
            <li><span>Ingredientes opcionales que ya tienes</span><b>hasta 10</b></li>
            <li><span>Usa ingredientes marcados con ⭐ (10 por cada uno)</span><b>hasta +25</b></li>
            <li><span>Coincide con el objetivo que elegiste</span><b>+5</b></li>
            <li><span>Coincide con el estilo que elegiste</span><b>+5</b></li>
            <li><span>Acompañamiento, bebida o postre del mismo estilo que el principal</span><b>+10</b></li>
        </ul>
        <p class="ayuda">
            El puntaje del combo mezcla el del plato principal (55 %) con el promedio de los demás platos (45 %).
            La función Sorpréndeme elige al azar entre las tres mejores opciones de cada tipo de plato.
        </p>
    </section>

    <section class="card">
        <h2>📅 Plan semanal</h2>
        <p>
            Arma cinco días seguidos con un plato principal distinto cada día. Los demás platos solo se repiten si no hay otra
            opción, los ingredientes con ⭐ se reparten entre los días y al final tienes una sola lista de compras para toda
            la semana. Si no alcanzan los platos distintos, el plan sale más corto y te avisamos.
        </p>
    </section>

    <section class="card">
        <h2>Lo que ComiBox no hace (todavía)</h2>
        <ul class="limites">
            <li>No incluye instrucciones de preparación paso a paso: te dice qué cocinar y qué comprar, no cómo cocinarlo.</li>
            <li>No lleva cantidades: sabe si tienes un ingrediente, pero no cuánto. El plan semanal supone que te alcanza para toda la semana.</li>
            <li>Los costos son aproximados, en pesos mexicanos, y los tiempos de preparación también.</li>
            <li>Se asume que ya tienes sal, aceite, agua y pimienta, por eso no aparecen entre los ingredientes.</li>
            <li>El estilo «Vegetariano» suma puntos a sus platos, pero no es un filtro estricto: un combo podría incluir carne.</li>
            <li>No hay cuentas de usuario: tu historial se guarda en la sesión del navegador y se pierde si borras las cookies.</li>
        </ul>
    </section>

    <section class="card">
        <h2>Quiénes lo hicimos</h2>
        <p>
            ComiBox es un proyecto académico sin fines comerciales, desarrollado para la materia
            <em>Métodos y Modelos de Desarrollo de Software</em> en el Centro Universitario UAEM Tianguistenco.
        </p>
        <div class="equipo">
            <span class="persona">Gael Pérez Saldaña</span>
            <span class="persona">José Julián Vásquez Aguirre</span>
        </div>
    </section>

    <section class="card">
        <h2>Créditos y fuentes</h2>
        <p>
            Los platos se adaptaron de recetarios de cocina mexicana (Diana Baker), italiana, japonesa, china (cocina en wok),
            española y argentina, simplificando cada receta a sus ingredientes clave. Las aguas frescas, los atoles y varios
            platos más son de elaboración propia. Los costos y tiempos son estimaciones del equipo.
        </p>
    </section>
@endsection

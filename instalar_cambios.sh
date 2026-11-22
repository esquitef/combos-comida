#!/usr/bin/env bash
# ComiBox · instalador del rediseño (pie de página, "Acerca del proyecto", emojis de ingredientes y fotos de platos).
# Uso: desde la raíz del proyecto (donde está el archivo "artisan"):   bash instalar_cambios.sh
set -euo pipefail

if [ ! -f artisan ]; then
    echo "❌ No encuentro el archivo 'artisan'. Entra a la carpeta del proyecto y vuelve a ejecutar:  bash instalar_cambios.sh"
    exit 1
fi

# Los archivos que ya existan se copian aquí antes de sobrescribirlos (carpeta ignorada por git).
RESPALDO="storage/app/respaldo_$(date +%Y%m%d_%H%M%S)"

escribir() {
    local ruta="$1"
    mkdir -p "$(dirname "$ruta")"
    if [ -f "$ruta" ]; then
        mkdir -p "$RESPALDO/$(dirname "$ruta")"
        cp "$ruta" "$RESPALDO/$ruta"
    fi
    cat > "$ruta"
    echo "  ✔ $ruta"
}

echo "Instalando archivos..."
escribir resources/views/layouts/app.blade.php <<'__COMIBOX_FIN__'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'ComiBox') · ComiBox</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    {{-- Tipografías de Google Fonts (necesitan internet). Si no cargan, se usan las del sistema. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root{
            /* Paleta tomada del logo */
            --v900:#1D3E1C; --v700:#2F6B24; --v500:#6B9518; --v100:#E8F0D2; --v50:#F4F8E9;
            --crema:#FBF4E4; --papel:#FFFDF8;
            --kraft:#E4A763; --kraft-cl:#F8E7CB; --kraft-txt:#6B3F12;
            --tomate-cl:#FCEAE4; --tomate-txt:#8A2A14;
            --ambar-cl:#FFF2CF; --ambar-txt:#7A5200;
            --texto:#2B3A2A; --suave:#657161; --borde:#E9E1CA;
            --radio:18px; --sombra:0 8px 24px rgba(29,62,28,.08);
            --f-titulo:'Fredoka',ui-rounded,'Nunito','Segoe UI',system-ui,sans-serif;
            --f-texto:'Nunito',ui-rounded,'Segoe UI',system-ui,-apple-system,sans-serif;
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:var(--f-texto);font-size:16px;line-height:1.55;color:var(--texto);
             background:var(--crema) radial-gradient(1100px 380px at 50% -120px,#EEF4D9 0%,rgba(238,244,217,0) 70%) no-repeat}
        .wrap{max-width:980px;margin:0 auto;padding:0 20px}
        a{color:var(--v700)}

        /* ---------- Encabezado ---------- */
        .top{position:sticky;top:0;z-index:20;background:rgba(255,253,248,.93);backdrop-filter:blur(8px);border-bottom:1px solid var(--borde)}
        .bar{display:flex;justify-content:space-between;align-items:center;gap:16px;min-height:70px}
        .marca{display:flex;align-items:center;gap:12px;text-decoration:none}
        .marca img{width:52px;height:52px;border-radius:50%;display:block;box-shadow:0 2px 8px rgba(29,62,28,.2)}
        .nombre{font-family:var(--f-titulo);font-weight:600;font-size:1.45rem;line-height:1;color:var(--v900)}
        .nombre b{font-weight:600;color:var(--v500)}
        .marca small{display:block;margin-top:3px;font-size:.78rem;color:var(--suave)}
        .top nav{display:flex;gap:6px}
        .top nav a{padding:8px 18px;border-radius:999px;text-decoration:none;font-weight:800;font-size:.95rem;color:var(--v900)}
        .top nav a:hover{background:var(--v100)}
        .top nav a.activo{background:var(--v700);color:#fff}

        /* ---------- Tipografía y tarjetas ---------- */
        h1,h2,h3{font-family:var(--f-titulo);font-weight:600;color:var(--v900)}
        h1{font-size:clamp(1.85rem,4.2vw,2.5rem);line-height:1.12;margin:0 0 8px}
        h2{font-size:1.25rem;margin:0 0 6px}
        .sub{color:var(--suave);margin:0 0 18px}
        .ayuda{color:var(--suave);margin:0 0 16px;font-size:.95rem}
        .card{background:var(--papel);border:1px solid var(--borde);border-radius:var(--radio);padding:24px;margin:18px 0;box-shadow:var(--sombra)}
        .hero{display:flex;align-items:center;justify-content:space-between;gap:26px;margin:26px 0 8px;padding:28px 34px;
              border:1px solid var(--borde);border-radius:26px;background:linear-gradient(135deg,var(--v50),var(--kraft-cl) 160%)}
        .hero p{margin:0;max-width:46ch;font-size:1.05rem;color:var(--suave)}
        .hero img{flex:none;width:132px;height:132px;border-radius:50%;box-shadow:0 12px 28px rgba(29,62,28,.2)}

        .alerta{margin:16px 0;padding:14px 20px;border:1px solid #F2B9A9;border-radius:14px;background:var(--tomate-cl);color:var(--tomate-txt)}
        .alerta ul{margin:0;padding-left:18px}
        .aviso{border-color:#EFD58A;background:var(--ambar-cl);color:var(--ambar-txt)}

        /* ---------- Formulario ---------- */
        .paso-cab{display:flex;align-items:center;gap:12px;margin-bottom:6px}
        .paso-cab h2{margin:0}
        .num{flex:none;display:grid;place-items:center;width:34px;height:34px;border-radius:50%;background:var(--v700);color:#fff;font-family:var(--f-titulo);font-weight:600}
        .opcional{font-family:var(--f-texto);font-size:.85rem;font-weight:600;color:var(--suave)}

        .cat{border:0;padding:0;margin:0 0 20px;min-width:0}
        .cat-tit{padding:0;margin-bottom:10px}
        .cat-in{display:inline-flex;align-items:center;gap:9px;font-family:var(--f-titulo);font-weight:600;font-size:1.05rem;color:var(--v900)}
        .cat-ico{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:var(--v100);font-size:1.1rem}
        .cat-in small{font-family:var(--f-texto);font-weight:700;font-size:.78rem;color:var(--suave);background:var(--crema);border:1px solid var(--borde);border-radius:999px;padding:0 8px}

        .chips{display:flex;flex-wrap:wrap;gap:8px}
        .chip{display:inline-flex;align-items:center;border:1.5px solid var(--borde);border-radius:999px;background:#fff}
        .chip:hover{border-color:#CFD9B0}
        .chip-main,.prio{position:relative;cursor:pointer;user-select:none}
        .chip-main{display:flex;align-items:center;gap:8px;padding:7px 14px 7px 9px;font-weight:700;font-size:.95rem}
        .chip-main::before{content:'';flex:none;display:grid;place-items:center;width:18px;height:18px;border-radius:50%;border:1.5px solid #D2CAB0;background:#fff;color:#fff;font-size:11px;line-height:1}
        .chip-main input,.prio input{position:absolute;opacity:0;pointer-events:none}
        .chip:has(.sel:checked){background:var(--v100);border-color:var(--v500)}
        .chip:has(.sel:checked) .chip-main{color:var(--v900)}
        .chip:has(.sel:checked) .chip-main::before{content:'✓';background:var(--v700);border-color:var(--v700)}
        .chip:has(.sel:focus-visible){outline:3px solid rgba(107,149,24,.45);outline-offset:2px}
        .prio{display:none;padding:5px 11px 5px 2px;font-size:.95rem;opacity:.45;filter:grayscale(1)}
        .chip:has(.sel:checked) .prio{display:inline-flex}
        .prio:has(input:checked){opacity:1;filter:none}

        .grid-prefs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:end;gap:18px 22px}
        .campo{display:flex;flex-direction:column;gap:6px;font-weight:800;font-size:.9rem;color:var(--v900)}
        .campo select,.campo input{height:46px;font:inherit;font-weight:600;color:var(--texto);padding:11px 13px;border:1.5px solid var(--borde);border-radius:12px;background:#fff}
        .campo select:focus,.campo input:focus{outline:3px solid rgba(107,149,24,.35);border-color:var(--v500)}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 22px;border:2px solid transparent;border-radius:14px;
             font-family:var(--f-titulo);font-weight:600;font-size:1.02rem;line-height:1.2;text-decoration:none;cursor:pointer}
        .btn-prim{background:var(--v700);color:#fff}
        .btn-prim:hover{background:var(--v900)}
        .btn-sec{background:var(--kraft-cl);border-color:var(--kraft);color:var(--kraft-txt)}
        .btn-sec:hover{background:var(--kraft)}
        .btn-out{background:#fff;border-color:var(--v700);color:var(--v700)}
        .btn-out:hover{background:var(--v50)}
        .btn:focus-visible{outline:3px solid rgba(107,149,24,.5);outline-offset:2px}

        .acciones{position:sticky;bottom:14px;z-index:15;display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin:22px 0 40px;padding:12px;
                  border:1px solid var(--borde);border-radius:22px;background:rgba(255,253,248,.95);backdrop-filter:blur(8px);box-shadow:0 10px 30px rgba(29,62,28,.16)}

        /* ---------- Resultado ---------- */
        .volver{display:inline-flex;align-items:center;gap:6px;margin-top:22px;font-weight:800;text-decoration:none}
        .volver:hover{text-decoration:underline}
        .resumen{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:18px 0}
        .dato{display:flex;align-items:center;gap:14px;padding:16px 18px;border:1px solid var(--borde);border-radius:var(--radio);background:var(--papel);box-shadow:var(--sombra)}
        .dato-ico{flex:none;display:grid;place-items:center;width:48px;height:48px;border-radius:15px;background:var(--v100);font-size:1.45rem}
        .dato span{display:block;font-size:.76rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--suave)}
        .dato strong{font-family:var(--f-titulo);font-weight:600;font-size:1.65rem;line-height:1.1;color:var(--v900)}
        .anillo{--p:0;position:relative;flex:none;display:grid;place-items:center;width:58px;height:58px;border-radius:50%;
                background:conic-gradient(var(--v500) calc(var(--p)*1%),#E4E7CF 0)}
        .anillo::before{content:'';position:absolute;inset:7px;border-radius:50%;background:var(--papel)}
        .anillo b{position:relative;font-family:var(--f-titulo);font-weight:600;font-size:1.05rem;color:var(--v900)}

        .plato{position:relative;overflow:hidden;margin:14px 0;padding:18px 22px 18px 26px;border:1px solid var(--borde);border-radius:var(--radio);background:var(--papel);box-shadow:var(--sombra)}
        .plato::before{content:'';position:absolute;left:0;top:0;bottom:0;width:7px;background:var(--v500)}
        .plato-falta::before{background:var(--kraft)}
        .plato-cab{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:8px;margin-bottom:4px}
        .rol{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;background:var(--v100);color:var(--v900);font-size:.76rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
        .estado{font-size:.88rem;font-weight:800;color:#2B6422}
        .plato-falta .estado{color:var(--ambar-txt)}
        .plato h2{margin:2px 0 2px;font-size:1.45rem}
        .meta{margin:0 0 12px;font-size:.95rem;color:var(--suave)}
        .ings{display:flex;flex-wrap:wrap;gap:7px;margin:0;padding:0;list-style:none}
        .ings li{padding:4px 12px;border:1px solid var(--borde);border-radius:999px;background:var(--crema);font-size:.88rem;font-weight:700}
        .ings .ok{border-color:#CDE0A3;background:var(--v100);color:#264F1E}
        .ings .falta{border-color:#EFD58A;background:var(--ambar-cl);color:var(--ambar-txt)}
        .ings .opc{border:1.5px dashed #D3CAAE;background:transparent;color:var(--suave);font-weight:600}

        .lista{margin:22px 0;padding:24px;border:2px dashed var(--kraft);border-radius:var(--radio);background:var(--kraft-cl)}
        .compras{margin:8px 0 18px;padding:0;list-style:none}
        .compras li{padding:10px 0;border-bottom:1px dashed #E2C697}
        .compras li:last-child{border-bottom:0}
        .compras label{display:flex;align-items:flex-start;gap:12px;cursor:pointer}
        .compras input{flex:none;width:20px;height:20px;margin-top:3px;accent-color:var(--v700)}
        .compras strong{display:block;font-weight:800}
        .compras small{display:block;font-size:.88rem;font-weight:600;color:var(--kraft-txt)}

        /* ---------- Historial ---------- */
        .seccion-tit{display:flex;align-items:center;gap:10px;margin:30px 0 4px}
        .item{display:flex;align-items:center;gap:16px;margin:12px 0;padding:16px 18px;border:1px solid var(--borde);border-radius:var(--radio);
              background:var(--papel);box-shadow:var(--sombra);color:inherit;text-decoration:none}
        .item:hover{border-color:var(--v500)}
        .item-ico{flex:none;display:grid;place-items:center;width:48px;height:48px;border-radius:15px;background:var(--v100);font-size:1.5rem}
        .item-txt{flex:1;min-width:0}
        .item-txt strong{display:block;font-family:var(--f-titulo);font-weight:600;font-size:1.08rem;line-height:1.3;color:var(--v900)}
        .item-txt small{color:var(--suave)}
        .item .anillo{width:50px;height:50px}
        .item-flecha{flex:none;font-size:1.4rem;color:var(--v500)}
        .vacio{padding:40px 22px;text-align:center}
        .vacio img{width:96px;height:96px;margin-bottom:10px;border-radius:50%}
        .vacio p{margin:0 0 16px;color:var(--suave)}

        /* ---------- Plan semanal ---------- */
        .dia{padding:20px 24px}
        .dia-cab{display:flex;align-items:center;gap:12px;margin-bottom:8px}
        .dia-cab h2{flex:1;margin:0}
        .dia-cab a{font-size:.9rem;font-weight:800;text-decoration:none}
        .dia-num{flex:none;display:grid;place-items:center;width:40px;height:40px;border-radius:50%;background:var(--v700);color:#fff;font-family:var(--f-titulo);font-weight:600}
        .dia-platos{margin:0 0 8px;padding:0;list-style:none}
        .dia-platos li{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--borde)}
        .dia-platos li:last-child{border-bottom:0}
        .mini-ico{flex:none;display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:var(--v100);font-size:1.15rem}
        .dia-platos .txt{flex:1;min-width:0}
        .dia-platos .rol-txt{display:block;font-size:.72rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--suave)}
        .dia-platos strong{font-weight:800}
        .dia-platos small{margin-left:6px;color:var(--suave)}
        .falta-tag{flex:none;padding:2px 11px;border-radius:999px;background:var(--ambar-cl);color:var(--ambar-txt);font-size:.8rem;font-weight:800}
        .dia .meta{margin:8px 0 0}

        /* ---------- Fotos de platos ---------- */
        .plato{display:flex;align-items:flex-start;gap:20px}
        .plato-cuerpo{flex:1;min-width:0}
        .plato-foto{flex:none;width:168px;overflow:hidden;border-radius:14px;background:var(--v100)}
        .plato-foto img{display:block;width:100%;height:auto;aspect-ratio:4/3;object-fit:cover}
        .plato-sin-foto{display:grid;place-items:center;height:126px;font-size:2.7rem;background:linear-gradient(135deg,var(--v100),var(--kraft-cl))}
        .mini-foto{flex:none;width:52px;height:52px;border-radius:14px;object-fit:cover;background:var(--v100)}
        .item-foto{flex:none;width:56px;height:56px;border-radius:15px;object-fit:cover;background:var(--v100)}
        .chip-emo{font-size:1rem;line-height:1}

        /* ---------- Acerca del proyecto ---------- */
        .cifras{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:18px 0 4px}
        .cifra{padding:14px 10px;border:1px solid var(--borde);border-radius:16px;background:var(--v50);text-align:center}
        .cifra strong{display:block;font-family:var(--f-titulo);font-weight:600;font-size:1.8rem;line-height:1.1;color:var(--v700)}
        .cifra span{font-size:.82rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:var(--suave)}
        .pasos{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-top:14px}
        .paso-card{display:flex;gap:14px;padding:16px 18px;border:1px solid var(--borde);border-radius:16px;background:var(--crema)}
        .paso-card h3{margin:0 0 4px;font-size:1.05rem}
        .paso-card p{margin:0;font-size:.95rem;color:var(--suave)}
        .puntos{margin:14px 0 6px;padding:0;list-style:none}
        .puntos li{display:flex;justify-content:space-between;align-items:baseline;gap:16px;padding:10px 0;border-bottom:1px dashed var(--borde)}
        .puntos li:last-child{border-bottom:0}
        .puntos b{flex:none;font-family:var(--f-titulo);font-weight:600;color:var(--v700);white-space:nowrap}
        .limites{margin:10px 0 0;padding:0;list-style:none}
        .limites li{position:relative;padding:8px 0 8px 28px}
        .limites li::before{content:'•';position:absolute;left:8px;top:8px;color:var(--kraft);font-weight:800}
        .equipo{display:flex;flex-wrap:wrap;gap:10px;margin:12px 0}
        .persona{padding:8px 16px;border:1px solid var(--borde);border-radius:999px;background:var(--v50);font-weight:800;color:var(--v900)}

        /* ---------- Pie de página ---------- */
        .pie{margin-top:44px;padding:30px 0 26px;border-top:1px solid var(--borde);background:var(--papel);color:var(--suave);font-size:.88rem}
        .pie-in{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:18px}
        .pie-marca{display:flex;align-items:center;gap:12px}
        .pie-marca img{width:44px;height:44px;border-radius:50%;display:block}
        .pie-marca strong{display:block;font-family:var(--f-titulo);font-weight:600;font-size:1.15rem;line-height:1.1;color:var(--v900)}
        .pie-enlaces{display:flex;flex-wrap:wrap;gap:6px 22px}
        .pie-enlaces a{font-weight:800;text-decoration:none;color:var(--v700)}
        .pie-enlaces a:hover{text-decoration:underline}
        .pie-legal{margin-top:18px;padding-top:16px;border-top:1px dashed var(--borde);text-align:center}
        .pie-legal p{margin:2px 0}

        @media (max-width:640px){
            .resumen,.grid-prefs,.pasos{grid-template-columns:1fr}
            .cifras{grid-template-columns:repeat(2,minmax(0,1fr))}
            .plato{flex-direction:column;gap:12px}
            .plato-foto{width:100%}
            .plato-foto img{aspect-ratio:16/9}
            .plato-sin-foto{display:none}
            .pie-in{flex-direction:column;align-items:flex-start}
            .hero{flex-direction:column;align-items:flex-start;padding:22px}
            .hero img{display:none}
            .wrap{padding:0 14px}
            .bar{gap:8px;min-height:62px}
            .marca{gap:9px}
            .marca img{width:42px;height:42px}
            .nombre{font-size:1.2rem}
            .marca small{display:none}
            .top nav{gap:2px}
            .top nav a{padding:7px 11px;font-size:.9rem}
            .acciones{bottom:8px;gap:8px;padding:10px;border-radius:18px}
            .acciones .btn-prim{flex:1 1 100%}
            .acciones .btn-sec,.acciones .btn-out{flex:1 1 calc(50% - 4px);padding:10px 8px;font-size:.92rem}
            .card{padding:18px}
        }
        @media print{
            .top,.pie,.volver,.btn,.acciones{display:none!important}
            body{background:#fff}
            .card,.plato,.dato,.item{box-shadow:none}
        }
    </style>
</head>
<body>
    <header class="top">
        <div class="wrap bar">
            <a class="marca" href="{{ route('combos.index') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logotipo de ComiBox" width="52" height="52">
                <span>
                    <span class="nombre">Comi<b>Box</b></span>
                    <small>Comida real, en casa</small>
                </span>
            </a>
            <nav>
                <a href="{{ route('combos.index') }}" @class(['activo' => request()->routeIs('combos.index')])>Generar</a>
                <a href="{{ route('combos.historial') }}" @class(['activo' => request()->routeIs('combos.historial')])>Historial</a>
            </nav>
        </div>
    </header>

    <main class="wrap">
        @yield('contenido')
    </main>

    <footer class="pie">
        <div class="wrap pie-in">
            <div class="pie-marca">
                <img src="{{ asset('img/logo.png') }}" alt="" width="44" height="44">
                <div>
                    <strong>ComiBox</strong>
                    <span>Comida real, en casa</span>
                </div>
            </div>
            <nav class="pie-enlaces" aria-label="Pie de página">
                <a href="{{ route('combos.index') }}">Generar</a>
                <a href="{{ route('combos.historial') }}">Historial</a>
                <a href="{{ route('acerca') }}">Acerca del proyecto</a>
            </nav>
        </div>
        <div class="wrap pie-legal">
            <p>Proyecto académico · UAEM Tianguistenco · Hecho por Gael Pérez Saldaña y José Julián Vásquez Aguirre</p>
            <p>© {{ date('Y') }} ComiBox. Recetas adaptadas de recetarios de cocina; costos y tiempos aproximados.</p>
        </div>
    </footer>
</body>
</html>
__COMIBOX_FIN__
escribir resources/views/combos/index.blade.php <<'__COMIBOX_FIN__'
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
__COMIBOX_FIN__
escribir resources/views/combos/show.blade.php <<'__COMIBOX_FIN__'
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
__COMIBOX_FIN__
escribir resources/views/combos/historial.blade.php <<'__COMIBOX_FIN__'
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
                @php $foto = $item['platos']->first()?->foto_url; @endphp
                @if ($foto)
                    <img class="item-foto" src="{{ $foto }}" alt="" width="56" height="56" loading="lazy">
                @else
                    <div class="item-ico">🍽️</div>
                @endif
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
__COMIBOX_FIN__
escribir resources/views/planes/show.blade.php <<'__COMIBOX_FIN__'
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
__COMIBOX_FIN__
escribir resources/views/acerca.blade.php <<'__COMIBOX_FIN__'
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
__COMIBOX_FIN__
escribir config/comibox.php <<'__COMIBOX_FIN__'
<?php

/*
 * Ajustes propios de ComiBox.
 *
 * Los símbolos (emojis) se eligen entre los de Unicode 12 o anteriores para que se vean bien en Windows 10, Android y Linux.
 * Un ingrediente que no aparece en 'emoji_ingredientes' simplemente se muestra sin símbolo.
 */

return [

    // Símbolo de cada rol de plato dentro de un combo.
    'emoji_roles' => [
        'principal' => '🍽️',
        'acompanamiento' => '🥗',
        'bebida' => '🥤',
        'postre' => '🍰',
    ],

    // Símbolo de cada categoría de ingredientes (si falta alguna, la vista usa 🍽️).
    'emoji_categorias' => [
        'Verduras' => '🥦',
        'Chiles, hierbas y especias' => '🌶️',
        'Carnes y aves' => '🍗',
        'Pescados y mariscos' => '🐟',
        'Huevos y lácteos' => '🥚',
        'Pastas, cereales y panes' => '🍝',
        'Legumbres y soya' => '🌱',
        'Frutas' => '🍎',
        'Frutos secos, semillas y flores' => '🥜',
        'Condimentos y básicos' => '🧂',
    ],

    // Símbolo de cada ingrediente (clave = nombre exacto del ingrediente).
    'emoji_ingredientes' => [
        'Tomate' => '🍅',
        'Cebolla' => '🧅',
        'Ajo' => '🧄',
        'Papa' => '🥔',
        'Camote' => '🍠',
        'Zanahoria' => '🥕',
        'Lechuga' => '🥬',
        'Aguacate' => '🥑',
        'Champiñones' => '🍄',
        'Pepino' => '🥒',
        'Berenjena' => '🍆',
        'Col' => '🥬',
        'Espinaca' => '🥬',
        'Acelga' => '🥬',
        'Calabaza' => '🎃',
        'Elote' => '🌽',
        'Brócoli' => '🥦',
        'Chile' => '🌶️',
        'Chile poblano' => '🌶️',
        'Chile chipotle' => '🌶️',
        'Chile guajillo' => '🌶️',
        'Chile ancho' => '🌶️',
        'Cilantro' => '🌿',
        'Perejil' => '🌿',
        'Hierbabuena' => '🌿',
        'Albahaca' => '🌿',
        'Orégano' => '🌿',
        'Laurel' => '🍃',
        'Pollo' => '🍗',
        'Carne molida' => '🥩',
        'Res' => '🥩',
        'Cerdo' => '🐖',
        'Jamón' => '🍖',
        'Chorizo' => '🌭',
        'Tocino' => '🥓',
        'Atún' => '🐟',
        'Camarón' => '🦐',
        'Pescado blanco' => '🐟',
        'Bacalao' => '🐟',
        'Salmón' => '🐟',
        'Huevo' => '🥚',
        'Leche' => '🥛',
        'Queso' => '🧀',
        'Queso crema' => '🧀',
        'Pasta' => '🍝',
        'Arroz' => '🍚',
        'Tortilla' => '🌮',
        'Pan' => '🍞',
        'Avena' => '🥣',
        'Harina' => '🌾',
        'Fideos' => '🍜',
        'Masa para empanadas' => '🥟',
        'Plátano' => '🍌',
        'Limón' => '🍋',
        'Manzana' => '🍎',
        'Fresa' => '🍓',
        'Naranja' => '🍊',
        'Piña' => '🍍',
        'Mango' => '🥭',
        'Melón' => '🍈',
        'Sandía' => '🍉',
        'Coco' => '🥥',
        'Uva' => '🍇',
        'Nuez' => '🌰',
        'Jamaica' => '🌺',
        'Lavanda' => '💐',
        'Café' => '☕',
        'Té' => '🍵',
        'Miel' => '🍯',
        'Chocolate' => '🍫',
    ],

];
__COMIBOX_FIN__
escribir app/Models/Dish.php <<'__COMIBOX_FIN__'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Dish extends Model
{
    /** Formatos de foto aceptados, en orden de preferencia. */
    public const EXTENSIONES_FOTO = ['webp', 'jpg', 'jpeg', 'png'];

    protected $fillable = [
        'nombre', 'tipo', 'tiempo_preparacion_min',
        'costo_aproximado', 'style_id', 'objetivo',
    ];

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'dish_ingredients')
            ->withPivot('cantidad', 'es_opcional');
    }

    public function combos(): BelongsToMany
    {
        return $this->belongsToMany(Combo::class, 'combo_dishes')
            ->withPivot('rol');
    }

    /**
     * Nombre del archivo de la foto, sin extensión.
     * Ejemplo: "Huevos motuleños" → "huevos-motulenos".
     */
    protected function slug(): Attribute
    {
        return Attribute::get(fn () => Str::slug($this->nombre));
    }

    /**
     * URL de la foto del plato (public/img/platos/{slug}.webp, .jpg, .jpeg o .png),
     * o null si todavía no existe. Mientras no haya foto, las vistas muestran un símbolo.
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(function () {
            foreach (self::EXTENSIONES_FOTO as $extension) {
                $ruta = "img/platos/{$this->slug}.{$extension}";

                if (is_file(public_path($ruta))) {
                    return asset($ruta);
                }
            }

            return null;
        });
    }
}
__COMIBOX_FIN__
escribir app/Http/Controllers/AcercaController.php <<'__COMIBOX_FIN__'
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\Style;
use Illuminate\View\View;

class AcercaController extends Controller
{
    /** Página «Acerca del proyecto»: explica qué es ComiBox y muestra las cifras reales del catálogo. */
    public function __invoke(): View
    {
        return view('acerca', [
            'totalIngredientes' => Ingredient::count(),
            'totalCategorias'   => Category::count(),
            'totalPlatos'       => Dish::count(),
            'totalEstilos'      => Style::count(),
        ]);
    }
}
__COMIBOX_FIN__
escribir app/Console/Commands/PlatosFotos.php <<'__COMIBOX_FIN__'
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
__COMIBOX_FIN__
escribir routes/web.php <<'__COMIBOX_FIN__'
<?php

use App\Http\Controllers\AcercaController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComboController::class, 'index'])->name('combos.index');
Route::post('/combos', [ComboController::class, 'generar'])->name('combos.generar');
Route::get('/combos/{combo}', [ComboController::class, 'show'])->name('combos.show');
Route::get('/historial', [ComboController::class, 'historial'])->name('combos.historial');

Route::post('/planes', [PlanController::class, 'generar'])->name('planes.generar');
Route::get('/planes/{plan}', [PlanController::class, 'show'])->name('planes.show');

Route::get('/acerca', AcercaController::class)->name('acerca');
__COMIBOX_FIN__
escribir tests/Feature/AcercaTest.php <<'__COMIBOX_FIN__'
<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcercaTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_acerca_del_proyecto_carga(): void
    {
        $this->get('/acerca')
            ->assertOk()
            ->assertSee('Acerca del proyecto')
            ->assertSee('Gael Pérez Saldaña')
            ->assertSee('José Julián Vásquez Aguirre')
            ->assertSee('Lo que ComiBox no hace');
    }

    public function test_las_cifras_salen_del_catalogo_real(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->get('/acerca')
            ->assertOk()
            ->assertSee('<strong>' . Ingredient::count() . '</strong>', false);
    }

    public function test_el_pie_de_pagina_enlaza_a_acerca_desde_cualquier_pantalla(): void
    {
        foreach (['/', '/historial', '/acerca'] as $ruta) {
            $this->get($ruta)
                ->assertOk()
                ->assertSee('Acerca del proyecto')
                ->assertSee(route('acerca'));
        }
    }
}
__COMIBOX_FIN__
escribir tests/Feature/FotosPlatosTest.php <<'__COMIBOX_FIN__'
<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Services\ComboGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\Concerns\CreaCatalogoDePrueba;
use Tests\TestCase;

class FotosPlatosTest extends TestCase
{
    use RefreshDatabase, CreaCatalogoDePrueba;

    // Plato inventado para las pruebas: así nunca se toca una foto real de public/img/platos.
    private const PLATO = 'Plato de prueba fotos';
    private const SLUG = 'plato-de-prueba-fotos';

    /** @var string[] Archivos creados por la prueba, para borrarlos al terminar. */
    private array $archivos = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->crearMundo();
        $this->crearPlato(self::PLATO, 'principal', 10, 10, 'Mexicano', 'rapida', ['Pollo']);
        File::ensureDirectoryExists(public_path('img/platos'));
    }

    protected function tearDown(): void
    {
        foreach ($this->archivos as $archivo) {
            File::delete($archivo);
        }

        parent::tearDown();
    }

    private function ponerFoto(string $extension = 'jpg'): void
    {
        $ruta = public_path('img/platos/' . self::SLUG . '.' . $extension);
        File::put($ruta, 'foto de prueba');
        $this->archivos[] = $ruta;
    }

    private function plato(): Dish
    {
        return Dish::where('nombre', self::PLATO)->firstOrFail();
    }

    public function test_el_nombre_del_archivo_se_arma_con_el_nombre_del_plato(): void
    {
        $this->assertSame(self::SLUG, $this->plato()->slug);

        $acentos = $this->crearPlato('Ñoquis de papa', 'principal', 45, 45, 'Italiano', 'economica', ['Pasta']);
        $this->assertSame('noquis-de-papa', $acentos->slug);
    }

    public function test_sin_archivo_no_hay_foto(): void
    {
        $this->assertNull($this->plato()->foto_url);
    }

    public function test_con_archivo_devuelve_su_url(): void
    {
        $this->ponerFoto('jpg');

        $this->assertSame(asset('img/platos/' . self::SLUG . '.jpg'), $this->plato()->foto_url);
    }

    public function test_webp_tiene_prioridad_sobre_jpg(): void
    {
        $this->ponerFoto('jpg');
        $this->ponerFoto('webp');

        $this->assertSame(asset('img/platos/' . self::SLUG . '.webp'), $this->plato()->foto_url);
    }

    public function test_la_pantalla_del_combo_muestra_la_foto_si_existe(): void
    {
        $this->ponerFoto('jpg');

        $sesion = Str::random(40);
        $servicio = app(ComboGeneratorService::class);
        $combo = $servicio->guardar($servicio->generar($this->ids(['Pollo'])), $sesion);

        $this->withCookie(config('session.cookie'), $sesion)
            ->get(route('combos.show', $combo))
            ->assertOk()
            ->assertSee(self::PLATO)
            ->assertSee('img/platos/' . self::SLUG . '.jpg', false)
            ->assertSee('class="plato-foto"', false)
            ->assertDontSee('plato-sin-foto', false);
    }

    public function test_la_pantalla_del_combo_funciona_sin_foto(): void
    {
        $sesion = Str::random(40);
        $servicio = app(ComboGeneratorService::class);
        $combo = $servicio->guardar($servicio->generar($this->ids(['Pollo'])), $sesion);

        $this->withCookie(config('session.cookie'), $sesion)
            ->get(route('combos.show', $combo))
            ->assertOk()
            ->assertSee(self::PLATO)
            ->assertSee('plato-sin-foto', false)
            ->assertDontSee('class="plato-foto"', false);
    }

    public function test_el_comando_lista_los_platos_sin_foto(): void
    {
        $this->artisan('platos:fotos')
            ->expectsOutputToContain(self::SLUG)
            ->expectsOutputToContain('arroz-con-pollo')
            ->assertExitCode(0);
    }

    public function test_el_comando_no_lista_los_que_ya_tienen_foto(): void
    {
        $this->ponerFoto('jpg');

        $this->artisan('platos:fotos')
            ->expectsOutputToContain('arroz-con-pollo')
            ->doesntExpectOutputToContain(self::SLUG . '.webp')
            ->assertExitCode(0);
    }

    public function test_el_comando_filtra_por_tipo(): void
    {
        $this->artisan('platos:fotos', ['--tipo' => 'bebida'])
            ->expectsOutputToContain('agua-de-limon')
            ->doesntExpectOutputToContain('arroz-con-pollo')
            ->assertExitCode(0);
    }
}
__COMIBOX_FIN__

mkdir -p public/img/platos
touch public/img/platos/.gitkeep
echo "  ✔ public/img/platos/"

php artisan view:clear >/dev/null 2>&1 || true
php artisan config:clear >/dev/null 2>&1 || true

echo ""
echo "Listo. Si algún archivo ya existía, quedó una copia en $RESPALDO (solo si lo había)."
[ -f tests/Concerns/CreaCatalogoDePrueba.php ] || echo "⚠️  Falta tests/Concerns/CreaCatalogoDePrueba.php (lo usan las pruebas de fotos)."
[ -f database/seeders/CatalogSeeder.php ]       || echo "⚠️  Falta database/seeders/CatalogSeeder.php (lo usa una prueba de Acerca)."
echo ""
echo "Siguiente:"
echo "  1) php artisan test"
echo "  2) php artisan serve   y abre http://127.0.0.1:8000"
echo "  3) php artisan platos:fotos   para ver qué fotos faltan (van en public/img/platos/)"

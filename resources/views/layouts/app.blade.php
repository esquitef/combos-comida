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
    
            <p>© {{ date('Y') }} ComiBox. Recetas adaptadas de recetarios de cocina; costos y tiempos aproximados.</p>
        </div>
    </footer>
</body>
</html>

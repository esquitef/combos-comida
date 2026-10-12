<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Combos') · Generador de Combos de Comida</title>

    <style>
        :root{--naranja:#E85D24;--naranja-osc:#C24B18;--carbon:#2D2A26;--dorado:#FFB100;--crema:#F3EEE8;--gris:#6B6560;--borde:#E4DFD6}
        *{box-sizing:border-box}
        body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;background:#FAFAF8;color:var(--carbon);line-height:1.5}
        .wrap{max-width:960px;margin:0 auto;padding:0 20px}

        .top{background:var(--carbon)}
        .bar{display:flex;justify-content:space-between;align-items:center;height:60px}
        .marca{color:#fff;font-weight:700;font-size:1.1rem;text-decoration:none}
        nav a{color:#D8D3CB;text-decoration:none;margin-left:20px;padding-bottom:4px}
        nav a.activo{color:var(--dorado);border-bottom:2px solid var(--dorado)}

        h1{font-size:1.9rem;margin:28px 0 4px}
        h2{font-size:1.15rem;margin:0 0 8px}
        .sub,.ayuda{color:var(--gris);margin:0 0 16px}
        .card{background:#fff;border:1px solid var(--borde);border-radius:12px;padding:20px;margin:16px 0}

        fieldset{border:0;padding:0;margin:0 0 14px}
        legend{font-weight:600;color:var(--naranja-osc);margin-bottom:6px;padding:0}
        .chips{display:flex;flex-wrap:wrap;gap:8px}
        .chip{display:inline-flex;align-items:center;border:1px solid var(--borde);border-radius:999px;background:var(--crema);overflow:hidden}
        .chip-main,.prio{position:relative}
        .chip-main{display:flex;align-items:center;padding:6px 14px;cursor:pointer;user-select:none}
        .chip-main input,.prio input{position:absolute;opacity:0;pointer-events:none}
        .chip:has(.sel:checked){background:var(--naranja);border-color:var(--naranja);color:#fff}
        .chip:has(.sel:focus-visible){outline:2px solid var(--dorado);outline-offset:2px}
        .prio{display:none;padding:6px 12px 6px 2px;cursor:pointer;opacity:.5;filter:grayscale(1)}
        .chip:has(.sel:checked) .prio{display:inline-flex}
        .prio:has(input:checked){opacity:1;filter:none}

        .grid-prefs{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px}
        .grid-prefs label{display:flex;flex-direction:column;gap:4px;font-size:.9rem;color:var(--gris)}
        .grid-prefs select,.grid-prefs input{padding:9px 10px;border:1px solid var(--borde);border-radius:8px;font-size:1rem;background:#fff;color:var(--carbon)}

        .acciones{display:flex;gap:12px;flex-wrap:wrap;margin:20px 0 40px}
        .btn{background:var(--naranja);color:#fff;border:0;border-radius:10px;padding:13px 26px;font-size:1rem;font-weight:600;cursor:pointer}
        .btn:hover{background:var(--naranja-osc)}
        .btn-alt{background:var(--carbon)}
        .btn-alt:hover{background:#000}
        .alerta{background:#FDECEA;border:1px solid #F5B5AD;color:#8A1F11;border-radius:10px;padding:12px 18px;margin:16px 0}
        .alerta ul{margin:0;padding-left:18px}

        .volver{display:inline-block;margin-top:20px;color:var(--naranja-osc);text-decoration:none}
        .resumen{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:16px 0}
        .resumen div{background:var(--carbon);color:#fff;border-radius:12px;padding:14px 18px}
        .resumen span{display:block;font-size:.8rem;color:#CFCAC2}
        .resumen strong{font-size:1.5rem;color:var(--dorado)}
        .plato{background:#fff;border:1px solid var(--borde);border-left:6px solid #3A9D5D;border-radius:12px;padding:16px 20px;margin:14px 0}
        .plato-falta{border-left-color:var(--dorado)}
        .plato-cab{display:flex;justify-content:space-between;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em}
        .rol{color:var(--naranja-osc);font-weight:700}
        .estado{color:#2F7D4A;font-weight:600}
        .plato-falta .estado{color:#B07400}
        .meta{color:var(--gris);margin:0 0 10px}
        .ings{list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;gap:6px}
        .ings li{padding:3px 10px;border-radius:999px;font-size:.85rem;background:var(--crema)}
        .ings .ok{background:#E3F3E8;color:#245C38}
        .ings .falta{background:#FFF0CC;color:#8A5A00}
        .ings .opc{background:#fff;border:1px dashed var(--borde);color:var(--gris)}
        .compras{list-style:none;padding:0;margin:0 0 14px}
        .compras li{padding:8px 0;border-bottom:1px solid var(--borde)}
        .compras small{color:var(--gris)}

        .combo-item{display:flex;justify-content:space-between;align-items:center;gap:16px;color:inherit;text-decoration:none}
        .combo-item small{display:block;color:var(--gris)}
        .puntaje{flex:none;background:var(--naranja);color:#fff;border-radius:50%;width:48px;height:48px;display:grid;place-items:center;font-weight:700}
        .pie{color:var(--gris);font-size:.85rem;padding:30px 20px}

        @media (max-width:600px){.resumen{grid-template-columns:1fr}}
        @media print{.top,.volver,.btn,.pie{display:none}}

        .aviso{background:#FFF6DB;border-color:#F2D58A;color:#6B4A00}
.dia-cab{display:flex;justify-content:space-between;align-items:baseline}
.dia-cab a{color:var(--naranja-osc);text-decoration:none;font-size:.9rem}
.dia-platos{list-style:none;padding:0;margin:0 0 8px}
.dia-platos li{display:flex;flex-wrap:wrap;align-items:baseline;gap:4px 10px;padding:6px 0;border-bottom:1px solid var(--borde)}
.dia-platos .rol{min-width:120px;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em}
.dia-platos small{color:var(--gris)}
.falta-tag{background:#FFF0CC;color:#8A5A00;border-radius:999px;padding:1px 10px;font-size:.8rem}
.dia .meta{margin:8px 0 0}
    </style>
</head>
<body>
    <header class="top">
        <div class="wrap bar">
            <a class="marca" href="{{ route('combos.index') }}">🍽️ Combos de Comida</a>
            <nav>
                <a href="{{ route('combos.index') }}" @class(['activo' => request()->routeIs('combos.index')])>Generar</a>
                <a href="{{ route('combos.historial') }}" @class(['activo' => request()->routeIs('combos.historial')])>Historial</a>
            </nav>
        </div>
    </header>

    <main class="wrap">
        @yield('contenido')
    </main>

    <footer class="wrap pie">Proyecto académico · Laravel + MySQL</footer>
</body>
</html>
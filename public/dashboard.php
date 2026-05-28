<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$empresa = $_SESSION['empresa'] ?? 'TallerPro';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

    <title>TallerPro</title>

    <meta name="theme-color" content="#020617">
    <meta name="background-color" content="#020617">
    <meta name="mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="TallerPro">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="./icons/icon-192.png">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --dark: #020617;
            --dark2: #071225;
            --panel: #0f1b31;
            --body: #eef3f8;
            --blue: #2563eb;
        }

        html {
            width: 100%;
            min-height: 100%;
            margin: 0;
            background: var(--dark);
            overflow-x: hidden;
            -webkit-text-size-adjust: 100%;
            overscroll-behavior: none;
        }

        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            overflow-x: hidden;
            background:
                linear-gradient(180deg,
                    var(--dark) 0px,
                    var(--dark) 300px,
                    var(--body) 300px,
                    var(--body) 100%);
            color: #020617;
            touch-action: manipulation;
            overscroll-behavior: none;
            -webkit-font-smoothing: antialiased;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        input,
        select,
        textarea,
        button {
            font-size: 16px;
        }

        .app-shell {
            min-height: 100dvh;
            background:
                linear-gradient(180deg,
                    var(--dark) 0px,
                    var(--dark) 300px,
                    var(--body) 300px,
                    var(--body) 100%);
        }

        .top-header {
            padding-top: calc(22px + env(safe-area-inset-top));
            background:
                radial-gradient(circle at 88% 8%, rgba(8, 145, 178, .35), transparent 30%),
                radial-gradient(circle at 15% 100%, rgba(37, 99, 235, .20), transparent 32%),
                linear-gradient(145deg, #020617 0%, #061426 52%, #020617 100%);
        }

        .metric-card {
            background:
                linear-gradient(145deg, rgba(255, 255, 255, .10), rgba(255, 255, 255, .045));
            border: 1px solid rgba(255, 255, 255, .12);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, .08),
                0 24px 55px rgba(0, 0, 0, .25);
        }

        .compact-status-card {
            background:
                linear-gradient(145deg, rgba(255, 255, 255, .105), rgba(255, 255, 255, .045));
            border: 1px solid rgba(255, 255, 255, .12);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, .08),
                0 18px 42px rgba(0, 0, 0, .22);
        }

        .compact-stat {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .07);
        }

        .soft-card {
            background: rgba(255, 255, 255, .94);
            box-shadow:
                0 18px 36px rgba(15, 23, 42, .08),
                inset 0 1px 0 rgba(255, 255, 255, .9);
        }

        .bottom-safe {
            padding-bottom: calc(10px + env(safe-area-inset-bottom));
        }

        .bottom-bar {
            width: min(92%, 390px);
            height: 72px;
            margin: 0 auto;
            background:
                radial-gradient(circle at 50% -10%, rgba(37, 99, 235, .28), transparent 38%),
                linear-gradient(145deg, #020617 0%, #081426 52%, #020617 100%);
            border: 1px solid rgba(255, 255, 255, .10);
            box-shadow:
                0 18px 45px rgba(2, 6, 23, .42),
                inset 0 1px 0 rgba(255, 255, 255, .08);
        }

        .nav-item {
            height: 56px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            font-size: 10px;
            font-weight: 900;
            color: rgba(226, 232, 240, .72);
            transition: transform .14s ease, color .14s ease;
        }

        .nav-item:active {
            transform: scale(.94);
        }

        .nav-item.active {
            color: #60a5fa;
        }

        .fab {
            width: 58px;
            height: 58px;
            background:
                radial-gradient(circle at 35% 25%, rgba(255, 255, 255, .35), transparent 30%),
                linear-gradient(145deg, #3b82f6 0%, #1d4ed8 100%);
            box-shadow:
                0 16px 34px rgba(37, 99, 235, .50),
                inset 0 1px 0 rgba(255, 255, 255, .28);
        }
    </style>
</head>

<body>

    <div class="app-shell mx-auto max-w-[430px] pb-28">

        <header class="top-header relative overflow-hidden rounded-b-[2rem] px-5 pb-6 text-white shadow-2xl">

            <div class="relative flex items-start justify-between">
                <div class="min-w-0">
                    <div class="mb-1 flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-sky-400"></span>

                        <p class="text-[11px] font-black uppercase tracking-[.28em] text-slate-400">
                            Taller activo
                        </p>
                    </div>

                    <h1 class="max-w-[270px] truncate text-[2rem] font-black leading-none tracking-[-.04em]">
                        <?php echo htmlspecialchars($empresa); ?>
                    </h1>
                </div>

                <a
                    href="logout.php"
                    class="grid h-[3.15rem] w-[3.15rem] shrink-0 place-items-center rounded-[1.25rem] border border-white/10 bg-white/10 text-2xl shadow-xl active:scale-95"
                    aria-label="Salir">
                    ⏻
                </a>
            </div>

            <section class="compact-status-card relative mt-6 rounded-[1.7rem] p-4">

                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.18em] text-slate-400">
                            Resumen de hoy
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-300">
                            Control rápido del taller
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/15 bg-emerald-400/15 px-3 py-2 text-xs font-black text-emerald-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Abierto
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center">

                    <div class="compact-stat rounded-[1.25rem] px-2 py-3">
                        <div class="mx-auto mb-2 grid h-10 w-10 place-items-center rounded-xl bg-blue-500/15 text-xl">
                            🧾
                        </div>

                        <p class="text-2xl font-black leading-none">
                            0
                        </p>

                        <p class="mt-1 text-[11px] font-bold text-slate-400">
                            Órdenes
                        </p>
                    </div>

                    <div class="compact-stat rounded-[1.25rem] px-2 py-3">
                        <div class="mx-auto mb-2 grid h-10 w-10 place-items-center rounded-xl bg-amber-500/15 text-xl">
                            ⏳
                        </div>

                        <p class="text-2xl font-black leading-none">
                            0
                        </p>

                        <p class="mt-1 text-[11px] font-bold text-slate-400">
                            Pendientes
                        </p>
                    </div>

                    <div class="compact-stat rounded-[1.25rem] px-2 py-3">
                        <div class="mx-auto mb-2 grid h-10 w-10 place-items-center rounded-xl bg-emerald-500/15 text-xl">
                            💳
                        </div>

                        <p class="text-2xl font-black leading-none">
                            0
                        </p>

                        <p class="mt-1 text-[11px] font-bold text-slate-400">
                            Fiados
                        </p>
                    </div>

                </div>

            </section>

        </header>

        <main class="px-5 pt-5">

            <section class="grid grid-cols-2 gap-3">

                <a href="clientes.php" class="soft-card min-h-[142px] rounded-[1.7rem] p-5 ring-1 ring-slate-200/70 active:scale-[.98]">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 text-2xl">
                        👤
                    </div>

                    <div class="mt-7 flex items-end justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-black tracking-[-.03em]">Clientes</h2>
                            <p class="mt-1 text-sm font-bold text-slate-500">Agenda y WhatsApp</p>
                        </div>

                        <span class="text-3xl font-light text-slate-400">›</span>
                    </div>
                </a>

                <a href="vehiculos.php" class="soft-card min-h-[142px] rounded-[1.7rem] p-5 ring-1 ring-slate-200/70 active:scale-[.98]">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-50 text-2xl">
                        🚗
                    </div>

                    <div class="mt-7 flex items-end justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-black tracking-[-.03em]">Vehículos</h2>
                            <p class="mt-1 text-sm font-bold text-slate-500">Buscar por placa</p>
                        </div>

                        <span class="text-3xl font-light text-slate-400">›</span>
                    </div>
                </a>

                <a href="cotizaciones.php" class="soft-card min-h-[142px] rounded-[1.7rem] p-5 ring-1 ring-slate-200/70 active:scale-[.98]">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-violet-50 text-2xl">
                        🧾
                    </div>

                    <div class="mt-7 flex items-end justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-black tracking-[-.03em]">Cotizaciones</h2>
                            <p class="mt-1 text-sm font-bold text-slate-500">Cotizaciones activas</p>
                        </div>

                        <span class="text-3xl font-light text-slate-400">›</span>
                    </div>
                </a>

                <a href="ordenes.php" class="soft-card min-h-[142px] rounded-[1.7rem] p-5 ring-1 ring-slate-200/70 active:scale-[.98]">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-violet-50 text-2xl">
                        🧾
                    </div>

                    <div class="mt-7 flex items-end justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-black tracking-[-.03em]">Órdenes</h2>
                            <p class="mt-1 text-sm font-bold text-slate-500">Trabajos activos</p>
                        </div>

                        <span class="text-3xl font-light text-slate-400">›</span>
                    </div>
                </a>

                <a href="deudas.php" class="soft-card min-h-[142px] rounded-[1.7rem] p-5 ring-1 ring-slate-200/70 active:scale-[.98]">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-2xl">
                        💰
                    </div>

                    <div class="mt-7 flex items-end justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-black tracking-[-.03em]">Deudas</h2>
                            <p class="mt-1 text-sm font-bold text-slate-500">Fiados y pagos</p>
                        </div>

                        <span class="text-3xl font-light text-slate-400">›</span>
                    </div>
                </a>

                <a href="productos.php" class="soft-card min-h-[142px] rounded-[1.7rem] p-5 ring-1 ring-slate-200/70 active:scale-[.98]">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-2xl">
                        📦
                    </div>

                    <div class="mt-7 flex items-end justify-between gap-2">
                        <div>
                            <h2 class="text-xl font-black tracking-[-.03em]">Stock</h2>
                            <p class="mt-1 text-sm font-bold text-slate-500">Inventario taller</p>
                        </div>

                        <span class="text-3xl font-light text-slate-400">›</span>
                    </div>
                </a>

            </section>

            <section class="mt-6">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-xl font-black tracking-[-.04em]">
                        Acciones rápidas
                    </h2>

                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-600">
                        MVP
                    </span>
                </div>

                <div class="overflow-hidden rounded-[1.7rem] bg-white shadow-sm ring-1 ring-slate-200/80">

                    <button class="flex w-full items-center justify-between gap-3 px-4 py-4 text-left active:bg-slate-50">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-slate-950 text-2xl text-white">
                                🔍
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-base font-black">Buscar vehículo</p>
                                <p class="truncate text-sm font-semibold text-slate-500">Ingresa placa del cliente</p>
                            </div>
                        </div>

                        <span class="shrink-0 text-3xl font-light text-slate-400">›</span>
                    </button>

                    <div class="mx-4 h-px bg-slate-100"></div>

                    <button class="flex w-full items-center justify-between gap-3 px-4 py-4 text-left active:bg-slate-50">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-emerald-500 text-2xl text-white">
                                📲
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-base font-black">Enviar aviso WhatsApp</p>
                                <p class="truncate text-sm font-semibold text-slate-500">Recordatorio manual</p>
                            </div>
                        </div>

                        <span class="shrink-0 text-3xl font-light text-slate-400">›</span>
                    </button>

                </div>
            </section>

            <section class="mt-6">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-xl font-black tracking-[-.04em]">
                        Trabajos recientes
                    </h2>

                    <span class="text-xs font-black text-slate-500">
                        Hoy
                    </span>
                </div>

                <div class="rounded-[1.7rem] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-3xl bg-slate-100 text-3xl">
                        🔧
                    </div>

                    <h3 class="mt-3 font-black">
                        Sin trabajos aún
                    </h3>

                    <p class="mx-auto mt-1 max-w-[260px] text-sm font-semibold text-slate-500">
                        Cuando registres una orden, aparecerá aquí como historial del taller.
                    </p>
                </div>
            </section>

        </main>

        <div class="fixed inset-x-0 bottom-0 z-50 bottom-safe">
            <nav class="bottom-bar relative rounded-[1.65rem] px-3 pb-2 pt-2 text-white">

                <a
                    href="nueva_orden.php"
                    class="fab absolute left-1/2 top-0 grid -translate-x-1/2 -translate-y-7 place-items-center rounded-full text-4xl font-light text-white ring-[8px] ring-[#eef3f8] active:scale-95"
                    aria-label="Nueva orden">
                    +
                </a>

                <div class="grid grid-cols-5 items-end text-center">

                    <a href="#" class="nav-item active">
                        <div class="text-[1.35rem] leading-none">⌂</div>
                        <p>Inicio</p>
                    </a>

                    <a href="vehiculos.php" class="nav-item">
                        <div class="text-[1.28rem] leading-none">🚗</div>
                        <p>Autos</p>
                    </a>

                    <a href="ordenes.php" class="nav-item pt-8">
                        <p>Nueva</p>
                    </a>

                    <a href="nueva_orden.php" class="nav-item">
                        <div class="text-[1.20rem] leading-none">🧾</div>
                        <p>Órdenes</p>
                    </a>

                    <a href="#" class="nav-item">
                        <div class="text-[1.35rem] leading-none">☰</div>
                        <p>Más</p>
                    </a>

                </div>
            </nav>
        </div>

    </div>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js');
        }
    </script>

</body>

</html>
<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrCotizaciones.php';

$empresa_id = (int)($_SESSION['empresa_id'] ?? 0);
$vehiculo_id = (int)($_GET['vehiculo_id'] ?? 0);

if ($vehiculo_id <= 0) {
    header("Location: vehiculos.php");
    exit;
}

$datos = CtrCotizaciones::datosNueva();
$vehiculo = $datos['vehiculo'];

if (!$vehiculo) {
    header("Location: vehiculos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

    <title>Nueva orden - TallerPro</title>

    <meta name="theme-color" content="#020617">
    <meta name="background-color" content="#020617">
    <meta name="mobile-web-app-capable" content="yes">

    <link rel="manifest" href="manifest.json">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --dark: #020617;
            --body: #eef3f8;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            overflow-x: hidden;
            background: var(--dark);
            -webkit-text-size-adjust: 100%;
            overscroll-behavior: none;
            touch-action: manipulation;
        }

        body {
            background:
                linear-gradient(180deg,
                    var(--dark) 0px,
                    var(--dark) 260px,
                    var(--body) 260px,
                    var(--body) 100%);
            color: #020617;
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
                    var(--dark) 260px,
                    var(--body) 260px,
                    var(--body) 100%);
        }

        .top-header {
            padding-top: calc(22px + env(safe-area-inset-top));
            background:
                radial-gradient(circle at 88% 8%, rgba(8, 145, 178, .35), transparent 30%),
                radial-gradient(circle at 15% 100%, rgba(37, 99, 235, .20), transparent 32%),
                linear-gradient(145deg, #020617 0%, #061426 52%, #020617 100%);
        }

        .soft-card {
            background: rgba(255, 255, 255, .96);
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

                <a
                    href="vehiculos.php?placa=<?php echo urlencode($vehiculo['placa']); ?>"
                    class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95">
                    ‹
                </a>

                <div class="text-center">
                    <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                        Trabajo
                    </p>

                    <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                        Nueva orden
                    </h1>
                </div>

                <a
                    href="logout.php"
                    class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95">
                    ⏻
                </a>

            </div>

            <section class="mt-6 rounded-[1.7rem] border border-white/10 bg-white/10 p-4 shadow-2xl backdrop-blur-xl">

                <p class="text-xs font-black uppercase tracking-[.18em] text-slate-400">
                    Vehículo seleccionado
                </p>

                <h2 class="mt-1 text-2xl font-black tracking-[-.04em]">
                    <?php echo htmlspecialchars($vehiculo['placa']); ?>
                </h2>

                <p class="mt-1 text-sm font-bold text-slate-300">
                    <?php echo htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']); ?>
                </p>

                <p class="mt-1 text-xs font-semibold text-slate-400">
                    Cliente: <?php echo htmlspecialchars($vehiculo['cliente']); ?>
                </p>

            </section>

        </header>

        <main class="px-5 pt-5">

            <section class="space-y-4">

                <a
                    href="orden_rapida.php?vehiculo_id=<?php echo (int)$vehiculo['vehiculo_id']; ?>"
                    class="soft-card block rounded-[1.8rem] p-5 ring-1 ring-slate-200/70 active:scale-[.98]">
                    <div class="flex items-start gap-4">
                        <div class="grid h-16 w-16 shrink-0 place-items-center rounded-[1.5rem] bg-blue-50 text-4xl">
                            ⚡
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h2 class="text-2xl font-black tracking-[-.04em]">
                                    Orden rápida
                                </h2>

                                <span class="text-3xl font-light text-slate-400">
                                    ›
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-500">
                                Para mantenimiento menor, mayor, cambio de aceite, frenos, escaneo o lavado.
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-black text-slate-600">Plantillas</span>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-black text-blue-700">Más fácil</span>
                            </div>
                        </div>
                    </div>
                </a>

                <a
                    href="orden_detallada.php?vehiculo_id=<?php echo (int)$vehiculo['vehiculo_id']; ?>"
                    class="soft-card block rounded-[1.8rem] p-5 ring-1 ring-slate-200/70 active:scale-[.98]">
                    <div class="flex items-start gap-4">
                        <div class="grid h-16 w-16 shrink-0 place-items-center rounded-[1.5rem] bg-slate-100 text-4xl">
                            🧾
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h2 class="text-2xl font-black tracking-[-.04em]">
                                    Orden detallada
                                </h2>

                                <span class="text-3xl font-light text-slate-400">
                                    ›
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-500">
                                Para trabajos personalizados con servicios, productos de stock y compras externas.
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-black text-slate-600">Libre</span>
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-[11px] font-black text-amber-700">Más completo</span>
                            </div>
                        </div>
                    </div>
                </a>

            </section>

            <section class="mt-6 soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">

                <h3 class="text-lg font-black tracking-[-.03em]">
                    ¿Cuál usar?
                </h3>

                <div class="mt-4 space-y-3 text-sm font-semibold leading-6 text-slate-600">

                    <div class="rounded-2xl bg-blue-50 p-4">
                        <p class="font-black text-blue-700">
                            Orden rápida
                        </p>

                        <p class="mt-1">
                            Para trabajos repetitivos. El mecánico toca menos y el sistema guía paso a paso.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="font-black text-slate-800">
                            Orden detallada
                        </p>

                        <p class="mt-1">
                            Para trabajos donde se agregan varios servicios, repuestos o compras externas.
                        </p>
                    </div>

                </div>

            </section>

        </main>

        <div class="fixed inset-x-0 bottom-0 z-50 bottom-safe">
            <nav class="bottom-bar relative rounded-[1.65rem] px-3 pb-2 pt-2 text-white">

                <a
                    href="nueva_orden.php?vehiculo_id=<?php echo (int)$vehiculo['vehiculo_id']; ?>"
                    class="fab absolute left-1/2 top-0 grid -translate-x-1/2 -translate-y-7 place-items-center rounded-full text-4xl font-light text-white ring-[8px] ring-[#eef3f8] active:scale-95">
                    +
                </a>

                <div class="grid grid-cols-5 items-end text-center">

                    <a href="dashboard.php" class="nav-item">
                        <div class="text-[1.35rem] leading-none">⌂</div>
                        <p>Inicio</p>
                    </a>

                    <a href="vehiculos.php" class="nav-item active">
                        <div class="text-[1.28rem] leading-none">🚗</div>
                        <p>Autos</p>
                    </a>

                    <a href="nueva_orden.php?vehiculo_id=<?php echo (int)$vehiculo['vehiculo_id']; ?>" class="nav-item pt-8">
                        <p>Nueva</p>
                    </a>

                    <a href="ordenes.php" class="nav-item">
                        <div class="text-[1.20rem] leading-none">🧾</div>
                        <p>Órdenes</p>
                    </a>

                    <a href="productos.php" class="nav-item">
                        <div class="text-[1.35rem] leading-none">📦</div>
                        <p>Stock</p>
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
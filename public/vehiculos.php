<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrVehiculos.php';

$resultado = CtrVehiculos::buscar();

$buscado = $resultado['buscado'];
$vehiculo = $resultado['vehiculo'];
$historial = $resultado['historial'];

$placaBuscada = strtoupper(trim($_GET['placa'] ?? ''));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

    <title>Vehículos - TallerPro</title>

    <meta name="theme-color" content="#020617">
    <meta name="background-color" content="#020617">
    <meta name="mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="TallerPro">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="manifest" href="manifest.json">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --dark: #020617;
            --body: #eef3f8;
            --blue: #2563eb;
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
                    var(--dark) 230px,
                    var(--body) 230px,
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
                    var(--dark) 230px,
                    var(--body) 230px,
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
                    href="dashboard.php"
                    class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95">
                    ‹
                </a>

                <div class="text-center">
                    <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                        Buscar
                    </p>

                    <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                        Vehículo
                    </h1>
                </div>

                <a
                    href="logout.php"
                    class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95">
                    ⏻
                </a>

            </div>

            <form method="GET" class="relative mt-6">
                <div class="rounded-[1.8rem] border border-white/10 bg-white/10 p-3 shadow-2xl backdrop-blur-xl">

                    <label class="mb-2 block px-2 text-xs font-black uppercase tracking-[.18em] text-slate-400">
                        Placa del vehículo
                    </label>

                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            name="placa"
                            value="<?php echo htmlspecialchars($placaBuscada); ?>"
                            placeholder="ABC123"
                            autocomplete="off"
                            maxlength="12"
                            class="min-w-0 flex-1 rounded-[1.25rem] border border-white/10 bg-white px-4 py-4 text-center text-2xl font-black uppercase tracking-[.18em] text-slate-950 outline-none">

                        <button
                            type="submit"
                            class="grid h-[58px] w-[58px] shrink-0 place-items-center rounded-[1.25rem] bg-blue-600 text-2xl text-white shadow-lg active:scale-95">
                            🔍
                        </button>
                    </div>

                </div>
            </form>

        </header>

        <main class="px-5 pt-5">

            <?php if (!$buscado): ?>

                <section class="soft-card rounded-[1.8rem] p-6 text-center ring-1 ring-slate-200/70">

                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-blue-50 text-4xl">
                        🚗
                    </div>

                    <h2 class="mt-4 text-2xl font-black tracking-[-.04em]">
                        Busca por placa
                    </h2>

                    <p class="mx-auto mt-2 max-w-[290px] text-sm font-semibold leading-6 text-slate-500">
                        Ingresa la placa para ver el cliente, historial del vehículo y crear cotizaciones u órdenes.
                    </p>

                    <div class="mt-5 rounded-2xl bg-slate-50 p-4 text-left">
                        <p class="text-xs font-black uppercase tracking-[.16em] text-slate-400">
                            Ejemplo de prueba
                        </p>

                        <p class="mt-1 text-lg font-black text-slate-950">
                            ABC123
                        </p>
                    </div>

                </section>

            <?php elseif ($vehiculo): ?>

                <section class="soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0">
                            <p class="text-xs font-black uppercase tracking-[.18em] text-blue-600">
                                Vehículo encontrado
                            </p>

                            <h2 class="mt-1 text-3xl font-black tracking-[-.05em]">
                                <?php echo htmlspecialchars($vehiculo['placa']); ?>
                            </h2>

                            <p class="mt-1 text-sm font-bold text-slate-500">
                                <?php echo htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']); ?>

                                <?php if (!empty($vehiculo['anio'])): ?>
                                    · <?php echo htmlspecialchars($vehiculo['anio']); ?>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-3xl bg-orange-50 text-3xl">
                            🚗
                        </div>

                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-black uppercase tracking-[.14em] text-slate-400">
                                Color
                            </p>

                            <p class="mt-1 font-black">
                                <?php echo htmlspecialchars($vehiculo['color'] ?: 'No registrado'); ?>
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-black uppercase tracking-[.14em] text-slate-400">
                                Kilometraje
                            </p>

                            <p class="mt-1 font-black">
                                <?php echo htmlspecialchars($vehiculo['kilometraje'] ?: 'No registrado'); ?>
                            </p>
                        </div>

                    </div>

                </section>

                <section class="mt-4 soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0">
                            <p class="text-xs font-black uppercase tracking-[.18em] text-slate-400">
                                Cliente
                            </p>

                            <h3 class="mt-1 truncate text-2xl font-black tracking-[-.04em]">
                                <?php echo htmlspecialchars($vehiculo['cliente']); ?>
                            </h3>

                            <p class="mt-1 text-sm font-bold text-slate-500">
                                <?php echo htmlspecialchars($vehiculo['telefono'] ?: 'Sin teléfono'); ?>
                            </p>
                        </div>

                        <?php if (!empty($vehiculo['telefono'])): ?>
                            <a
                                href="https://wa.me/51<?php echo preg_replace('/[^0-9]/', '', $vehiculo['telefono']); ?>"
                                target="_blank"
                                class="grid h-14 w-14 shrink-0 place-items-center rounded-3xl bg-emerald-500 text-3xl text-white shadow-lg active:scale-95">
                                ☎
                            </a>
                        <?php endif; ?>

                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3">

                        <a
                            href="nueva_orden.php?vehiculo_id=<?php echo (int)$vehiculo['id']; ?>"
                            class="rounded-2xl bg-slate-950 px-4 py-4 text-center text-sm font-black text-white shadow-lg active:scale-95">
                            Nueva orden
                        </a>

                        <a
                            href="nueva_cotizacion.php?vehiculo_id=<?php echo (int)$vehiculo['id']; ?>"
                            class="rounded-2xl bg-blue-600 px-4 py-4 text-center text-sm font-black text-white shadow-lg active:scale-95">
                            + Cotización
                        </a>

                        <a
                            href="deudas.php"
                            class="rounded-2xl bg-emerald-600 px-4 py-4 text-center text-sm font-black text-white shadow-lg active:scale-95">
                            Ver deudas
                        </a>

                    </div>

                </section>

                <section class="mt-6">

                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-xl font-black tracking-[-.04em]">
                            Historial
                        </h2>

                        <span class="text-xs font-black text-slate-500">
                            Últimos trabajos
                        </span>
                    </div>

                    <?php if (count($historial) > 0): ?>

                        <div class="space-y-3">

                            <?php foreach ($historial as $orden): ?>

                                <article class="soft-card rounded-[1.5rem] p-4 ring-1 ring-slate-200/70">

                                    <div class="flex items-start justify-between gap-3">

                                        <div class="min-w-0">
                                            <p class="text-sm font-black text-slate-950">
                                                <?php echo htmlspecialchars($orden['descripcion']); ?>
                                            </p>

                                            <p class="mt-1 text-xs font-bold text-slate-500">
                                                <?php echo date('d/m/Y H:i', strtotime($orden['fecha_registro'])); ?>
                                            </p>
                                        </div>

                                        <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-black text-emerald-700">
                                            <?php echo htmlspecialchars($orden['estado']); ?>
                                        </span>

                                    </div>

                                    <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">

                                        <p class="text-xs font-black uppercase tracking-[.14em] text-slate-400">
                                            Total
                                        </p>

                                        <p class="text-lg font-black">
                                            S/ <?php echo number_format((float)$orden['total'], 2); ?>
                                        </p>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-3xl bg-slate-100 text-3xl">
                                🧾
                            </div>

                            <h3 class="mt-3 font-black">
                                Sin historial
                            </h3>

                            <p class="mx-auto mt-1 max-w-[260px] text-sm font-semibold text-slate-500">
                                Este vehículo todavía no tiene órdenes registradas.
                            </p>
                        </div>

                    <?php endif; ?>

                </section>

            <?php else: ?>

                <section class="soft-card rounded-[1.8rem] p-6 text-center ring-1 ring-slate-200/70">

                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-red-50 text-4xl">
                        🔎
                    </div>

                    <h2 class="mt-4 text-2xl font-black tracking-[-.04em]">
                        No encontrado
                    </h2>

                    <p class="mx-auto mt-2 max-w-[290px] text-sm font-semibold leading-6 text-slate-500">
                        No existe ningún vehículo con la placa
                        <strong><?php echo htmlspecialchars($placaBuscada); ?></strong>.
                    </p>

                    <a
                        href="registrar_vehiculo.php?placa=<?php echo urlencode($placaBuscada); ?>"
                        class="mt-5 block rounded-2xl bg-blue-600 px-4 py-4 text-center text-sm font-black text-white shadow-lg active:scale-95">
                        Registrar cliente y vehículo
                    </a>

                </section>

            <?php endif; ?>

        </main>

        <div class="fixed inset-x-0 bottom-0 z-50 bottom-safe">
            <nav class="bottom-bar relative rounded-[1.65rem] px-3 pb-2 pt-2 text-white">

                <a
                    href="registrar_vehiculo.php"
                    class="fab absolute left-1/2 top-0 grid -translate-x-1/2 -translate-y-7 place-items-center rounded-full text-4xl font-light text-white ring-[8px] ring-[#eef3f8] active:scale-95"
                    aria-label="Nuevo vehículo">
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

                    <a href="registrar_vehiculo.php" class="nav-item pt-8">
                        <p>Nuevo</p>
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

        const inputPlaca = document.querySelector('input[name="placa"]');

        if (inputPlaca) {
            inputPlaca.addEventListener('input', function() {
                this.value = this.value
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .slice(0, 12);
            });
        }
    </script>

</body>

</html>
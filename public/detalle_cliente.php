<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrClientes.php';

$datos = CtrClientes::detalle();

$cliente = $datos['cliente'];
$vehiculos = $datos['vehiculos'];
$ordenes = $datos['ordenes'];
$cotizaciones = $datos['cotizaciones'];

if (!$cliente) {
    header("Location: clientes.php");
    exit;
}

$deudaTotal = 0;

foreach ($ordenes as $orden) {
    $deudaTotal += (float)($orden['saldo'] ?? 0);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

    <title>Cliente - TallerPro</title>

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
                    var(--dark) 285px,
                    var(--body) 285px,
                    var(--body) 100%);
            color: #020617;
            -webkit-font-smoothing: antialiased;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        .app-shell {
            min-height: 100dvh;
            background:
                linear-gradient(180deg,
                    var(--dark) 0px,
                    var(--dark) 285px,
                    var(--body) 285px,
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
    </style>
</head>

<body>

    <div class="app-shell mx-auto max-w-[430px] pb-10">

        <header class="top-header relative overflow-hidden rounded-b-[2rem] px-5 pb-6 text-white shadow-2xl">

            <div class="relative flex items-start justify-between">

                <a
                    href="clientes.php"
                    class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95">
                    ‹
                </a>

                <div class="text-center">
                    <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                        Cliente
                    </p>

                    <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                        Detalle
                    </h1>
                </div>

                <?php if (!empty($cliente['telefono'])): ?>
                    <a
                        href="https://wa.me/51<?php echo preg_replace('/[^0-9]/', '', $cliente['telefono']); ?>"
                        target="_blank"
                        class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-emerald-500 text-xl shadow-xl active:scale-95">
                        ☎
                    </a>
                <?php else: ?>
                    <a
                        href="logout.php"
                        class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95">
                        ⏻
                    </a>
                <?php endif; ?>

            </div>

            <section class="mt-6 rounded-[1.7rem] border border-white/10 bg-white/10 p-4 shadow-2xl backdrop-blur-xl">

                <p class="text-xs font-black uppercase tracking-[.18em] text-slate-400">
                    Cliente
                </p>

                <h2 class="mt-1 text-2xl font-black tracking-[-.04em]">
                    <?php echo htmlspecialchars($cliente['nombre']); ?>
                </h2>

                <p class="mt-2 text-sm font-bold text-slate-300">
                    <?php echo htmlspecialchars($cliente['telefono'] ?: 'Sin celular'); ?>
                </p>

                <?php if (!empty($cliente['direccion'])): ?>
                    <p class="mt-1 text-xs font-semibold text-slate-400">
                        <?php echo htmlspecialchars($cliente['direccion']); ?>
                    </p>
                <?php endif; ?>

                <a
                    href="editar_cliente.php?id=<?php echo (int)$cliente['id']; ?>"
                    class="mt-4 block rounded-2xl bg-blue-600 px-4 py-3 text-center text-sm font-black text-white shadow-lg active:scale-95">
                    Editar cliente
                </a>

            </section>

            <section class="mt-3 grid grid-cols-3 gap-2">

                <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                    <p class="text-2xl font-black">
                        <?php echo count($vehiculos); ?>
                    </p>

                    <p class="mt-1 text-[11px] font-bold text-slate-400">
                        Autos
                    </p>
                </div>

                <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                    <p class="text-2xl font-black">
                        <?php echo count($ordenes); ?>
                    </p>

                    <p class="mt-1 text-[11px] font-bold text-slate-400">
                        Órdenes
                    </p>
                </div>

                <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                    <p class="text-xl font-black text-red-200">
                        S/ <?php echo number_format($deudaTotal, 0); ?>
                    </p>

                    <p class="mt-1 text-[11px] font-bold text-slate-400">
                        Deuda
                    </p>
                </div>

            </section>

        </header>

        <main class="px-5 pt-5">

            <section>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-xl font-black tracking-[-.04em]">
                        Vehículos
                    </h2>

                    <a
                        href="registrar_vehiculo.php"
                        class="rounded-full bg-blue-600 px-4 py-2 text-xs font-black text-white">
                        + Auto
                    </a>
                </div>

                <?php if (count($vehiculos) > 0): ?>

                    <div class="space-y-3">
                        <?php foreach ($vehiculos as $vehiculo): ?>

                            <article class="soft-card rounded-[1.5rem] p-4 ring-1 ring-slate-200/70">

                                <div class="flex items-start justify-between gap-3">

                                    <div>
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-black text-blue-700">
                                            <?php echo htmlspecialchars($vehiculo['placa']); ?>
                                        </span>

                                        <h3 class="mt-3 text-lg font-black">
                                            <?php echo htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']); ?>
                                        </h3>

                                        <p class="mt-1 text-sm font-bold text-slate-500">
                                            <?php echo htmlspecialchars(($vehiculo['anio'] ?? '') . ' · ' . ($vehiculo['color'] ?? '')); ?>
                                        </p>
                                    </div>

                                    <a
                                        href="vehiculos.php?placa=<?php echo urlencode($vehiculo['placa']); ?>"
                                        class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-black text-white">
                                        Ver
                                    </a>

                                </div>

                            </article>

                        <?php endforeach; ?>
                    </div>

                <?php else: ?>

                    <div class="rounded-[1.7rem] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                        <p class="text-sm font-bold text-slate-500">
                            Este cliente aún no tiene vehículos registrados.
                        </p>
                    </div>

                <?php endif; ?>
            </section>

            <section class="mt-6">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-xl font-black tracking-[-.04em]">
                        Órdenes recientes
                    </h2>

                    <a href="ordenes.php" class="text-xs font-black text-blue-600">
                        Ver todas
                    </a>
                </div>

                <?php if (count($ordenes) > 0): ?>

                    <div class="space-y-3">
                        <?php foreach ($ordenes as $orden): ?>

                            <article class="soft-card rounded-[1.5rem] p-4 ring-1 ring-slate-200/70">

                                <div class="flex items-start justify-between gap-3">

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap gap-2">
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-black text-slate-700">
                                                <?php echo htmlspecialchars($orden['codigo'] ?: ('ORD-' . $orden['id'])); ?>
                                            </span>

                                            <?php if ((float)$orden['saldo'] > 0): ?>
                                                <span class="rounded-full bg-red-50 px-3 py-1 text-[11px] font-black text-red-700">
                                                    Debe S/ <?php echo number_format((float)$orden['saldo'], 2); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <h3 class="mt-3 text-base font-black">
                                            <?php echo htmlspecialchars($orden['descripcion']); ?>
                                        </h3>

                                        <p class="mt-1 text-xs font-bold text-slate-500">
                                            <?php echo htmlspecialchars($orden['placa']); ?>
                                            · <?php echo date('d/m/Y', strtotime($orden['fecha_registro'])); ?>
                                        </p>
                                    </div>

                                    <p class="shrink-0 text-lg font-black">
                                        S/ <?php echo number_format((float)$orden['total'], 2); ?>
                                    </p>

                                </div>

                                <div class="mt-4 border-t border-slate-100 pt-3">
                                    <a
                                        href="detalle_orden.php?id=<?php echo (int)$orden['id']; ?>"
                                        class="block rounded-2xl bg-slate-950 px-4 py-3 text-center text-sm font-black text-white">
                                        Ver orden
                                    </a>
                                </div>

                            </article>

                        <?php endforeach; ?>
                    </div>

                <?php else: ?>

                    <div class="rounded-[1.7rem] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                        <p class="text-sm font-bold text-slate-500">
                            Este cliente aún no tiene órdenes.
                        </p>
                    </div>

                <?php endif; ?>
            </section>

            <section class="mt-6">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-xl font-black tracking-[-.04em]">
                        Cotizaciones
                    </h2>

                    <a href="cotizaciones.php" class="text-xs font-black text-blue-600">
                        Ver todas
                    </a>
                </div>

                <?php if (count($cotizaciones) > 0): ?>

                    <div class="space-y-3">
                        <?php foreach ($cotizaciones as $cot): ?>

                            <article class="soft-card rounded-[1.5rem] p-4 ring-1 ring-slate-200/70">

                                <div class="flex items-start justify-between gap-3">

                                    <div>
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-black text-blue-700">
                                            <?php echo htmlspecialchars($cot['estado']); ?>
                                        </span>

                                        <h3 class="mt-3 text-base font-black">
                                            <?php echo htmlspecialchars($cot['codigo']); ?>
                                        </h3>

                                        <p class="mt-1 text-xs font-bold text-slate-500">
                                            <?php echo htmlspecialchars($cot['placa']); ?>
                                            · <?php echo date('d/m/Y', strtotime($cot['created_at'])); ?>
                                        </p>
                                    </div>

                                    <p class="shrink-0 text-lg font-black">
                                        S/ <?php echo number_format((float)$cot['total'], 2); ?>
                                    </p>

                                </div>

                                <div class="mt-4 border-t border-slate-100 pt-3">
                                    <a
                                        href="detalle_cotizacion.php?id=<?php echo (int)$cot['id']; ?>"
                                        class="block rounded-2xl bg-blue-600 px-4 py-3 text-center text-sm font-black text-white">
                                        Ver cotización
                                    </a>
                                </div>

                            </article>

                        <?php endforeach; ?>
                    </div>

                <?php else: ?>

                    <div class="rounded-[1.7rem] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                        <p class="text-sm font-bold text-slate-500">
                            Este cliente aún no tiene cotizaciones.
                        </p>
                    </div>

                <?php endif; ?>
            </section>

        </main>

    </div>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js');
        }
    </script>

</body>

</html>
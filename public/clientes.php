<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrClientes.php';

$clientes = CtrClientes::listar();
$resumen = CtrClientes::resumen();

$buscar = trim($_GET['buscar'] ?? '');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover"
    >

    <title>Clientes - TallerPro</title>

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
                linear-gradient(
                    180deg,
                    var(--dark) 0px,
                    var(--dark) 285px,
                    var(--body) 285px,
                    var(--body) 100%
                );
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
                linear-gradient(
                    180deg,
                    var(--dark) 0px,
                    var(--dark) 285px,
                    var(--body) 285px,
                    var(--body) 100%
                );
        }

        .top-header {
            padding-top: calc(22px + env(safe-area-inset-top));
            background:
                radial-gradient(circle at 88% 8%, rgba(8, 145, 178, .35), transparent 30%),
                radial-gradient(circle at 15% 100%, rgba(37, 99, 235, .20), transparent 32%),
                linear-gradient(145deg, #020617 0%, #061426 52%, #020617 100%);
        }

        .soft-card {
            background: rgba(255,255,255,.96);
            box-shadow:
                0 18px 36px rgba(15, 23, 42, .08),
                inset 0 1px 0 rgba(255,255,255,.9);
        }

        .bottom-safe {
            padding-bottom: calc(10px + env(safe-area-inset-bottom));
        }

        .bottom-bar {
            width: min(92%, 390px);
            height: 72px;
            margin: 0 auto;
            background:
                radial-gradient(circle at 50% -10%, rgba(37,99,235,.28), transparent 38%),
                linear-gradient(145deg, #020617 0%, #081426 52%, #020617 100%);
            border: 1px solid rgba(255,255,255,.10);
            box-shadow:
                0 18px 45px rgba(2, 6, 23, .42),
                inset 0 1px 0 rgba(255,255,255,.08);
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
            color: rgba(226,232,240,.72);
        }

        .nav-item.active {
            color: #60a5fa;
        }

        .fab {
            width: 58px;
            height: 58px;
            background:
                radial-gradient(circle at 35% 25%, rgba(255,255,255,.35), transparent 30%),
                linear-gradient(145deg, #3b82f6 0%, #1d4ed8 100%);
            box-shadow:
                0 16px 34px rgba(37, 99, 235, .50),
                inset 0 1px 0 rgba(255,255,255,.28);
        }
    </style>
</head>

<body>

<div class="app-shell mx-auto max-w-[430px] pb-28">

    <header class="top-header relative overflow-hidden rounded-b-[2rem] px-5 pb-6 text-white shadow-2xl">

        <div class="relative flex items-start justify-between">

            <a
                href="dashboard.php"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95"
            >
                ‹
            </a>

            <div class="text-center">
                <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                    Taller
                </p>

                <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                    Clientes
                </h1>
            </div>

            <a
                href="registrar_vehiculo.php"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-blue-600 text-xl shadow-xl active:scale-95"
            >
                +
            </a>

        </div>

        <section class="mt-6 grid grid-cols-3 gap-2">

            <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                <p class="text-2xl font-black">
                    <?php echo (int)($resumen['total_clientes'] ?? 0); ?>
                </p>

                <p class="mt-1 text-[11px] font-bold text-slate-400">
                    Clientes
                </p>
            </div>

            <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                <p class="text-2xl font-black text-emerald-200">
                    <?php echo (int)($resumen['con_whatsapp'] ?? 0); ?>
                </p>

                <p class="mt-1 text-[11px] font-bold text-slate-400">
                    WhatsApp
                </p>
            </div>

            <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                <p class="text-xl font-black text-red-200">
                    S/ <?php echo number_format((float)($resumen['deuda_total'] ?? 0), 0); ?>
                </p>

                <p class="mt-1 text-[11px] font-bold text-slate-400">
                    Deuda
                </p>
            </div>

        </section>

    </header>

    <main class="px-5 pt-5">

        <form method="GET" class="soft-card rounded-[1.6rem] p-4 ring-1 ring-slate-200/70">

            <label class="mb-2 block text-xs font-black uppercase tracking-[.16em] text-slate-400">
                Buscar cliente
            </label>

            <div class="flex gap-2">
                <input
                    type="text"
                    name="buscar"
                    value="<?php echo htmlspecialchars($buscar); ?>"
                    placeholder="Nombre, celular o dirección..."
                    class="min-w-0 flex-1 rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-4 font-bold outline-none focus:border-blue-600 focus:bg-white"
                >

                <button
                    type="submit"
                    class="rounded-[1.2rem] bg-blue-600 px-5 py-4 font-black text-white shadow-lg active:scale-95"
                >
                    Buscar
                </button>
            </div>

        </form>

        <section class="mt-5 space-y-3">

            <?php if (count($clientes) > 0): ?>

                <?php foreach ($clientes as $cliente): ?>

                    <article class="soft-card rounded-[1.5rem] p-4 ring-1 ring-slate-200/70">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-black text-blue-700">
                                        <?php echo (int)$cliente['total_vehiculos']; ?> vehículo(s)
                                    </span>

                                    <?php if ((float)$cliente['deuda_total'] > 0): ?>
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-[11px] font-black text-red-700">
                                            Debe S/ <?php echo number_format((float)$cliente['deuda_total'], 2); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h2 class="mt-3 text-lg font-black leading-tight tracking-[-.03em]">
                                    <?php echo htmlspecialchars($cliente['nombre']); ?>
                                </h2>

                                <p class="mt-1 text-sm font-bold text-slate-500">
                                    <?php echo htmlspecialchars($cliente['telefono'] ?: 'Sin celular'); ?>
                                </p>

                                <?php if (!empty($cliente['direccion'])): ?>
                                    <p class="mt-1 text-xs font-bold text-slate-400">
                                        <?php echo htmlspecialchars($cliente['direccion']); ?>
                                    </p>
                                <?php endif; ?>

                            </div>

                            <?php if (!empty($cliente['telefono'])): ?>
                                <a
                                    href="https://wa.me/51<?php echo preg_replace('/[^0-9]/', '', $cliente['telefono']); ?>"
                                    target="_blank"
                                    class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-emerald-500 text-xl text-white shadow-lg active:scale-95"
                                >
                                    ☎
                                </a>
                            <?php endif; ?>

                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-100 pt-4">

                            <a
                                href="detalle_cliente.php?id=<?php echo (int)$cliente['id']; ?>"
                                class="rounded-2xl bg-slate-950 px-4 py-3 text-center text-sm font-black text-white active:scale-95"
                            >
                                Ver detalle
                            </a>

                            <a
                                href="registrar_vehiculo.php"
                                class="rounded-2xl bg-blue-600 px-4 py-3 text-center text-sm font-black text-white active:scale-95"
                            >
                                Nuevo auto
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="rounded-[1.7rem] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-3xl bg-slate-100 text-3xl">
                        👤
                    </div>

                    <h3 class="mt-3 font-black">
                        Sin clientes
                    </h3>

                    <p class="mx-auto mt-1 max-w-[260px] text-sm font-semibold text-slate-500">
                        Registra un cliente junto con su vehículo desde la pantalla de autos.
                    </p>

                    <a
                        href="registrar_vehiculo.php"
                        class="mt-5 block rounded-2xl bg-blue-600 px-4 py-4 text-sm font-black text-white shadow-lg"
                    >
                        + Registrar cliente
                    </a>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <div class="fixed inset-x-0 bottom-0 z-50 bottom-safe">
        <nav class="bottom-bar relative rounded-[1.65rem] px-3 pb-2 pt-2 text-white">

            <a
                href="registrar_vehiculo.php"
                class="fab absolute left-1/2 top-0 grid -translate-x-1/2 -translate-y-7 place-items-center rounded-full text-4xl font-light text-white ring-[8px] ring-[#eef3f8] active:scale-95"
            >
                +
            </a>

            <div class="grid grid-cols-5 items-end text-center">

                <a href="dashboard.php" class="nav-item">
                    <div class="text-[1.35rem] leading-none">⌂</div>
                    <p>Inicio</p>
                </a>

                <a href="vehiculos.php" class="nav-item">
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
</script>

</body>
</html>
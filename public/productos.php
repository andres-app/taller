<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrProductos.php';

$productos = CtrProductos::listar();
$categorias = CtrProductos::categorias();
$resumen = CtrProductos::resumen();

$buscar = trim($_GET['buscar'] ?? '');
$categoriaActual = trim($_GET['categoria'] ?? '');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover"
    >

    <title>Inventario - TallerPro</title>

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
                    var(--dark) 260px,
                    var(--body) 260px,
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
                    var(--dark) 260px,
                    var(--body) 260px,
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
                    Inventario
                </h1>
            </div>

            <a
                href="registrar_producto.php"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-blue-600 text-2xl shadow-xl active:scale-95"
            >
                +
            </a>

        </div>

        <section class="mt-6 grid grid-cols-3 gap-2">

            <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                <p class="text-2xl font-black">
                    <?php echo (int) ($resumen['total_productos'] ?? 0); ?>
                </p>

                <p class="mt-1 text-[11px] font-bold text-slate-400">
                    Productos
                </p>
            </div>

            <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                <p class="text-2xl font-black text-amber-300">
                    <?php echo (int) ($resumen['bajo_stock'] ?? 0); ?>
                </p>

                <p class="mt-1 text-[11px] font-bold text-slate-400">
                    Bajo stock
                </p>
            </div>

            <div class="rounded-[1.35rem] border border-white/10 bg-white/10 p-3 text-center">
                <p class="text-xl font-black">
                    S/ <?php echo number_format((float) ($resumen['valorizado_costo'] ?? 0), 0); ?>
                </p>

                <p class="mt-1 text-[11px] font-bold text-slate-400">
                    Costo
                </p>
            </div>

        </section>

    </header>

    <main class="px-5 pt-5">

        <?php if (isset($_GET['ok'])): ?>
            <div class="mb-4 rounded-[1.2rem] border border-emerald-100 bg-emerald-50 p-4 text-sm font-black text-emerald-700">
                Producto registrado correctamente.
            </div>
        <?php endif; ?>

        <form method="GET" class="soft-card rounded-[1.6rem] p-4 ring-1 ring-slate-200/70">

            <label class="mb-2 block text-xs font-black uppercase tracking-[.16em] text-slate-400">
                Buscar producto
            </label>

            <input
                type="text"
                name="buscar"
                value="<?php echo htmlspecialchars($buscar); ?>"
                placeholder="Aceite, filtro, freno..."
                class="w-full rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-4 font-bold outline-none focus:border-blue-600 focus:bg-white"
            >

            <div class="mt-3 flex gap-2">

                <select
                    name="categoria"
                    class="min-w-0 flex-1 rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-4 font-bold outline-none focus:border-blue-600 focus:bg-white"
                >
                    <option value="">Todas</option>

                    <?php foreach ($categorias as $cat): ?>
                        <option
                            value="<?php echo htmlspecialchars($cat['categoria']); ?>"
                            <?php echo $categoriaActual === $cat['categoria'] ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($cat['categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button
                    type="submit"
                    class="rounded-[1.2rem] bg-blue-600 px-5 py-4 font-black text-white shadow-lg active:scale-95"
                >
                    Buscar
                </button>

            </div>

        </form>

        <section class="mt-5 space-y-3">

            <?php if (count($productos) > 0): ?>

                <?php foreach ($productos as $producto): ?>

                    <?php
                    $bajoStock = ((float)$producto['stock_actual'] <= (float)$producto['stock_minimo']);
                    ?>

                    <article class="soft-card rounded-[1.5rem] p-4 ring-1 ring-slate-200/70">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-black text-blue-700">
                                        <?php echo htmlspecialchars($producto['categoria']); ?>
                                    </span>

                                    <?php if ($bajoStock): ?>
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-[11px] font-black text-amber-700">
                                            Bajo stock
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h2 class="mt-3 text-lg font-black leading-tight tracking-[-.03em]">
                                    <?php echo htmlspecialchars($producto['nombre']); ?>
                                </h2>

                                <p class="mt-1 text-xs font-bold text-slate-500">
                                    Unidad: <?php echo htmlspecialchars($producto['unidad']); ?>
                                </p>
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="text-xs font-black uppercase tracking-[.14em] text-slate-400">
                                    Stock
                                </p>

                                <p class="mt-1 text-2xl font-black <?php echo $bajoStock ? 'text-amber-600' : 'text-slate-950'; ?>">
                                    <?php echo rtrim(rtrim(number_format((float)$producto['stock_actual'], 2), '0'), '.'); ?>
                                </p>
                            </div>

                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4">

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] font-black uppercase tracking-[.12em] text-slate-400">
                                    Mínimo
                                </p>

                                <p class="mt-1 font-black">
                                    <?php echo rtrim(rtrim(number_format((float)$producto['stock_minimo'], 2), '0'), '.'); ?>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] font-black uppercase tracking-[.12em] text-slate-400">
                                    Costo
                                </p>

                                <p class="mt-1 font-black">
                                    S/ <?php echo number_format((float)$producto['costo'], 2); ?>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] font-black uppercase tracking-[.12em] text-slate-400">
                                    Venta
                                </p>

                                <p class="mt-1 font-black">
                                    S/ <?php echo number_format((float)$producto['precio_venta'], 2); ?>
                                </p>
                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="rounded-[1.7rem] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-3xl bg-slate-100 text-3xl">
                        📦
                    </div>

                    <h3 class="mt-3 font-black">
                        Sin productos
                    </h3>

                    <p class="mx-auto mt-1 max-w-[260px] text-sm font-semibold text-slate-500">
                        Registra aceites, filtros o repuestos para usarlos en cotizaciones y órdenes.
                    </p>

                    <a
                        href="registrar_producto.php"
                        class="mt-5 block rounded-2xl bg-blue-600 px-4 py-4 text-sm font-black text-white shadow-lg"
                    >
                        + Registrar producto
                    </a>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <div class="fixed inset-x-0 bottom-0 z-50 bottom-safe">
        <nav class="bottom-bar relative rounded-[1.65rem] px-3 pb-2 pt-2 text-white">

            <a
                href="registrar_producto.php"
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

                <a href="registrar_producto.php" class="nav-item pt-8">
                    <p>Nuevo</p>
                </a>

                <a href="#" class="nav-item">
                    <div class="text-[1.20rem] leading-none">🧾</div>
                    <p>Órdenes</p>
                </a>

                <a href="productos.php" class="nav-item active">
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
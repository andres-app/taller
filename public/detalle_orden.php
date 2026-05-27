<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrOrdenes.php';

$datos = CtrOrdenes::detalle();

$orden = $datos['orden'];
$detalle = $datos['detalle'];

if (!$orden) {
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
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover"
    >

    <title>Detalle orden - TallerPro</title>

    <meta name="theme-color" content="#020617">
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
    </style>
</head>

<body>

<div class="app-shell mx-auto max-w-[430px] pb-10">

    <header class="top-header relative overflow-hidden rounded-b-[2rem] px-5 pb-6 text-white shadow-2xl">

        <div class="relative flex items-start justify-between">

            <a
                href="vehiculos.php?placa=<?php echo urlencode($orden['placa']); ?>"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95"
            >
                ‹
            </a>

            <div class="text-center">
                <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                    Orden
                </p>

                <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                    <?php echo htmlspecialchars($orden['codigo'] ?: ('ORD-' . $orden['id'])); ?>
                </h1>
            </div>

            <a
                href="logout.php"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95"
            >
                ⏻
            </a>

        </div>

        <section class="mt-6 rounded-[1.7rem] border border-white/10 bg-white/10 p-4 shadow-2xl backdrop-blur-xl">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-xs font-black uppercase tracking-[.18em] text-slate-400">
                        Total orden
                    </p>

                    <p class="mt-1 text-4xl font-black tracking-[-.06em]">
                        S/ <?php echo number_format((float)$orden['total'], 2); ?>
                    </p>
                </div>

                <span class="rounded-full bg-amber-500/20 px-3 py-2 text-xs font-black text-amber-200">
                    <?php echo htmlspecialchars($orden['estado']); ?>
                </span>

            </div>

            <p class="mt-3 text-xs font-semibold text-slate-400">
                Origen: <?php echo htmlspecialchars($orden['origen'] ?: 'DIRECTA'); ?>
            </p>

        </section>

    </header>

    <main class="px-5 pt-5">

        <section class="soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">

            <p class="text-xs font-black uppercase tracking-[.16em] text-slate-400">
                Cliente y vehículo
            </p>

            <h2 class="mt-2 text-2xl font-black tracking-[-.04em]">
                <?php echo htmlspecialchars($orden['cliente']); ?>
            </h2>

            <p class="mt-1 text-sm font-bold text-slate-500">
                <?php echo htmlspecialchars($orden['placa'] . ' · ' . $orden['marca'] . ' ' . $orden['modelo']); ?>
            </p>

        </section>

        <section class="mt-5 space-y-3">

            <h2 class="text-xl font-black tracking-[-.04em]">
                Detalle de trabajo
            </h2>

            <?php foreach ($detalle as $item): ?>

                <?php
                $badge = 'bg-slate-100 text-slate-700';
                if ($item['tipo_item'] === 'STOCK') {
                    $badge = 'bg-blue-50 text-blue-700';
                }
                if ($item['tipo_item'] === 'EXTERNO') {
                    $badge = 'bg-amber-50 text-amber-700';
                }
                ?>

                <article class="soft-card rounded-[1.5rem] p-4 ring-1 ring-slate-200/70">

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">
                            <span class="rounded-full <?php echo $badge; ?> px-3 py-1 text-[11px] font-black">
                                <?php echo htmlspecialchars($item['tipo_item']); ?>
                            </span>

                            <h3 class="mt-3 text-base font-black leading-tight">
                                <?php echo htmlspecialchars($item['descripcion']); ?>
                            </h3>

                            <p class="mt-1 text-xs font-bold text-slate-500">
                                Cantidad: <?php echo rtrim(rtrim(number_format((float)$item['cantidad'], 2), '0'), '.'); ?>
                                · Precio: S/ <?php echo number_format((float)$item['precio_unitario'], 2); ?>
                            </p>
                        </div>

                        <p class="shrink-0 text-lg font-black">
                            S/ <?php echo number_format((float)$item['subtotal'], 2); ?>
                        </p>

                    </div>

                </article>

            <?php endforeach; ?>

        </section>

        <?php if (!empty($orden['telefono'])): ?>

            <section class="mt-6">
                <a
                    href="https://wa.me/51<?php echo preg_replace('/[^0-9]/', '', $orden['telefono']); ?>?text=<?php echo urlencode('Hola, su orden ' . ($orden['codigo'] ?: ('ORD-' . $orden['id'])) . ' fue registrada por S/ ' . number_format((float)$orden['total'], 2)); ?>"
                    target="_blank"
                    class="block rounded-[1.5rem] bg-emerald-500 px-5 py-4 text-center text-base font-black text-white shadow-xl shadow-emerald-500/20 active:scale-[.98]"
                >
                    Avisar por WhatsApp
                </a>
            </section>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
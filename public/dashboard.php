<?php
session_start();

if(!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$empresa = $_SESSION['empresa'] ?? 'TallerPro';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

    <title>TallerPro</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0f172a">

    <style>
        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            overflow-x: hidden;
            overscroll-behavior: none;
            -webkit-text-size-adjust: 100%;
            touch-action: manipulation;
            background: #eef1f5;
        }

        input,
        select,
        textarea,
        button {
            font-size: 16px;
        }

        * {
            -webkit-tap-highlight-color: transparent;
        }

        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }

        .glass {
            background: rgba(255,255,255,.82);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }
    </style>
</head>

<body class="text-slate-950">

<div class="mx-auto min-h-screen max-w-[430px] bg-[#eef1f5] pb-32">

    <header class="relative overflow-hidden rounded-b-[2.2rem] bg-slate-950 px-5 pt-7 pb-6 text-white shadow-2xl">
        <div class="absolute -right-20 -top-24 h-56 w-56 rounded-full bg-cyan-400/20 blur-3xl"></div>
        <div class="absolute -left-20 bottom-0 h-44 w-44 rounded-full bg-emerald-400/10 blur-3xl"></div>

        <div class="relative flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.22em] text-slate-400">
                    Taller activo
                </p>

                <h1 class="mt-1 max-w-[260px] truncate text-2xl font-black leading-tight">
                    <?php echo htmlspecialchars($empresa); ?>
                </h1>
            </div>

            <a href="logout.php" class="grid h-11 w-11 place-items-center rounded-2xl bg-white/10 text-lg font-black">
                ⏻
            </a>
        </div>

        <div class="relative mt-6 rounded-[1.7rem] border border-white/10 bg-white/10 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-300">
                        Caja de hoy
                    </p>

                    <p class="mt-1 text-4xl font-black tracking-tight">
                        S/ 0.00
                    </p>
                </div>

                <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-200">
                    Abierto
                </span>
            </div>

            <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                <div class="rounded-2xl bg-white/10 px-2 py-3">
                    <p class="text-lg font-black">0</p>
                    <p class="text-[11px] font-semibold text-slate-400">Órdenes</p>
                </div>

                <div class="rounded-2xl bg-white/10 px-2 py-3">
                    <p class="text-lg font-black">0</p>
                    <p class="text-[11px] font-semibold text-slate-400">Pendientes</p>
                </div>

                <div class="rounded-2xl bg-white/10 px-2 py-3">
                    <p class="text-lg font-black">0</p>
                    <p class="text-[11px] font-semibold text-slate-400">Fiados</p>
                </div>
            </div>
        </div>
    </header>

    <main class="px-5 pt-5">

        <section class="grid grid-cols-2 gap-3">
            <a href="#" class="rounded-[1.6rem] bg-white p-5 shadow-sm ring-1 ring-slate-200/70 active:scale-[.98]">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 text-2xl">👤</div>
                <h2 class="mt-4 text-base font-black">Clientes</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">Agenda y WhatsApp</p>
            </a>

            <a href="#" class="rounded-[1.6rem] bg-white p-5 shadow-sm ring-1 ring-slate-200/70 active:scale-[.98]">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-50 text-2xl">🚗</div>
                <h2 class="mt-4 text-base font-black">Vehículos</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">Buscar por placa</p>
            </a>

            <a href="#" class="rounded-[1.6rem] bg-white p-5 shadow-sm ring-1 ring-slate-200/70 active:scale-[.98]">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-violet-50 text-2xl">🧾</div>
                <h2 class="mt-4 text-base font-black">Órdenes</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">Trabajos activos</p>
            </a>

            <a href="#" class="rounded-[1.6rem] bg-white p-5 shadow-sm ring-1 ring-slate-200/70 active:scale-[.98]">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-2xl">💰</div>
                <h2 class="mt-4 text-base font-black">Deudas</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">Fiados y pagos</p>
            </a>
        </section>

        <section class="mt-6">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-black">Acciones rápidas</h2>
                <span class="text-xs font-bold text-slate-500">MVP</span>
            </div>

            <div class="space-y-3">
                <button class="flex w-full items-center justify-between rounded-[1.5rem] bg-white p-4 text-left shadow-sm ring-1 ring-slate-200/70 active:scale-[.99]">
                    <div class="flex items-center gap-3">
                        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-slate-950 text-white">🔍</div>
                        <div>
                            <p class="font-black">Buscar vehículo</p>
                            <p class="text-xs font-semibold text-slate-500">Ingresa placa del cliente</p>
                        </div>
                    </div>
                    <span class="text-xl text-slate-400">›</span>
                </button>

                <button class="flex w-full items-center justify-between rounded-[1.5rem] bg-white p-4 text-left shadow-sm ring-1 ring-slate-200/70 active:scale-[.99]">
                    <div class="flex items-center gap-3">
                        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-slate-950 text-white">📲</div>
                        <div>
                            <p class="font-black">Enviar aviso WhatsApp</p>
                            <p class="text-xs font-semibold text-slate-500">Recordatorio manual</p>
                        </div>
                    </div>
                    <span class="text-xl text-slate-400">›</span>
                </button>
            </div>
        </section>

        <section class="mt-6">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-black">Trabajos recientes</h2>
                <span class="text-xs font-bold text-slate-500">Hoy</span>
            </div>

            <div class="rounded-[1.8rem] border border-dashed border-slate-300 bg-white/70 p-6 text-center">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-3xl bg-slate-100 text-3xl">
                    🔧
                </div>

                <h3 class="mt-3 font-black">Sin trabajos aún</h3>

                <p class="mx-auto mt-1 max-w-[260px] text-sm font-medium text-slate-500">
                    Cuando registres una orden, aparecerá aquí como una app real de taller.
                </p>
            </div>
        </section>

    </main>

    <div class="fixed inset-x-0 bottom-0 z-50 safe-bottom">
        <div class="mx-auto max-w-[430px] px-4 pb-3">
            <div class="glass relative rounded-[2rem] border border-white/80 px-3 py-2 shadow-2xl">

                <a href="#" class="absolute left-1/2 top-0 grid h-16 w-16 -translate-x-1/2 -translate-y-7 place-items-center rounded-full bg-slate-950 text-3xl text-white shadow-2xl ring-8 ring-[#eef1f5] active:scale-95">
                    +
                </a>

                <div class="grid grid-cols-5 items-end text-center">
                    <a href="#" class="rounded-2xl px-2 py-2 text-slate-950">
                        <div class="text-xl">⌂</div>
                        <p class="mt-1 text-[10px] font-black">Inicio</p>
                    </a>

                    <a href="#" class="rounded-2xl px-2 py-2 text-slate-400">
                        <div class="text-xl">🚗</div>
                        <p class="mt-1 text-[10px] font-black">Autos</p>
                    </a>

                    <div></div>

                    <a href="#" class="rounded-2xl px-2 py-2 text-slate-400">
                        <div class="text-xl">🧾</div>
                        <p class="mt-1 text-[10px] font-black">Orden</p>
                    </a>

                    <a href="#" class="rounded-2xl px-2 py-2 text-slate-400">
                        <div class="text-xl">☰</div>
                        <p class="mt-1 text-[10px] font-black">Más</p>
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js');
}
</script>

</body>
</html>
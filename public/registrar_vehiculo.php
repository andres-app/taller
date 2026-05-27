<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrRegistroVehiculo.php';

$respuesta = CtrRegistroVehiculo::guardar();

$placaPrevia = strtoupper(trim($_GET['placa'] ?? ''));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover"
    >

    <title>Registrar vehículo - TallerPro</title>

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
                linear-gradient(
                    180deg,
                    var(--dark) 0px,
                    var(--dark) 210px,
                    var(--body) 210px,
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
                    var(--dark) 210px,
                    var(--body) 210px,
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

        .field-label {
            display: block;
            margin-bottom: 7px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #64748b;
        }

        .field {
            width: 100%;
            border-radius: 1.15rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 14px 15px;
            font-weight: 800;
            color: #020617;
            outline: none;
        }

        .field:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
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
                href="vehiculos.php"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95"
            >
                ‹
            </a>

            <div class="text-center">
                <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                    Nuevo
                </p>

                <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                    Cliente y auto
                </h1>
            </div>

            <a
                href="logout.php"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95"
            >
                ⏻
            </a>

        </div>

        <div class="mt-6 rounded-[1.7rem] border border-white/10 bg-white/10 p-4 shadow-2xl backdrop-blur-xl">
            <p class="text-sm font-bold text-slate-300">
                Registro rápido
            </p>

            <p class="mt-1 text-xs font-semibold leading-5 text-slate-400">
                Guarda al cliente y su vehículo en una sola pantalla para crear órdenes después.
            </p>
        </div>

    </header>

    <main class="px-5 pt-5">

        <?php if ($respuesta['ok'] === false): ?>
            <div class="mb-4 rounded-[1.2rem] border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
                <?php echo htmlspecialchars($respuesta['mensaje']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <section class="soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">

                <div class="mb-5 flex items-center gap-3">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 text-2xl">
                        👤
                    </div>

                    <div>
                        <h2 class="text-xl font-black tracking-[-.04em]">
                            Datos del cliente
                        </h2>

                        <p class="text-sm font-semibold text-slate-500">
                            Información básica de contacto
                        </p>
                    </div>
                </div>

                <div class="space-y-4">

                    <div>
                        <label class="field-label">Nombre completo *</label>
                        <input
                            type="text"
                            name="nombre"
                            required
                            class="field"
                            placeholder="Ej. Juan Pérez"
                        >
                    </div>

                    <div>
                        <label class="field-label">Celular / WhatsApp</label>
                        <input
                            type="tel"
                            name="telefono"
                            inputmode="numeric"
                            class="field"
                            placeholder="Ej. 999888777"
                        >
                    </div>

                    <div>
                        <label class="field-label">Dirección / referencia</label>
                        <input
                            type="text"
                            name="direccion"
                            class="field"
                            placeholder="Ej. SJL, mercado central"
                        >
                    </div>

                </div>

            </section>

            <section class="soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">

                <div class="mb-5 flex items-center gap-3">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-50 text-2xl">
                        🚗
                    </div>

                    <div>
                        <h2 class="text-xl font-black tracking-[-.04em]">
                            Datos del vehículo
                        </h2>

                        <p class="text-sm font-semibold text-slate-500">
                            Placa e información técnica
                        </p>
                    </div>
                </div>

                <div class="space-y-4">

                    <div>
                        <label class="field-label">Placa *</label>
                        <input
                            type="text"
                            name="placa"
                            required
                            maxlength="12"
                            value="<?php echo htmlspecialchars($placaPrevia); ?>"
                            class="field text-center text-2xl uppercase tracking-[.18em]"
                            placeholder="ABC123"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="field-label">Marca</label>
                            <input
                                type="text"
                                name="marca"
                                class="field"
                                placeholder="Toyota"
                            >
                        </div>

                        <div>
                            <label class="field-label">Modelo</label>
                            <input
                                type="text"
                                name="modelo"
                                class="field"
                                placeholder="Yaris"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="field-label">Año</label>
                            <input
                                type="text"
                                name="anio"
                                inputmode="numeric"
                                maxlength="4"
                                class="field"
                                placeholder="2018"
                            >
                        </div>

                        <div>
                            <label class="field-label">Color</label>
                            <input
                                type="text"
                                name="color"
                                class="field"
                                placeholder="Blanco"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="field-label">Kilometraje</label>
                        <input
                            type="text"
                            name="kilometraje"
                            inputmode="numeric"
                            class="field"
                            placeholder="Ej. 85000"
                        >
                    </div>

                    <div>
                        <label class="field-label">Observaciones</label>
                        <textarea
                            name="observaciones"
                            rows="3"
                            class="field resize-none"
                            placeholder="Ej. Cliente frecuente, revisar frenos..."
                        ></textarea>
                    </div>

                </div>

            </section>

            <button
                type="submit"
                class="w-full rounded-[1.5rem] bg-blue-600 px-5 py-4 text-base font-black text-white shadow-xl shadow-blue-600/25 active:scale-[.98]"
            >
                Guardar cliente y vehículo
            </button>

        </form>

    </main>

    <div class="fixed inset-x-0 bottom-0 z-50 bottom-safe">
        <nav class="bottom-bar relative rounded-[1.65rem] px-3 pb-2 pt-2 text-white">

            <a
                href="registrar_vehiculo.php"
                class="fab absolute left-1/2 top-0 grid -translate-x-1/2 -translate-y-7 place-items-center rounded-full text-4xl font-light text-white ring-[8px] ring-[#eef3f8] active:scale-95"
                aria-label="Nueva orden"
            >
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
                    <p>Nueva</p>
                </a>

                <a href="#" class="nav-item">
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

    const placaInput = document.querySelector('input[name="placa"]');

    if (placaInput) {
        placaInput.addEventListener('input', function () {
            this.value = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .slice(0, 12);
        });
    }

    const phoneInput = document.querySelector('input[name="telefono"]');

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            this.value = this.value
                .replace(/[^0-9]/g, '')
                .slice(0, 12);
        });
    }
</script>

</body>
</html>
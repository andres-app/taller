<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrOrdenes.php';

$respuesta = CtrOrdenes::registrarPago();

$orden_id = (int)($_GET['orden_id'] ?? $_POST['orden_id'] ?? 0);

if ($orden_id <= 0) {
    header("Location: deudas.php");
    exit;
}

$datos = CtrOrdenes::detalle();

$orden = $datos['orden'];

if (!$orden) {
    header("Location: deudas.php");
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

    <title>Registrar pago - TallerPro</title>

    <meta name="theme-color" content="#020617">
    <meta name="background-color" content="#020617">

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
                    var(--dark) 250px,
                    var(--body) 250px,
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
                    var(--dark) 250px,
                    var(--body) 250px,
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
    </style>
</head>

<body>

<div class="app-shell mx-auto max-w-[430px] pb-10">

    <header class="top-header relative overflow-hidden rounded-b-[2rem] px-5 pb-6 text-white shadow-2xl">

        <div class="relative flex items-start justify-between">

            <a
                href="deudas.php"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95"
            >
                ‹
            </a>

            <div class="text-center">
                <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                    Abono
                </p>

                <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                    Registrar pago
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

            <p class="text-xs font-black uppercase tracking-[.18em] text-slate-400">
                Saldo pendiente
            </p>

            <p class="mt-1 text-4xl font-black tracking-[-.06em]">
                S/ <?php echo number_format((float)$orden['saldo'], 2); ?>
            </p>

            <p class="mt-2 text-sm font-bold text-slate-300">
                <?php echo htmlspecialchars($orden['cliente']); ?>
            </p>

            <p class="mt-1 text-xs font-semibold text-slate-400">
                <?php echo htmlspecialchars($orden['placa'] . ' · ' . $orden['marca'] . ' ' . $orden['modelo']); ?>
            </p>

        </section>

    </header>

    <main class="px-5 pt-5">

        <?php if ($respuesta['ok'] === false): ?>
            <div class="mb-4 rounded-[1.2rem] border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
                <?php echo htmlspecialchars($respuesta['mensaje']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">

            <input type="hidden" name="orden_id" value="<?php echo (int)$orden['id']; ?>">

            <div class="mb-5 flex items-center gap-3">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-2xl">
                    💵
                </div>

                <div>
                    <h2 class="text-xl font-black tracking-[-.04em]">
                        Nuevo abono
                    </h2>

                    <p class="text-sm font-semibold text-slate-500">
                        Registra el pago del cliente
                    </p>
                </div>
            </div>

            <div class="space-y-4">

                <div>
                    <label class="field-label">Monto a pagar *</label>
                    <input
                        type="number"
                        name="monto"
                        step="0.01"
                        min="0.01"
                        max="<?php echo htmlspecialchars($orden['saldo']); ?>"
                        required
                        class="field text-2xl"
                        placeholder="0.00"
                    >
                </div>

                <div>
                    <label class="field-label">Método</label>
                    <select name="metodo" class="field">
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="YAPE">Yape</option>
                        <option value="PLIN">Plin</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                        <option value="TARJETA">Tarjeta</option>
                    </select>
                </div>

                <div>
                    <label class="field-label">Observación</label>
                    <textarea
                        name="observacion"
                        rows="3"
                        class="field resize-none"
                        placeholder="Ej. Abono parcial, pago por Yape..."
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-[1.5rem] bg-blue-600 px-5 py-4 text-base font-black text-white shadow-xl shadow-blue-600/25 active:scale-[.98]"
                >
                    Guardar pago
                </button>

            </div>

        </form>

    </main>

</div>

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js');
}
</script>

</body>
</html>
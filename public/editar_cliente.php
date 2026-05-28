<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrClientes.php';

$respuesta = CtrClientes::actualizar();

$datos = CtrClientes::detalle();
$cliente = $datos['cliente'];

if (!$cliente) {
    header("Location: clientes.php");
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

    <title>Editar cliente - TallerPro</title>

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
                href="detalle_cliente.php?id=<?php echo (int)$cliente['id']; ?>"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95"
            >
                ‹
            </a>

            <div class="text-center">
                <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                    Cliente
                </p>

                <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                    Editar datos
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
                Editando
            </p>

            <h2 class="mt-1 text-2xl font-black tracking-[-.04em]">
                <?php echo htmlspecialchars($cliente['nombre']); ?>
            </h2>

            <p class="mt-2 text-sm font-bold text-slate-300">
                Corrige nombre, celular o dirección del cliente.
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

            <input type="hidden" name="cliente_id" value="<?php echo (int)$cliente['id']; ?>">

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
                        value="<?php echo htmlspecialchars($cliente['nombre']); ?>"
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
                        value="<?php echo htmlspecialchars($cliente['telefono']); ?>"
                        placeholder="Ej. 999888777"
                    >
                </div>

                <div>
                    <label class="field-label">Dirección / referencia</label>
                    <textarea
                        name="direccion"
                        rows="3"
                        class="field resize-none"
                        placeholder="Ej. Referencia del cliente"
                    ><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-[1.5rem] bg-blue-600 px-5 py-4 text-base font-black text-white shadow-xl shadow-blue-600/25 active:scale-[.98]"
                >
                    Guardar cambios
                </button>

            </div>

        </form>

    </main>

</div>

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js');
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
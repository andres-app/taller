<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../app/controllers/CtrUsuarios.php';

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard.php");
    exit;
}

CtrUsuarios::login();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

    <title>TallerPro</title>

    <meta name="theme-color" content="#020617">
    <meta name="mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TallerPro">

    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="./icons/icon-192.png">

    <script src="https://cdn.tailwindcss.com"></script>

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
            background: #020617;
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

        .safe-screen {
            min-height: 100vh;
            min-height: 100dvh;
            padding-top: env(safe-area-inset-top);
            padding-bottom: env(safe-area-inset-bottom);
        }

        .glass-card {
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>
</head>

<body class="text-slate-950">

    <main class="safe-screen relative mx-auto flex max-w-[430px] items-center justify-center overflow-hidden px-5 py-6">

        <div class="absolute inset-0 bg-[#020617]"></div>
        <div class="absolute -top-32 right-[-90px] h-80 w-80 rounded-full bg-blue-600/30 blur-3xl"></div>
        <div class="absolute bottom-[-120px] left-[-100px] h-80 w-80 rounded-full bg-emerald-400/20 blur-3xl"></div>
        <div class="absolute left-1/2 top-1/2 h-[520px] w-[520px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/5 blur-3xl"></div>

        <section class="relative w-full">

            <div class="mb-8 text-center text-white">
                <div class="mx-auto mb-5 grid h-20 w-20 place-items-center rounded-[1.8rem] bg-white/10 text-4xl shadow-2xl ring-1 ring-white/10">
                    🔧
                </div>

                <p class="text-xs font-black uppercase tracking-[.28em] text-blue-200/80">
                    PWA Taller Automotriz
                </p>

                <h1 class="mt-3 text-4xl font-black tracking-tight">
                    TallerPro
                </h1>

                <p class="mx-auto mt-3 max-w-[280px] text-sm font-semibold leading-relaxed text-slate-300">
                    Controla clientes, vehículos, órdenes y fiados desde el celular.
                </p>
            </div>

            <div class="glass-card rounded-[2rem] p-5 shadow-2xl ring-1 ring-white/70">

                <form method="POST" autocomplete="off" class="space-y-4">

                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-700">
                            Usuario
                        </label>

                        <input
                            type="text"
                            name="usuario"
                            value="admin"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white px-4 font-bold text-slate-950 outline-none transition focus:border-slate-950 focus:ring-4 focus:ring-slate-950/10"
                            required>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-700">
                            Contraseña
                        </label>

                        <input
                            type="password"
                            name="password"
                            value="123456"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-white px-4 font-bold text-slate-950 outline-none transition focus:border-slate-950 focus:ring-4 focus:ring-slate-950/10"
                            required>
                    </div>

                    <button
                        type="submit"
                        class="mt-2 flex h-14 w-full items-center justify-center rounded-2xl bg-slate-950 font-black text-white shadow-xl shadow-slate-950/20 active:scale-[.98]">
                        Ingresar al taller
                    </button>

                </form>

            </div>

            <p class="mt-5 text-center text-xs font-semibold text-slate-400">
                MVP inicial · Multiempresa · Multiusuario
            </p>

        </section>

    </main>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js');
        }
    </script>

</body>

</html>
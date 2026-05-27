<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../app/controllers/CtrUsuarios.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerPro</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="manifest" href="manifest.json">

    <meta name="theme-color" content="#111827">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white w-full max-w-md rounded-3xl shadow-xl p-8">

        <h1 class="text-3xl font-bold text-center mb-2">
            TallerPro
        </h1>

        <p class="text-center text-gray-500 mb-8">
            Sistema para talleres automotrices
        </p>

        <form method="POST">

            <div class="mb-4">
                <label class="block mb-2 font-semibold">Usuario</label>

                <input
                    type="text"
                    name="usuario"
                    class="w-full border rounded-xl px-4 py-3"
                    required
                >
            </div>

            <div class="mb-6">
                <label class="block mb-2 font-semibold">Password</label>

                <input
                    type="password"
                    name="password"
                    class="w-full border rounded-xl px-4 py-3"
                    required
                >
            </div>

            <button
                class="w-full bg-gray-900 text-white py-3 rounded-xl font-bold"
            >
                Ingresar
            </button>

            <?php
            CtrUsuarios::login();
            ?>

        </form>

    </div>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js');
        }
    </script>

</body>
</html>

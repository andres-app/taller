<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrCotizaciones.php';

$respuesta = CtrCotizaciones::guardar();
$datos = CtrCotizaciones::datosNueva();

$vehiculo = $datos['vehiculo'];
$productos = $datos['productos'];

if (!$vehiculo) {
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

    <title>Nueva cotización - TallerPro</title>

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
            padding: 13px 14px;
            font-weight: 800;
            color: #020617;
            outline: none;
        }

        .field:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        .bottom-total {
            padding-bottom: calc(12px + env(safe-area-inset-bottom));
        }
    </style>
</head>

<body>

<div class="app-shell mx-auto max-w-[430px] pb-36">

    <header class="top-header relative overflow-hidden rounded-b-[2rem] px-5 pb-6 text-white shadow-2xl">

        <div class="relative flex items-start justify-between">

            <a
                href="vehiculos.php?placa=<?php echo urlencode($vehiculo['placa']); ?>"
                class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/10 text-xl shadow-xl active:scale-95"
            >
                ‹
            </a>

            <div class="text-center">
                <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">
                    Nueva
                </p>

                <h1 class="mt-1 text-2xl font-black tracking-[-.04em]">
                    Cotización
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
                Vehículo
            </p>

            <h2 class="mt-1 text-2xl font-black tracking-[-.04em]">
                <?php echo htmlspecialchars($vehiculo['placa']); ?>
            </h2>

            <p class="mt-1 text-sm font-bold text-slate-300">
                <?php echo htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']); ?>
            </p>

            <p class="mt-1 text-xs font-semibold text-slate-400">
                Cliente: <?php echo htmlspecialchars($vehiculo['cliente']); ?>
            </p>

        </section>

    </header>

    <main class="px-5 pt-5">

        <?php if ($respuesta['ok'] === false): ?>
            <div class="mb-4 rounded-[1.2rem] border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
                <?php echo htmlspecialchars($respuesta['mensaje']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="formCotizacion">

            <input type="hidden" name="vehiculo_id" value="<?php echo (int)$vehiculo['vehiculo_id']; ?>">

            <section class="soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">

                <div class="mb-4">
                    <h2 class="text-xl font-black tracking-[-.04em]">
                        Items del presupuesto
                    </h2>

                    <p class="mt-1 text-sm font-semibold text-slate-500">
                        No descuenta stock hasta convertirlo en orden.
                    </p>
                </div>

                <div id="itemsContainer" class="space-y-4"></div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <button
                        type="button"
                        onclick="agregarItem('SERVICIO')"
                        class="rounded-2xl bg-slate-950 px-3 py-4 text-xs font-black text-white active:scale-95"
                    >
                        + Servicio
                    </button>

                    <button
                        type="button"
                        onclick="agregarItem('STOCK')"
                        class="rounded-2xl bg-blue-600 px-3 py-4 text-xs font-black text-white active:scale-95"
                    >
                        + Stock
                    </button>

                    <button
                        type="button"
                        onclick="agregarItem('EXTERNO')"
                        class="rounded-2xl bg-amber-500 px-3 py-4 text-xs font-black text-white active:scale-95"
                    >
                        + Externo
                    </button>
                </div>

            </section>

            <section class="mt-5 soft-card rounded-[1.8rem] p-5 ring-1 ring-slate-200/70">
                <label class="field-label">Observación</label>

                <textarea
                    name="observacion"
                    rows="3"
                    class="field resize-none"
                    placeholder="Ej. Presupuesto válido por 3 días..."
                ></textarea>
            </section>

        </form>

    </main>

    <div class="fixed inset-x-0 bottom-0 z-50 bottom-total">
        <div class="mx-auto max-w-[430px] px-5">
            <div class="rounded-[1.7rem] bg-slate-950 p-4 text-white shadow-2xl">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.16em] text-slate-400">
                            Total cotizado
                        </p>

                        <p class="mt-1 text-3xl font-black">
                            S/ <span id="totalTexto">0.00</span>
                        </p>
                    </div>

                    <button
                        type="submit"
                        form="formCotizacion"
                        class="rounded-2xl bg-blue-600 px-5 py-4 text-sm font-black text-white shadow-lg active:scale-95"
                    >
                        Guardar
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
const productos = <?php echo json_encode($productos, JSON_UNESCAPED_UNICODE); ?>;
let itemIndex = 0;

function money(value) {
    return Number(value || 0).toFixed(2);
}

function agregarItem(tipo) {
    const container = document.getElementById('itemsContainer');
    const index = itemIndex++;

    let opcionesProductos = '<option value="">Seleccionar producto</option>';

    productos.forEach(p => {
        opcionesProductos += `
            <option 
                value="${p.id}"
                data-nombre="${p.nombre}"
                data-costo="${p.costo}"
                data-precio="${p.precio_venta}"
                data-stock="${p.stock_actual}"
            >
                ${p.nombre} · Stock ${p.stock_actual} · S/ ${money(p.precio_venta)}
            </option>
        `;
    });

    let color = 'bg-slate-950';
    let titulo = 'Servicio';

    if (tipo === 'STOCK') {
        color = 'bg-blue-600';
        titulo = 'Producto de stock';
    }

    if (tipo === 'EXTERNO') {
        color = 'bg-amber-500';
        titulo = 'Compra externa';
    }

    const html = `
        <article class="rounded-[1.5rem] border border-slate-200 bg-white p-4 item-row">

            <input type="hidden" name="items[${index}][tipo_item]" value="${tipo}">

            <div class="mb-4 flex items-center justify-between">
                <span class="rounded-full ${color} px-3 py-1 text-[11px] font-black text-white">
                    ${titulo}
                </span>

                <button type="button" onclick="this.closest('.item-row').remove(); calcularTotal();" class="text-sm font-black text-red-500">
                    Quitar
                </button>
            </div>

            ${tipo === 'STOCK' ? `
                <div class="mb-3">
                    <label class="field-label">Producto del inventario</label>
                    <select name="items[${index}][producto_id]" class="field producto-select" onchange="seleccionarProducto(this)">
                        ${opcionesProductos}
                    </select>
                </div>
            ` : `
                <input type="hidden" name="items[${index}][producto_id]" value="">
            `}

            <div class="mb-3">
                <label class="field-label">Descripción</label>
                <input 
                    type="text" 
                    name="items[${index}][descripcion]" 
                    class="field descripcion-input"
                    placeholder="${tipo === 'SERVICIO' ? 'Ej. Mano de obra cambio de aceite' : tipo === 'EXTERNO' ? 'Ej. Filtro comprado afuera' : 'Producto'}"
                    ${tipo === 'STOCK' ? 'readonly' : ''}
                >
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="field-label">Cant.</label>
                    <input 
                        type="number" 
                        name="items[${index}][cantidad]" 
                        class="field cantidad-input"
                        value="1"
                        step="0.01"
                        min="0.01"
                        oninput="calcularTotal()"
                    >
                </div>

                <div>
                    <label class="field-label">Costo</label>
                    <input 
                        type="number" 
                        name="items[${index}][costo_unitario]" 
                        class="field costo-input"
                        value="0"
                        step="0.01"
                        min="0"
                    >
                </div>

                <div>
                    <label class="field-label">Venta</label>
                    <input 
                        type="number" 
                        name="items[${index}][precio_unitario]" 
                        class="field precio-input"
                        value="0"
                        step="0.01"
                        min="0"
                        oninput="calcularTotal()"
                    >
                </div>
            </div>

            <div class="mt-3 rounded-2xl bg-slate-50 p-3 text-right">
                <p class="text-xs font-black uppercase tracking-[.14em] text-slate-400">
                    Subtotal
                </p>

                <p class="text-xl font-black">
                    S/ <span class="subtotal-text">0.00</span>
                </p>
            </div>

        </article>
    `;

    container.insertAdjacentHTML('beforeend', html);
    calcularTotal();
}

function seleccionarProducto(select) {
    const row = select.closest('.item-row');
    const option = select.options[select.selectedIndex];

    const nombre = option.dataset.nombre || '';
    const costo = option.dataset.costo || 0;
    const precio = option.dataset.precio || 0;

    row.querySelector('.descripcion-input').value = nombre;
    row.querySelector('.costo-input').value = money(costo);
    row.querySelector('.precio-input').value = money(precio);

    calcularTotal();
}

function calcularTotal() {
    let total = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const cantidad = parseFloat(row.querySelector('.cantidad-input').value || 0);
        const precio = parseFloat(row.querySelector('.precio-input').value || 0);
        const subtotal = cantidad * precio;

        row.querySelector('.subtotal-text').innerText = money(subtotal);
        total += subtotal;
    });

    document.getElementById('totalTexto').innerText = money(total);
}

agregarItem('SERVICIO');
</script>

</body>
</html>
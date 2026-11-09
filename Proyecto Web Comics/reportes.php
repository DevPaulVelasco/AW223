<?php
/**
 * KomiX - Dashboard y Reportes
 * Archivo: reportes.php (Página 15 del diseño)
 */
require_once 'includes/auth.php';
$pagina_actual = 'reportes';
$titulo_pagina = 'KomiX - Dashboard & Reportes';
include 'includes/header.php';
include 'includes/sidebar.php';

$top_productos = [
    ['pos' => '1', 'producto' => 'Spider-Man Vol.1 #45', 'unidades' => '85 uds', 'total' => '$1,275.00'],
    ['pos' => '2', 'producto' => 'Batman: Year One', 'unidades' => '40 uds', 'total' => '$1,200.00'],
    ['pos' => '3', 'producto' => 'X-Men: Dark Phoenix', 'unidades' => '35 uds', 'total' => '$980.00'],
    ['pos' => '4', 'producto' => 'Watchmen Deluxe', 'unidades' => '28 uds', 'total' => '$890.00'],
    ['pos' => '5', 'producto' => 'Spawn #1 Especial', 'unidades' => '22 uds', 'total' => '$650.00']
];

$ultimos_movimientos = [
    ['hora' => '12:40 PM', 'tipo' => 'Venta #104', 'monto' => '$754.00', 'usuario' => 'Mendoza'],
    ['hora' => '12:40 PM', 'tipo' => 'Venta #103', 'monto' => '$754.00', 'usuario' => 'Mendoza'],
    ['hora' => '11:15 AM', 'tipo' => 'Entrada Pedido #02', 'monto' => '$2,350.00', 'usuario' => 'Admin'],
    ['hora' => '10:02 AM', 'tipo' => 'Venta #102', 'monto' => '$410.00', 'usuario' => 'García'],
    ['hora' => '09:20 AM', 'tipo' => 'Venta #101', 'monto' => '$185.00', 'usuario' => 'Mendoza']
];
?>

<main class="main-content">
    <div class="content-header-row" style="margin-bottom: 20px;">
        <h1 class="page-title">Dashboard</h1>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-weight: 700; font-size: 0.95rem;">Rangos de fechas:</span>
            <select class="btn-filtro" id="dashboard-periodo" aria-label="Filtrar periodo del dashboard">
                <option value="mes">Este Mes</option>
                <option value="semana">Últimos 7 días</option>
                <option value="ano">Este Año</option>
                <option value="todo">Todo</option>
            </select>
        </div>
    </div>

    <!-- 3 Tarjetas Rojas de KPIs -->
    <div class="kpi-cards-grid">
        <div class="kpi-card kpi-card-rojo">
            <span class="kpi-title">Ingresos totales</span>
            <span class="kpi-value" data-metrica>$45,000,000</span>
        </div>
        <div class="kpi-card kpi-card-rojo">
            <span class="kpi-title">Total de ventas</span>
            <span class="kpi-value" data-metrica>328 ventas</span>
        </div>
        <div class="kpi-card kpi-card-rojo">
            <span class="kpi-title">Ticket promedio</span>
            <span class="kpi-value" data-metrica>$138.05</span>
        </div>
    </div>

    <!-- Gráfico de Ventas (Ventas en el Tiempo) -->
    <div class="chart-box-container">
        <h3 class="chart-title" id="titulo-grafico">Gráfico de Ventas (Ventas en el Tiempo) · Este Mes</h3>
        <div style="width: 100%; height: 200px; position: relative;">
            <svg viewBox="0 0 900 180" style="width: 100%; height: 100%; overflow: hidden;">
                <!-- Líneas de cuadrícula horizontal -->
                <line x1="50" y1="20" x2="880" y2="20" stroke="#eee" stroke-width="1" />
                <line x1="50" y1="60" x2="880" y2="60" stroke="#eee" stroke-width="1" />
                <line x1="50" y1="100" x2="880" y2="100" stroke="#eee" stroke-width="1" />
                <line x1="50" y1="140" x2="880" y2="140" stroke="#eee" stroke-width="1" />

                <!-- Etiquetas eje Y -->
                <text x="40" y="24" text-anchor="end" font-size="10" fill="#888">1600</text>
                <text x="40" y="64" text-anchor="end" font-size="10" fill="#888">1200</text>
                <text x="40" y="104" text-anchor="end" font-size="10" fill="#888">800</text>
                <text x="40" y="144" text-anchor="end" font-size="10" fill="#888">400</text>

                <!-- Curva de Ventas 1 (Marrón/Gris) -->
                <path id="serie-ventas-1" d="M 60 140 Q 90 90 120 120 T 180 110 T 240 100 T 300 130 T 360 80 T 420 110 T 480 75 T 540 120 T 600 70 T 660 110 T 720 90 T 780 120 T 840 60" fill="none" stroke="#777" stroke-width="2" />

                <!-- Curva de Ventas 2 (Rojo KomiX) -->
                <path id="serie-ventas-2" d="M 60 150 Q 100 110 140 130 T 220 95 T 310 115 T 400 65 T 490 135 T 580 85 T 670 70 T 750 115 T 840 85" fill="none" stroke="#7b0808" stroke-width="2.5" />

                <!-- Días 1 a 30 en eje X -->
                <?php for ($d = 1; $d <= 30; $d++): 
                    $x = 60 + (($d - 1) * (800 / 29));
                ?>
                    <text class="etiqueta-dia" x="<?php echo $x; ?>" y="165" text-anchor="middle" font-size="9" fill="#777"><?php echo $d; ?></text>
                <?php endfor; ?>
                <text x="470" y="178" text-anchor="middle" font-size="10" font-weight="700" fill="#555">Días</text>
            </svg>
        </div>
    </div>

    <!-- Cuadrícula Inferior: Top 5 y Últimos Movimientos -->
    <div class="dashboard-bottom-grid">
        <!-- Top 5 Productos más vendidos -->
        <div class="dashboard-panel-card">
            <h4>Top 5 Productos más vendidos</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="text-align: left; color: #666; border-bottom: 1px solid #eee;">
                        <th style="padding: 6px 0;">Pos.</th>
                        <th>Producto</th>
                        <th>Unidades Vendidas</th>
                        <th>Total Gen.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_productos as $tp): ?>
                        <tr style="border-bottom: 1px solid #f2f2f2;">
                            <td style="padding: 8px 0; font-weight: 700;"><?php echo $tp['pos']; ?></td>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($tp['producto']); ?></td>
                            <td><?php echo $tp['unidades']; ?></td>
                            <td style="font-weight: 700;"><?php echo $tp['total']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Últimos Movimientos (Inventario y Ventas) -->
        <div class="dashboard-panel-card">
            <h4>Últimos Movimientos (Inventario y Ventas)</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="text-align: left; color: #666; border-bottom: 1px solid #eee;">
                        <th style="padding: 6px 0;">Hora/Fecha</th>
                        <th>Tipo (Venta/Entrada)</th>
                        <th>Monto</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimos_movimientos as $um): ?>
                        <tr style="border-bottom: 1px solid #f2f2f2;">
                            <td style="padding: 8px 0;"><?php echo $um['hora']; ?></td>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($um['tipo']); ?></td>
                            <td style="font-weight: 700;"><?php echo $um['monto']; ?></td>
                            <td style="color: #555;"><?php echo htmlspecialchars($um['usuario']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

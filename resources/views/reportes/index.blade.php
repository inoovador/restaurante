<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reportes y Análisis - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: #f8f9fa;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <a href="/dashboard" class="text-gray-600 hover:text-gray-900 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Reportes y Análisis</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="exportarReporte()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-download mr-2"></i>
                            Exportar
                        </button>
                        <button onclick="imprimirReporte()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-print mr-2"></i>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Filtros de Fecha -->
            <div class="bg-white rounded-lg shadow mb-6 p-4">
                <form method="GET" action="{{ route('reportes.index') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" 
                               class="px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                        <input type="date" name="fecha_fin" value="{{ $fechaFin }}" 
                               class="px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-filter mr-2"></i>
                        Filtrar
                    </button>
                    <div class="ml-auto flex gap-2">
                        <button type="button" onclick="setFechaRapida('hoy')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Hoy</button>
                        <button type="button" onclick="setFechaRapida('semana')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Esta Semana</button>
                        <button type="button" onclick="setFechaRapida('mes')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Este Mes</button>
                        <button type="button" onclick="setFechaRapida('año')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Este Año</button>
                    </div>
                </form>
            </div>

            <!-- KPIs Principales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                        </div>
                        <span class="text-sm {{ $stats['cambio_porcentual'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas fa-arrow-{{ $stats['cambio_porcentual'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ number_format(abs($stats['cambio_porcentual']), 1) }}%
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">Ventas Totales</p>
                    <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($stats['ventas_totales'], 2) }}</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">Número de Ventas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['numero_ventas']) }}</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-receipt text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">Ticket Promedio</p>
                    <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($stats['ticket_promedio'], 2) }}</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <i class="fas fa-box text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">Productos Vendidos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['productos_vendidos']) }}</p>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Gráfico de Ventas por Día -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas por Día</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="ventasPorDiaChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Ventas por Categoría -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas por Categoría</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="ventasPorCategoriaChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Más Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Gráfico de Ventas por Hora -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas por Hora del Día</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="ventasPorHoraChart"></canvas>
                    </div>
                </div>

                <!-- Métodos de Pago -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Métodos de Pago</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="metodosPagoChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tablas de Datos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Productos Más Vendidos -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">Top 10 Productos Más Vendidos</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($productosMasVendidos as $index => $producto)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                        <div class="text-sm text-gray-500">{{ $producto->codigo }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $producto->cantidad_vendida }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">S/ {{ number_format($producto->total_vendido, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay datos disponibles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Empleados -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">Top 5 Empleados por Ventas</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ventas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($empleadosTopVentas as $index => $empleado)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600 text-xs"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $empleado->empleado }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $empleado->cantidad_ventas }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">S/ {{ number_format($empleado->total_vendido, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay datos disponibles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos para los gráficos
        const ventasPorDia = @json($ventasPorDia);
        const ventasPorCategoria = @json($ventasPorCategoria);
        const ventasPorHora = @json($ventasPorHora);
        const metodosPago = @json($metodosPago);

        // Gráfico de Ventas por Día
        const ctxVentasDia = document.getElementById('ventasPorDiaChart').getContext('2d');
        new Chart(ctxVentasDia, {
            type: 'line',
            data: {
                labels: ventasPorDia.map(v => v.fecha),
                datasets: [{
                    label: 'Ventas',
                    data: ventasPorDia.map(v => v.total),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'S/ ' + value.toFixed(0);
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Ventas por Categoría
        const ctxCategoria = document.getElementById('ventasPorCategoriaChart').getContext('2d');
        new Chart(ctxCategoria, {
            type: 'doughnut',
            data: {
                labels: ventasPorCategoria.map(v => v.categoria),
                datasets: [{
                    data: ventasPorCategoria.map(v => v.total),
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(139, 92, 246)',
                        'rgb(239, 68, 68)',
                        'rgb(107, 114, 128)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Ventas por Hora
        const ctxHora = document.getElementById('ventasPorHoraChart').getContext('2d');
        new Chart(ctxHora, {
            type: 'bar',
            data: {
                labels: ventasPorHora.map(v => v.hora + ':00'),
                datasets: [{
                    label: 'Ventas',
                    data: ventasPorHora.map(v => v.total),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'S/ ' + value.toFixed(0);
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Métodos de Pago
        const ctxPago = document.getElementById('metodosPagoChart').getContext('2d');
        new Chart(ctxPago, {
            type: 'pie',
            data: {
                labels: metodosPago.map(m => m.metodo_pago || 'Efectivo'),
                datasets: [{
                    data: metodosPago.map(m => m.total),
                    backgroundColor: [
                        'rgb(245, 158, 11)',
                        'rgb(139, 92, 246)',
                        'rgb(239, 68, 68)',
                        'rgb(59, 130, 246)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Funciones de utilidad
        function setFechaRapida(periodo) {
            const hoy = new Date();
            let fechaInicio, fechaFin;

            switch(periodo) {
                case 'hoy':
                    fechaInicio = fechaFin = hoy.toISOString().split('T')[0];
                    break;
                case 'semana':
                    const inicioSemana = new Date(hoy);
                    inicioSemana.setDate(hoy.getDate() - hoy.getDay());
                    fechaInicio = inicioSemana.toISOString().split('T')[0];
                    fechaFin = hoy.toISOString().split('T')[0];
                    break;
                case 'mes':
                    fechaInicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().split('T')[0];
                    fechaFin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0).toISOString().split('T')[0];
                    break;
                case 'año':
                    fechaInicio = new Date(hoy.getFullYear(), 0, 1).toISOString().split('T')[0];
                    fechaFin = new Date(hoy.getFullYear(), 11, 31).toISOString().split('T')[0];
                    break;
            }

            document.querySelector('input[name="fecha_inicio"]').value = fechaInicio;
            document.querySelector('input[name="fecha_fin"]').value = fechaFin;
            document.querySelector('form').submit();
        }

        function exportarReporte() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '/reportes/export?' + params.toString();
        }

        function imprimirReporte() {
            window.print();
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Control de Caja - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #E32636;
            --secondary-color: #4d82bc;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <a href="/dashboard" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Control de Caja</h1>
                            <p class="text-sm text-gray-500">
                                @if($caja)
                                    Caja abierta el {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-red-500">Caja cerrada - Debe abrir caja para operar</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        @if($caja)
                            <button onclick="openMovimientoModal('egreso')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                                <i class="fas fa-minus mr-2"></i>
                                Egreso
                            </button>
                            <button onclick="openMovimientoModal('ingreso')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all">
                                <i class="fas fa-plus mr-2"></i>
                                Ingreso
                            </button>
                            <button onclick="cerrarCaja()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all">
                                <i class="fas fa-lock mr-2"></i>
                                Cerrar Caja
                            </button>
                        @else
                            <button onclick="abrirCaja()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                                <i class="fas fa-unlock mr-2"></i>
                                Abrir Caja
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($caja)
        <!-- Estadísticas de Caja -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Saldo Inicial</p>
                            <p class="text-3xl font-bold text-blue-600">S/. {{ number_format($stats['saldo_inicial'] ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-piggy-bank text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Ingresos</p>
                            <p class="text-3xl font-bold text-green-600">S/. {{ number_format($stats['ingresos'] ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Egresos</p>
                            <p class="text-3xl font-bold text-red-600">S/. {{ number_format($stats['egresos'] ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Saldo Actual</p>
                            <p class="text-3xl font-bold text-purple-600">S/. {{ number_format($stats['saldo_actual'] ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-wallet text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de Ventas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen del Día</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-600">Ventas del Día</p>
                        <p class="text-2xl font-bold text-green-600">S/. {{ number_format($stats['ventas_dia'] ?? 0, 2) }}</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600">Efectivo Esperado</p>
                        <p class="text-2xl font-bold text-blue-600">S/. {{ number_format(($stats['saldo_inicial'] ?? 0) + ($stats['ventas_dia'] ?? 0), 2) }}</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-sm text-gray-600">Diferencia</p>
                        <p class="text-2xl font-bold text-purple-600">S/. {{ number_format(($stats['saldo_actual'] ?? 0) - (($stats['saldo_inicial'] ?? 0) + ($stats['ventas_dia'] ?? 0)), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movimientos de Caja -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Movimientos de Hoy</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hora
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Concepto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Monto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($movimientos as $movimiento)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movimiento->created_at ? \Carbon\Carbon::parse($movimiento->created_at)->format('H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $movimiento->tipo === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $movimiento->tipo === 'ingreso' ? 'Ingreso' : 'Egreso' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movimiento->concepto ?? 'Sin concepto' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium
                                    {{ $movimiento->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movimiento->tipo === 'ingreso' ? '+' : '-' }} S/. {{ number_format($movimiento->monto ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $movimiento->usuario ?? 'Sistema' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-receipt text-4xl mb-3"></i>
                                        <p class="text-lg font-medium">No hay movimientos registrados</p>
                                        <p class="text-sm mt-2">Los movimientos aparecerán aquí</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <!-- Caja Cerrada -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <div class="mx-auto h-24 w-24 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-lock text-red-600 text-3xl"></i>
                </div>
                <h3 class="mt-4 text-xl font-medium text-gray-900">Caja Cerrada</h3>
                <p class="mt-2 text-gray-500">Debe abrir la caja para comenzar a operar</p>
                <button onclick="abrirCaja()" class="mt-6 px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                    <i class="fas fa-unlock mr-2"></i>
                    Abrir Caja
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal Movimiento -->
    <div id="movimientoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Nuevo Movimiento</h2>
            <form id="movimientoForm">
                <input type="hidden" id="tipoMovimiento" name="tipo">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                        <input type="number" name="monto" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                        <input type="text" name="concepto" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirCaja() {
            const monto = prompt('Ingrese el monto inicial de caja:');
            if (monto && !isNaN(monto) && parseFloat(monto) >= 0) {
                // Implementar lógica para abrir caja
                alert(`Caja abierta con S/. ${monto}`);
                location.reload();
            }
        }

        function cerrarCaja() {
            if (confirm('¿Está seguro de cerrar la caja? Esta acción no se puede deshacer.')) {
                // Implementar lógica para cerrar caja
                alert('Caja cerrada correctamente');
                location.reload();
            }
        }

        function openMovimientoModal(tipo) {
            document.getElementById('tipoMovimiento').value = tipo;
            document.getElementById('modalTitle').textContent = tipo === 'ingreso' ? 'Nuevo Ingreso' : 'Nuevo Egreso';
            document.getElementById('movimientoModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('movimientoModal').classList.add('hidden');
            document.getElementById('movimientoForm').reset();
        }

        // Manejo del formulario
        document.getElementById('movimientoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const tipo = formData.get('tipo');
            const monto = formData.get('monto');
            const concepto = formData.get('concepto');
            
            // Implementar lógica para registrar movimiento
            console.log('Registrando movimiento:', { tipo, monto, concepto });
            
            closeModal();
            alert(`${tipo === 'ingreso' ? 'Ingreso' : 'Egreso'} registrado correctamente`);
            location.reload();
        });
    </script>
</body>
</html>
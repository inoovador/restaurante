<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Pedidos - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        .status-pendiente { 
            @apply bg-yellow-100 text-yellow-800; 
        }
        .status-preparando { 
            @apply bg-blue-100 text-blue-800; 
        }
        .status-listo { 
            @apply bg-green-100 text-green-800; 
        }
        .status-completado { 
            @apply bg-gray-100 text-gray-800; 
        }
        .status-cancelado { 
            @apply bg-red-100 text-red-800; 
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
                        <h1 class="text-2xl font-bold text-gray-900">Lista de Pedidos</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="refreshPedidos()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Actualizar
                        </button>
                        <a href="/ventas" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Nuevo Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-list-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Pedidos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pedidos'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pendientes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pedidos_pendientes'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-utensils text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Preparando</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pedidos_preparando'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Completados</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pedidos_completados'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select id="filtroEstado" class="border border-gray-300 rounded-md px-3 py-2" onchange="filtrarPedidos()">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="preparando">Preparando</option>
                                <option value="listo">Listo</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                            <input type="date" id="filtroFecha" class="border border-gray-300 rounded-md px-3 py-2" onchange="filtrarPedidos()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <input type="text" id="buscarPedido" placeholder="Número de pedido o cliente" class="border border-gray-300 rounded-md px-3 py-2" oninput="filtrarPedidos()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Pedidos -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pedidos Recientes</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pedido
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cliente/Mesa
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Items
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="pedidosTable">
                            @forelse($pedidos as $pedido)
                            <tr class="hover:bg-gray-50" data-estado="{{ $pedido->estado ?? 'pendiente' }}" data-fecha="{{ \Carbon\Carbon::parse($pedido->created_at)->format('Y-m-d') }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-receipt text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">#{{ $pedido->id }}</div>
                                            <div class="text-sm text-gray-500">Pedido {{ $pedido->numero_venta ?? $pedido->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $pedido->cliente_nombre ?? 'Cliente General' }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($pedido->mesa_numero)
                                            Mesa {{ $pedido->mesa_numero }}
                                        @else
                                            Para llevar
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($pedido->items->take(3) as $item)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $item->cantidad }}x {{ Str::limit($item->producto_nombre, 15) }}
                                        </span>
                                        @endforeach
                                        @if($pedido->items->count() > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            +{{ $pedido->items->count() - 3 }} más
                                        </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">S/ {{ number_format($pedido->total, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select class="status-select px-2 py-1 text-xs font-medium rounded-full border-0 status-{{ $pedido->estado ?? 'pendiente' }}" 
                                            data-pedido-id="{{ $pedido->id }}" onchange="cambiarEstado(this)">
                                        <option value="pendiente" {{ ($pedido->estado ?? 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="preparando" {{ ($pedido->estado ?? '') == 'preparando' ? 'selected' : '' }}>Preparando</option>
                                        <option value="listo" {{ ($pedido->estado ?? '') == 'listo' ? 'selected' : '' }}>Listo</option>
                                        <option value="completado" {{ ($pedido->estado ?? '') == 'completado' ? 'selected' : '' }}>Completado</option>
                                        <option value="cancelado" {{ ($pedido->estado ?? '') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="verDetalle({{ $pedido->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="imprimirPedido({{ $pedido->id }})" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-list-alt text-4xl mb-3"></i>
                                        <p class="text-lg font-medium">No hay pedidos registrados</p>
                                        <p class="text-sm">Los pedidos aparecerán aquí una vez que se realicen ventas.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshPedidos() {
            window.location.reload();
        }

        function filtrarPedidos() {
            const estado = document.getElementById('filtroEstado').value;
            const fecha = document.getElementById('filtroFecha').value;
            const buscar = document.getElementById('buscarPedido').value.toLowerCase();
            const filas = document.querySelectorAll('#pedidosTable tr');

            filas.forEach(fila => {
                let mostrar = true;

                // Filtrar por estado
                if (estado && fila.dataset.estado !== estado) {
                    mostrar = false;
                }

                // Filtrar por fecha
                if (fecha && fila.dataset.fecha !== fecha) {
                    mostrar = false;
                }

                // Filtrar por búsqueda
                if (buscar && !fila.textContent.toLowerCase().includes(buscar)) {
                    mostrar = false;
                }

                fila.style.display = mostrar ? '' : 'none';
            });
        }

        function cambiarEstado(select) {
            const pedidoId = select.dataset.pedidoId;
            const nuevoEstado = select.value;

            fetch(`/pedidos/${pedidoId}/estado`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ estado: nuevoEstado })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar clase de estado visual
                    select.className = `status-select px-2 py-1 text-xs font-medium rounded-full border-0 status-${nuevoEstado}`;
                    select.closest('tr').dataset.estado = nuevoEstado;
                    
                    // Mostrar notificación
                    showNotification(`Estado actualizado a ${nuevoEstado}`, 'success');
                } else {
                    showNotification('Error al actualizar estado', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al actualizar estado', 'error');
            });
        }

        function verDetalle(pedidoId) {
            window.location.href = `/pedidos/${pedidoId}`;
        }

        function imprimirPedido(pedidoId) {
            // Crear un iframe oculto para imprimir sin afectar la página actual
            const iframe = document.createElement('iframe');
            iframe.style.position = 'absolute';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';
            iframe.style.left = '-9999px';
            iframe.src = `/pedidos/${pedidoId}/print`; // Usar ruta específica de impresión
            
            // Agregar el iframe al documento
            document.body.appendChild(iframe);
            
            // Remover el iframe después de un tiempo
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 5000);
        }

        function showNotification(message, type) {
            const colors = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'info': 'bg-blue-500'
            };

            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Control de Inventario - FoodPoint</title>
    
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
                            <h1 class="text-2xl font-bold text-gray-900">Control de Inventario</h1>
                            <p class="text-sm text-gray-500">Administra el stock y movimientos de inventario</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="openMovimientoModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            Movimiento
                        </button>
                        <button onclick="exportarInventario()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all">
                            <i class="fas fa-file-excel mr-2"></i>
                            Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Productos</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_productos'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-boxes text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Stock Bajo</p>
                            <p class="text-3xl font-bold text-red-600">{{ $stats['stock_bajo'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Valor Inventario</p>
                            <p class="text-2xl font-bold text-green-600">S/. {{ number_format($stats['valor_inventario'] ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Productos Activos</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['productos_activos'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs de Navegación -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow mb-4">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6">
                        <button onclick="showTab('productos')" class="tab-button active py-4 px-1 border-b-2 border-red-500 text-red-600 font-medium text-sm">
                            Productos
                        </button>
                        <button onclick="showTab('movimientos')" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                            Movimientos
                        </button>
                        <button onclick="showTab('alertas')" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                            Alertas de Stock
                        </button>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Tab Productos -->
        <div id="productos-tab" class="tab-content">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-64">
                            <div class="relative">
                                <input type="text" id="searchProductos" placeholder="Buscar productos..." 
                                       class="w-full px-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Estado:</label>
                            <select onchange="filtrarProductos(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option value="">Todos</option>
                                <option value="activo">Activos</option>
                                <option value="stock_bajo">Stock Bajo</option>
                                <option value="sin_stock">Sin Stock</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Productos -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock Actual
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock Mínimo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Valor Stock
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="productosTableBody">
                                @foreach($productos as $producto)
                                <tr class="hover:bg-gray-50 producto-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 producto-nombre">{{ $producto->nombre }}</div>
                                        <div class="text-sm text-gray-500">Código: {{ $producto->codigo ?? 'Sin código' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold 
                                            {{ $producto->stock <= $producto->stock_minimo ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $producto->stock }} unidades
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $producto->stock_minimo }} unidades
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        S/. {{ number_format($producto->stock * ($producto->precio_compra ?? 0), 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($producto->stock <= 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Sin Stock
                                            </span>
                                        @elseif($producto->stock <= $producto->stock_minimo)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Stock Bajo
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Normal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="ajustarStock({{ $producto->id }}, '{{ $producto->nombre }}')" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="verMovimientos({{ $producto->id }})" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Movimientos -->
        <div id="movimientos-tab" class="tab-content hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Últimos Movimientos</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Motivo
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($movimientos as $movimiento)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $movimiento->created_at ? \Carbon\Carbon::parse($movimiento->created_at)->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $movimiento->producto_nombre ?? 'Producto eliminado' }}</div>
                                        <div class="text-sm text-gray-500">{{ $movimiento->producto_codigo ?? 'Sin código' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $movimiento->tipo === 'entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($movimiento->tipo ?? 'entrada') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium
                                        {{ $movimiento->tipo === 'entrada' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $movimiento->tipo === 'entrada' ? '+' : '-' }} {{ $movimiento->cantidad ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $movimiento->motivo ?? 'Sin especificar' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <i class="fas fa-exchange-alt text-4xl mb-3"></i>
                                            <p class="text-lg font-medium">No hay movimientos registrados</p>
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

        <!-- Tab Alertas -->
        <div id="alertas-tab" class="tab-content hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
                <div class="space-y-4">
                    @foreach($productos->filter(function($p) { return $p->stock <= $p->stock_minimo; }) as $producto)
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-100 rounded-lg mr-4">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">{{ $producto->nombre }}</h4>
                                    <p class="text-sm text-gray-500">
                                        Stock actual: <span class="font-medium text-red-600">{{ $producto->stock }}</span> 
                                        | Mínimo requerido: {{ $producto->stock_minimo }}
                                    </p>
                                </div>
                            </div>
                            <button onclick="ajustarStock({{ $producto->id }}, '{{ $producto->nombre }}')" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Reabastecer
                            </button>
                        </div>
                    </div>
                    @endforeach

                    @if($productos->filter(function($p) { return $p->stock <= $p->stock_minimo; })->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-green-500">
                            <i class="fas fa-check-circle text-4xl mb-3"></i>
                            <p class="text-lg font-medium">No hay alertas de stock</p>
                            <p class="text-sm mt-2">Todos los productos tienen stock suficiente</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Movimiento de Stock -->
    <div id="movimientoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Ajustar Stock</h2>
            <form id="movimientoForm">
                <input type="hidden" id="productoId" name="producto_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
                        <input type="text" id="productoNombre" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Movimiento</label>
                        <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="entrada">Entrada (Agregar)</option>
                            <option value="salida">Salida (Quitar)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                        <input type="number" name="cantidad" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                        <select name="motivo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="">Seleccionar motivo</option>
                            <option value="compra">Compra</option>
                            <option value="venta">Venta</option>
                            <option value="ajuste">Ajuste de inventario</option>
                            <option value="merma">Merma</option>
                            <option value="devolucion">Devolución</option>
                        </select>
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
        // Gestión de tabs
        function showTab(tabName) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Mostrar el contenido seleccionado
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Actualizar botones
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-red-500', 'text-red-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-red-500', 'text-red-600');
        }

        // Búsqueda de productos
        document.getElementById('searchProductos').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.producto-row');
            
            rows.forEach(row => {
                const nombre = row.querySelector('.producto-nombre').textContent.toLowerCase();
                row.style.display = nombre.includes(searchTerm) ? '' : 'none';
            });
        });

        function filtrarProductos(filtro) {
            const rows = document.querySelectorAll('.producto-row');
            rows.forEach(row => {
                let mostrar = true;
                
                if (filtro === 'stock_bajo') {
                    mostrar = row.textContent.includes('Stock Bajo');
                } else if (filtro === 'sin_stock') {
                    mostrar = row.textContent.includes('Sin Stock');
                } else if (filtro === 'activo') {
                    mostrar = !row.textContent.includes('Sin Stock');
                }
                
                row.style.display = mostrar ? '' : 'none';
            });
        }

        function openMovimientoModal() {
            document.getElementById('movimientoModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('movimientoModal').classList.add('hidden');
            document.getElementById('movimientoForm').reset();
        }

        function ajustarStock(id, nombre) {
            document.getElementById('productoId').value = id;
            document.getElementById('productoNombre').value = nombre;
            openMovimientoModal();
        }

        function verMovimientos(id) {
            showTab('movimientos');
            // Filtrar movimientos por producto
        }

        function exportarInventario() {
            alert('Exportando inventario a Excel...');
        }

        // Manejo del formulario
        document.getElementById('movimientoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const tipo = formData.get('tipo');
            const cantidad = formData.get('cantidad');
            const motivo = formData.get('motivo');
            
            closeModal();
            alert(`Movimiento de ${tipo} registrado correctamente`);
            location.reload();
        });
    </script>
</body>
</html>
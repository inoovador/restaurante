<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Compras - FoodPoint</title>
    
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
                            <h1 class="text-2xl font-bold text-gray-900">Gestión de Compras</h1>
                            <p class="text-sm text-gray-500">Administra las compras y proveedores</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="openProveedorModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                            <i class="fas fa-truck mr-2"></i>
                            Proveedores
                        </button>
                        <button onclick="openCompraModal()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                            <i class="fas fa-plus mr-2"></i>
                            Nueva Compra
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
                            <p class="text-sm font-medium text-gray-600">Total Compras</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Este Mes</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['mes_actual'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-calendar-month text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Monto Mes</p>
                            <p class="text-2xl font-bold text-purple-600">S/. {{ number_format($stats['total_monto'] ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pendientes</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pendientes'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <input type="text" id="searchCompras" placeholder="Buscar compras..." 
                                   class="w-full px-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Estado:</label>
                        <select onchange="filtrarPorEstado(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="recibido">Recibido</option>
                            <option value="pagado">Pagado</option>
                        </select>
                    </div>
                    <button onclick="exportarCompras()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-file-excel mr-2"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Compras -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    N° Compra
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Proveedor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="comprasTableBody">
                            @forelse($compras as $compra)
                            <tr class="hover:bg-gray-50 compra-row">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ str_pad($compra->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-sm text-gray-500">{{ $compra->documento ?? 'Sin documento' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $compra->proveedor_nombre ?? 'Proveedor no encontrado' }}</div>
                                    <div class="text-sm text-gray-500">RUC: {{ $compra->proveedor_ruc ?? 'Sin RUC' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $compra->fecha ? \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') : 'Sin fecha' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">S/. {{ number_format($compra->total ?? 0, 2) }}</div>
                                    <div class="text-sm text-gray-500">{{ $compra->items ?? 0 }} items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $estado = $compra->estado ?? 'pendiente';
                                        $colors = [
                                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                                            'recibido' => 'bg-blue-100 text-blue-800',
                                            'pagado' => 'bg-green-100 text-green-800',
                                            'cancelado' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$estado] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="verCompra({{ $compra->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editarCompra({{ $compra->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="imprimirCompra({{ $compra->id }})" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                                        <p class="text-lg font-medium">No hay compras registradas</p>
                                        <p class="text-sm mt-2">Comienza registrando tu primera compra</p>
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

    <!-- Modal Nueva Compra -->
    <div id="compraModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Nueva Compra</h2>
            <form id="compraForm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select name="proveedor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="">Seleccionar proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }} - {{ $proveedor->ruc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
                        <input type="text" name="documento" placeholder="Factura/Boleta" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <!-- Detalle de productos -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-medium">Detalle de Compra</h3>
                        <button type="button" onclick="agregarItem()" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-plus mr-1"></i>
                            Agregar Item
                        </button>
                    </div>
                    
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Producto</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Cantidad</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Precio Unit.</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Subtotal</th>
                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="itemsCompra">
                                <tr>
                                    <td class="px-4 py-2">
                                        <input type="text" name="productos[]" placeholder="Nombre del producto" required class="w-full px-2 py-1 border border-gray-300 rounded">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="cantidades[]" placeholder="1" min="1" required class="w-full px-2 py-1 border border-gray-300 rounded" onchange="calcularSubtotal(this)">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="precios[]" placeholder="0.00" step="0.01" min="0" required class="w-full px-2 py-1 border border-gray-300 rounded" onchange="calcularSubtotal(this)">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="subtotales[]" placeholder="0.00" readonly class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-50">
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" onclick="eliminarItem(this)" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <div class="text-xl font-bold">
                            Total: S/. <span id="totalCompra">0.00</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Guardar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Búsqueda de compras
        document.getElementById('searchCompras').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.compra-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        function filtrarPorEstado(estado) {
            const rows = document.querySelectorAll('.compra-row');
            rows.forEach(row => {
                if (!estado || row.textContent.toLowerCase().includes(estado)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openCompraModal() {
            document.getElementById('modalTitle').textContent = 'Nueva Compra';
            document.getElementById('compraForm').reset();
            document.getElementById('compraModal').classList.remove('hidden');
        }

        function openProveedorModal() {
            alert('Modal de proveedores - Por implementar');
        }

        function closeModal() {
            document.getElementById('compraModal').classList.add('hidden');
        }

        function agregarItem() {
            const tbody = document.getElementById('itemsCompra');
            const newRow = tbody.rows[0].cloneNode(true);
            
            // Limpiar valores
            newRow.querySelectorAll('input').forEach(input => {
                input.value = '';
            });
            
            tbody.appendChild(newRow);
        }

        function eliminarItem(btn) {
            const tbody = document.getElementById('itemsCompra');
            if (tbody.rows.length > 1) {
                btn.closest('tr').remove();
                calcularTotal();
            }
        }

        function calcularSubtotal(input) {
            const row = input.closest('tr');
            const cantidad = parseFloat(row.querySelector('input[name="cantidades[]"]').value) || 0;
            const precio = parseFloat(row.querySelector('input[name="precios[]"]').value) || 0;
            const subtotal = cantidad * precio;
            
            row.querySelector('input[name="subtotales[]"]').value = subtotal.toFixed(2);
            calcularTotal();
        }

        function calcularTotal() {
            let total = 0;
            document.querySelectorAll('input[name="subtotales[]"]').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('totalCompra').textContent = total.toFixed(2);
        }

        function verCompra(id) {
            alert(`Ver detalle de compra ${id}`);
        }

        function editarCompra(id) {
            alert(`Editar compra ${id}`);
        }

        function imprimirCompra(id) {
            alert(`Imprimir compra ${id}`);
        }

        function exportarCompras() {
            alert('Exportando compras a Excel...');
        }

        // Manejo del formulario
        document.getElementById('compraForm').addEventListener('submit', function(e) {
            e.preventDefault();
            closeModal();
            alert('Compra registrada correctamente');
        });
    </script>
</body>
</html>
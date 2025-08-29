<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Clientes - FoodPoint</title>
    
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
                            <h1 class="text-2xl font-bold text-gray-900">Gestión de Clientes</h1>
                            <p class="text-sm text-gray-500">Administra la información de tus clientes</p>
                        </div>
                    </div>
                    <button onclick="openAddModal()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                        <i class="fas fa-plus mr-2"></i>
                        Nuevo Cliente
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Clientes</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Nuevos (7 días)</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['nuevos'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-user-plus text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Clientes Frecuentes</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['frecuentes'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-star text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Búsqueda y filtros -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <input type="text" id="searchClientes" placeholder="Buscar clientes..." 
                                   class="w-full px-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <button onclick="exportarClientes()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-file-excel mr-2"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contacto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Documento
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Registro
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="clientesTableBody">
                            @forelse($clientes as $cliente)
                            <tr class="hover:bg-gray-50 cliente-row">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                                                <span class="text-white font-bold">{{ strtoupper(substr($cliente->nombre ?? 'C', 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 cliente-nombre">{{ $cliente->nombre ?? 'Sin nombre' }}</div>
                                            <div class="text-sm text-gray-500">{{ $cliente->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $cliente->telefono ?? 'Sin teléfono' }}</div>
                                    <div class="text-sm text-gray-500">{{ $cliente->direccion ?? 'Sin dirección' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $cliente->tipo_documento ?? 'DNI' }}</div>
                                    <div class="text-sm text-gray-500">{{ $cliente->numero_documento ?? 'Sin documento' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $cliente->created_at ? \Carbon\Carbon::parse($cliente->created_at)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($cliente->activo ?? true)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editarCliente({{ $cliente->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="eliminarCliente({{ $cliente->id }})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-users text-4xl mb-3"></i>
                                        <p class="text-lg font-medium">No hay clientes registrados</p>
                                        <p class="text-sm mt-2">Comienza agregando tu primer cliente</p>
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

    <!-- Modal Agregar/Editar Cliente -->
    <div id="clienteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Nuevo Cliente</h2>
            <form id="clienteForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" name="nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Documento</label>
                        <select name="tipo_documento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="CE">Carnet Extranjería</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número Documento</label>
                        <input type="text" name="numero_documento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="tel" name="telefono" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <textarea name="direccion" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Búsqueda de clientes
        document.getElementById('searchClientes').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.cliente-row');
            
            rows.forEach(row => {
                const nombre = row.querySelector('.cliente-nombre').textContent.toLowerCase();
                if (nombre.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Nuevo Cliente';
            document.getElementById('clienteForm').reset();
            document.getElementById('clienteModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('clienteModal').classList.add('hidden');
        }

        function editarCliente(id) {
            document.getElementById('modalTitle').textContent = 'Editar Cliente';
            document.getElementById('clienteModal').classList.remove('hidden');
            // Aquí implementar lógica para cargar datos del cliente
        }

        function eliminarCliente(id) {
            if (confirm('¿Está seguro de eliminar este cliente?')) {
                // Implementar eliminación
                console.log('Eliminar cliente:', id);
            }
        }

        function exportarClientes() {
            // Implementar exportación
            alert('Exportando clientes a Excel...');
        }

        // Manejo del formulario
        document.getElementById('clienteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí implementar lógica para guardar cliente
            closeModal();
            alert('Cliente guardado correctamente');
        });
    </script>
</body>
</html>
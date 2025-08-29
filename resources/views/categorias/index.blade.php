<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Categorías - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #E32636;
            --secondary-color: #4d82bc;
        }
        
        .category-bebidas { background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); }
        .category-comida { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
        .category-postre { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); }
        .category-otro { background: linear-gradient(135deg, #6B7280 0%, #374151 100%); }
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
                            <h1 class="text-2xl font-bold text-gray-900">Gestión de Categorías</h1>
                            <p class="text-sm text-gray-500">Organiza los productos por categorías</p>
                        </div>
                    </div>
                    <button onclick="openAddModal()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                        <i class="fas fa-plus mr-2"></i>
                        Nueva Categoría
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Categorías</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-tags text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Activas</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['activas'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Comida</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $stats['comida'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-utensils text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Bebidas</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['bebida'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-glass-martini text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Categorías -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Todas las Categorías</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categorias as $categoria)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full {{ 'category-' . $categoria->tipo }} flex items-center justify-center">
                                            <i class="fas fa-{{ $categoria->icono ?? 'tag' }} text-white"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $categoria->nombre }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $categoria->descripcion ?? 'Sin descripción' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $categoria->tipo === 'comida' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $categoria->tipo === 'bebida' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $categoria->tipo === 'postre' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $categoria->tipo === 'otro' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($categoria->tipo) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $categoria->area === 'cocina' ? 'bg-orange-100 text-orange-800' : 'bg-cyan-100 text-cyan-800' }}">
                                        {{ ucfirst($categoria->area ?? 'general') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded" style="background-color: {{ $categoria->color ?? '#6B7280' }}"></div>
                                        <span class="ml-2 text-xs text-gray-500">{{ $categoria->color ?? '#6B7280' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($categoria->activo ?? true)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editCategoria({{ json_encode($categoria) }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteCategoria({{ $categoria->id }})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No hay categorías registradas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar categoría -->
    <div id="categoriaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Nueva Categoría</h3>
            </div>
            <form id="categoriaForm" onsubmit="saveCategoria(event)">
                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" id="categoria_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                            <select id="tipo" name="tipo" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="comida">Comida</option>
                                <option value="bebida">Bebida</option>
                                <option value="postre">Postre</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Área *</label>
                            <select id="area" name="area" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="cocina">Cocina</option>
                                <option value="barra">Barra</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Color *</label>
                            <input type="color" id="color" name="color" value="#E32636" required
                                   class="w-full h-10 border border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icono</label>
                            <select id="icono" name="icono"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="utensils">🍴 Cubiertos</option>
                                <option value="glass-martini">🍸 Copa</option>
                                <option value="ice-cream">🍨 Helado</option>
                                <option value="pizza-slice">🍕 Pizza</option>
                                <option value="hamburger">🍔 Hamburguesa</option>
                                <option value="coffee">☕ Café</option>
                                <option value="beer">🍺 Cerveza</option>
                                <option value="wine-glass">🍷 Vino</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="activo" name="activo" checked
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-600">Categoría activa</span>
                        </label>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para abrir modal de nueva categoría
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Nueva Categoría';
            document.getElementById('categoriaForm').reset();
            document.getElementById('categoria_id').value = '';
            document.getElementById('categoriaModal').classList.remove('hidden');
        }

        // Función para editar categoría
        function editCategoria(categoria) {
            document.getElementById('modalTitle').textContent = 'Editar Categoría';
            document.getElementById('categoria_id').value = categoria.id;
            document.getElementById('nombre').value = categoria.nombre;
            document.getElementById('descripcion').value = categoria.descripcion || '';
            document.getElementById('tipo').value = categoria.tipo;
            document.getElementById('area').value = categoria.area || 'general';
            document.getElementById('color').value = categoria.color || '#E32636';
            document.getElementById('icono').value = categoria.icono || 'utensils';
            document.getElementById('activo').checked = categoria.activo ?? true;
            document.getElementById('categoriaModal').classList.remove('hidden');
        }

        // Función para cerrar modal
        function closeModal() {
            document.getElementById('categoriaModal').classList.add('hidden');
        }

        // Función para guardar categoría
        async function saveCategoria(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            data.activo = document.getElementById('activo').checked ? 1 : 0;
            
            const id = document.getElementById('categoria_id').value;
            const url = id ? `/categorias/${id}` : '/categorias';
            const method = id ? 'PUT' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    closeModal();
                    location.reload();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'No se pudo guardar la categoría'));
                }
            } catch (error) {
                alert('Error de conexión: ' + error.message);
            }
        }

        // Función para eliminar categoría
        async function deleteCategoria(id) {
            if (!confirm('¿Está seguro de eliminar esta categoría?')) {
                return;
            }
            
            try {
                const response = await fetch(`/categorias/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    location.reload();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'No se pudo eliminar la categoría'));
                }
            } catch (error) {
                alert('Error de conexión: ' + error.message);
            }
        }
    </script>
</body>
</html>
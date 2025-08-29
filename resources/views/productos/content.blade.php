{{-- Vista Blade de Productos - Solo contenido HTML, sin layout --}}
<div class="p-6">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestión de Productos</h2>
                <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    + Nuevo Producto
                </button>
            </div>

            {{-- Filtros --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <input type="text" id="searchInput" placeholder="Buscar producto..." 
                       class="px-3 py-2 border border-gray-300 rounded-md">
                
                <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
                
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Todos los estados</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($productos as $producto)
                        <tr class="producto-row" 
                            data-nombre="{{ strtolower($producto->nombre) }}"
                            data-categoria="{{ $producto->categoria_id }}"
                            data-activo="{{ $producto->activo ? '1' : '0' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $producto->codigo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ $producto->descripcion }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $producto->categoria_nombre ?? 'Sin categoría' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${{ number_format($producto->precio_venta, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $producto->stock > 10 ? 'bg-green-100 text-green-800' : ($producto->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $producto->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $producto->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick='editProducto(@json($producto))' class="text-blue-600 hover:text-blue-900 mr-3">
                                    Editar
                                </button>
                                <button onclick="deleteProducto({{ $producto->id }})" class="text-red-600 hover:text-red-900">
                                    Eliminar
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

{{-- Modal --}}
<div id="productoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Nuevo Producto</h3>
            
            <form id="productoForm" data-ajax="true" method="POST" action="{{ route('productos.store') }}">
                @csrf
                <input type="hidden" id="producto_id" name="producto_id">
                <input type="hidden" id="_method" name="_method" value="POST">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                    <input type="text" id="codigo" name="codigo" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select id="categoria_id" name="categoria_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Seleccionar categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                        <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                        <input type="number" id="stock" name="stock" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" value="1" checked class="mr-2 rounded">
                        <span class="text-sm font-medium text-gray-700">Producto activo</span>
                    </label>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Funciones globales para los botones
    window.openModal = function() {
        document.getElementById('productoModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Nuevo Producto';
        document.getElementById('productoForm').reset();
        document.getElementById('producto_id').value = '';
        document.getElementById('_method').value = 'POST';
        document.getElementById('productoForm').action = '{{ route("productos.store") }}';
    };

    window.closeModal = function() {
        document.getElementById('productoModal').classList.add('hidden');
    };

    window.editProducto = function(producto) {
        document.getElementById('productoModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Editar Producto';
        document.getElementById('producto_id').value = producto.id;
        document.getElementById('codigo').value = producto.codigo;
        document.getElementById('nombre').value = producto.nombre;
        document.getElementById('descripcion').value = producto.descripcion || '';
        document.getElementById('categoria_id').value = producto.categoria_id;
        document.getElementById('precio_venta').value = producto.precio_venta;
        document.getElementById('stock').value = producto.stock;
        document.getElementById('activo').checked = producto.activo;
        
        document.getElementById('_method').value = 'PUT';
        document.getElementById('productoForm').action = `/productos/${producto.id}`;
    };

    window.deleteProducto = function(id) {
        if (confirm('¿Está seguro de eliminar este producto?')) {
            fetch(`/productos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Producto eliminado exitosamente');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el producto');
            });
        }
    };

// Manejador del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const method = document.getElementById('_method').value;
            
            // Convertir FormData a objeto
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Asegurar que activo sea booleano
            data.activo = document.getElementById('activo').checked ? 1 : 0;
            
            fetch(this.action, {
                method: method === 'PUT' ? 'PUT' : 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(method === 'PUT' ? 'Producto actualizado exitosamente' : 'Producto creado exitosamente');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el producto');
            });
        });
    }
    
    // Inicializar filtros cuando el contenido esté listo
    initializeFilters();
});

// También escuchar el evento personalizado de HybridPage
window.addEventListener('hybrid-content-loaded', function() {
    initializeFilters();
});

function initializeFilters() {
    // Evitar múltiples inicializaciones
    if (window.filtersInitialized) return;
    window.filtersInitialized = true;
    
    // Filtros
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const categoryId = categoryFilter ? categoryFilter.value : '';
        const status = statusFilter ? statusFilter.value : '';
        
        document.querySelectorAll('.producto-row').forEach(row => {
            const nombre = row.dataset.nombre || '';
            const categoria = row.dataset.categoria || '';
            const activo = row.dataset.activo || '';
            
            const matchSearch = !searchTerm || nombre.includes(searchTerm);
            const matchCategory = !categoryId || categoria === categoryId;
            const matchStatus = !status || activo === status;
            
            row.style.display = matchSearch && matchCategory && matchStatus ? '' : 'none';
        });
    }
    
    if (searchInput) {
        // Remover listeners anteriores y agregar nuevos
        searchInput.removeEventListener('input', filterTable);
        searchInput.addEventListener('input', filterTable);
    }
    if (categoryFilter) {
        categoryFilter.removeEventListener('change', filterTable);
        categoryFilter.addEventListener('change', filterTable);
    }
    if (statusFilter) {
        statusFilter.removeEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);
    }
</script>
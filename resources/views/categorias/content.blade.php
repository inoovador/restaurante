{{-- Vista Blade de Categorías - Solo contenido HTML, sin layout --}}
<div class="p-6">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestión de Categorías</h2>
                <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    + Nueva Categoría
                </button>
            </div>

            {{-- Tabla de Categorías --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categorias as $categoria)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded mr-3" style="background-color: {{ $categoria->color ?? '#3B82F6' }}"></div>
                                    <span class="text-sm font-medium text-gray-900">{{ $categoria->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $categoria->descripcion ?? 'Sin descripción' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ucfirst($categoria->tipo ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ucfirst($categoria->area ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-8 h-8 rounded border" style="background-color: {{ $categoria->color ?? '#3B82F6' }}"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $categoria->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $categoria->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick='editCategoria(@json($categoria))' class="text-blue-600 hover:text-blue-900 mr-3">
                                    Editar
                                </button>
                                <button onclick="deleteCategoria({{ $categoria->id }})" class="text-red-600 hover:text-red-900">
                                    Eliminar
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

{{-- Modal --}}
<div id="categoriaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Nueva Categoría</h3>
            
            <form id="categoriaForm" method="POST" action="{{ route('categorias.store') ?? '/categorias' }}">
                @csrf
                <input type="hidden" id="categoria_id" name="categoria_id">
                <input type="hidden" id="_method" name="_method" value="POST">
                
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
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select id="tipo" name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="comida">Comida</option>
                            <option value="bebida">Bebida</option>
                            <option value="postre">Postre</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Área</label>
                        <select id="area" name="area" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="cocina">Cocina</option>
                            <option value="barra">Barra</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <input type="color" id="color" name="color" value="#3B82F6"
                           class="w-full h-10 border border-gray-300 rounded-md">
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" value="1" checked class="mr-2 rounded">
                        <span class="text-sm font-medium text-gray-700">Categoría activa</span>
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
    document.getElementById('categoriaModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Nueva Categoría';
    document.getElementById('categoriaForm').reset();
    document.getElementById('categoria_id').value = '';
    document.getElementById('_method').value = 'POST';
    document.getElementById('categoriaForm').action = '{{ route("categorias.store") ?? "/categorias" }}';
    document.getElementById('color').value = '#3B82F6';
    document.getElementById('activo').checked = true;
};

window.closeModal = function() {
    document.getElementById('categoriaModal').classList.add('hidden');
};

window.editCategoria = function(categoria) {
    document.getElementById('categoriaModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Editar Categoría';
    document.getElementById('categoria_id').value = categoria.id;
    document.getElementById('nombre').value = categoria.nombre;
    document.getElementById('descripcion').value = categoria.descripcion || '';
    document.getElementById('tipo').value = categoria.tipo || 'comida';
    document.getElementById('area').value = categoria.area || 'cocina';
    document.getElementById('color').value = categoria.color || '#3B82F6';
    document.getElementById('activo').checked = categoria.activo;
    
    document.getElementById('_method').value = 'PUT';
    document.getElementById('categoriaForm').action = `/categorias/${categoria.id}`;
};

window.deleteCategoria = function(id) {
    if (confirm('¿Está seguro de eliminar esta categoría?')) {
        fetch(`/categorias/${id}`, {
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
                alert('Categoría eliminada exitosamente');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la categoría');
        });
    }
};

// Manejador del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoriaForm');
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
                    alert(method === 'PUT' ? 'Categoría actualizada exitosamente' : 'Categoría creada exitosamente');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la categoría');
            });
        });
    }
});
</script>
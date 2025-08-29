<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Roles y Permisos - FoodPoint</title>
    
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
                            <h1 class="text-2xl font-bold text-gray-900">Gestión de Roles y Permisos</h1>
                            <p class="text-sm text-gray-500">Configura los roles y permisos del sistema</p>
                        </div>
                    </div>
                    <button onclick="openAddModal()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                        <i class="fas fa-plus mr-2"></i>
                        Nuevo Rol
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Roles</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-user-cog text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Roles Activos</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['activos'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Roles -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($roles as $rol)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border {{ $rol->activo ? 'border-green-200' : 'border-gray-200' }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 {{ $rol->activo ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg">
                                    <i class="fas fa-user-tag {{ $rol->activo ? 'text-green-600' : 'text-gray-600' }} text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $rol->nombre }}</h3>
                                    <p class="text-sm text-gray-500">{{ $rol->descripcion ?? 'Sin descripción' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $rol->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $rol->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>

                        <!-- Permisos del rol -->
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Permisos:</h4>
                            <div class="flex flex-wrap gap-1">
                                @php
                                    // Simulamos algunos permisos para cada rol
                                    $rolePermisos = [];
                                    switch(strtolower($rol->nombre)) {
                                        case 'administrador':
                                        case 'admin':
                                            $rolePermisos = array_keys($permisos ?? []);
                                            break;
                                        case 'mesero':
                                            $rolePermisos = ['ventas', 'clientes'];
                                            break;
                                        case 'cocinero':
                                            $rolePermisos = ['productos'];
                                            break;
                                        case 'cajero':
                                            $rolePermisos = ['ventas', 'caja'];
                                            break;
                                        default:
                                            $rolePermisos = [];
                                    }
                                @endphp

                                @if(count($rolePermisos) > 0)
                                    @foreach($rolePermisos as $permiso)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                            {{ $permisos[$permiso] ?? ucfirst($permiso) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-400">Sin permisos asignados</span>
                                @endif
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex justify-end space-x-2">
                            <button onclick="editarRol({{ $rol->id }})" 
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button onclick="toggleRol({{ $rol->id }}, {{ $rol->activo ? 'false' : 'true' }})" 
                                    class="px-3 py-1 {{ $rol->activo ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }} rounded hover:opacity-80 transition-colors">
                                <i class="fas {{ $rol->activo ? 'fa-pause' : 'fa-play' }} text-sm"></i>
                            </button>
                            <button onclick="eliminarRol({{ $rol->id }})" 
                                    class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <i class="fas fa-user-cog text-4xl mb-3"></i>
                        <p class="text-lg font-medium">No hay roles configurados</p>
                        <p class="text-sm mt-2">Comienza creando tu primer rol</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Agregar/Editar Rol -->
    <div id="rolModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Nuevo Rol</h2>
            <form id="rolForm">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol</label>
                            <input type="text" name="nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select name="activo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                    </div>

                    <!-- Permisos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Permisos</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($permisos as $key => $permiso)
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="permisos[]" 
                                       value="{{ $key }}" 
                                       id="permiso_{{ $key }}"
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                <label for="permiso_{{ $key }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $permiso }}
                                </label>
                            </div>
                            @endforeach
                        </div>
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
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Nuevo Rol';
            document.getElementById('rolForm').reset();
            document.getElementById('rolModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('rolModal').classList.add('hidden');
        }

        function editarRol(id) {
            document.getElementById('modalTitle').textContent = 'Editar Rol';
            document.getElementById('rolModal').classList.remove('hidden');
            // Implementar lógica para cargar datos del rol
        }

        function toggleRol(id, estado) {
            const action = estado === 'true' ? 'activar' : 'desactivar';
            if (confirm(`¿Está seguro de ${action} este rol?`)) {
                console.log(`Toggle rol ${id} a estado:`, estado);
                // Implementar lógica para cambiar estado
            }
        }

        function eliminarRol(id) {
            if (confirm('¿Está seguro de eliminar este rol? Esta acción no se puede deshacer.')) {
                console.log('Eliminar rol:', id);
                // Implementar lógica de eliminación
            }
        }

        // Manejo del formulario
        document.getElementById('rolForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const permisos = formData.getAll('permisos[]');
            
            console.log('Guardando rol con permisos:', permisos);
            
            closeModal();
            alert('Rol guardado correctamente');
        });

        // Seleccionar/deseleccionar todos los permisos
        function toggleAllPermisos(checked) {
            const checkboxes = document.querySelectorAll('input[name="permisos[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checked;
            });
        }
    </script>
</body>
</html>
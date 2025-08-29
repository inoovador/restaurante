<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Usuarios - FoodPoint</title>
    
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
                            <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
                            <p class="text-sm text-gray-500">Administra los usuarios del sistema</p>
                        </div>
                    </div>
                    <button onclick="openAddModal()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                        <i class="fas fa-user-plus mr-2"></i>
                        Nuevo Usuario
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
                            <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
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
                            <p class="text-sm font-medium text-gray-600">Usuarios Activos</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['activos'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-user-check text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Nuevos (7 días)</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['nuevos'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-user-plus text-purple-600 text-xl"></i>
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
                            <input type="text" id="searchUsuarios" placeholder="Buscar usuarios..." 
                                   class="w-full px-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Rol:</label>
                        <select onchange="filtrarPorRol(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Todos los roles</option>
                            <option value="admin">Administrador</option>
                            <option value="mesero">Mesero</option>
                            <option value="cocinero">Cocinero</option>
                            <option value="cajero">Cajero</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rol
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Último acceso
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="usuariosTableBody">
                            @forelse($usuarios as $usuario)
                            <tr class="hover:bg-gray-50 usuario-row">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            @if($usuario->avatar ?? false)
                                                <img class="h-10 w-10 rounded-full" src="{{ $usuario->avatar }}" alt="{{ $usuario->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                                                    <span class="text-white font-bold">{{ strtoupper(substr($usuario->name ?? 'U', 0, 1)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 usuario-nombre">{{ $usuario->name ?? 'Sin nombre' }}</div>
                                            <div class="text-sm text-gray-500">{{ $usuario->telefono ?? 'Sin teléfono' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $usuario->email }}</div>
                                    @if($usuario->email_verified_at)
                                        <div class="text-sm text-green-500">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Verificado
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            No verificado
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ ($usuario->rol ?? 'mesero') === 'admin' ? 'bg-red-100 text-red-800' : 
                                           (($usuario->rol ?? 'mesero') === 'mesero' ? 'bg-blue-100 text-blue-800' : 
                                           (($usuario->rol ?? 'mesero') === 'cocinero' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst($usuario->rol ?? 'mesero') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($usuario->email_verified_at)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $usuario->updated_at ? \Carbon\Carbon::parse($usuario->updated_at)->diffForHumans() : 'Nunca' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editarUsuario({{ $usuario->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="toggleUsuario({{ $usuario->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                    <button onclick="eliminarUsuario({{ $usuario->id }})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-users text-4xl mb-3"></i>
                                        <p class="text-lg font-medium">No hay usuarios registrados</p>
                                        <p class="text-sm mt-2">Comienza agregando tu primer usuario</p>
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

    <!-- Modal Agregar/Editar Usuario -->
    <div id="usuarioModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Nuevo Usuario</h2>
            <form id="usuarioForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="tel" name="telefono" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <select name="rol" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="mesero">Mesero</option>
                            <option value="cocinero">Cocinero</option>
                            <option value="cajero">Cajero</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
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
        // Búsqueda de usuarios
        document.getElementById('searchUsuarios').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.usuario-row');
            
            rows.forEach(row => {
                const nombre = row.querySelector('.usuario-nombre').textContent.toLowerCase();
                if (nombre.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function filtrarPorRol(rol) {
            const rows = document.querySelectorAll('.usuario-row');
            rows.forEach(row => {
                if (!rol || row.textContent.toLowerCase().includes(rol)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
            document.getElementById('usuarioForm').reset();
            document.getElementById('usuarioModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('usuarioModal').classList.add('hidden');
        }

        function editarUsuario(id) {
            document.getElementById('modalTitle').textContent = 'Editar Usuario';
            document.getElementById('usuarioModal').classList.remove('hidden');
            // Implementar lógica para cargar datos del usuario
        }

        function toggleUsuario(id) {
            if (confirm('¿Cambiar el estado del usuario?')) {
                console.log('Toggle usuario:', id);
            }
        }

        function eliminarUsuario(id) {
            if (confirm('¿Está seguro de eliminar este usuario?')) {
                console.log('Eliminar usuario:', id);
            }
        }

        // Manejo del formulario
        document.getElementById('usuarioForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.querySelector('[name="password"]').value;
            const confirmation = document.querySelector('[name="password_confirmation"]').value;
            
            if (password !== confirmation) {
                alert('Las contraseñas no coinciden');
                return;
            }
            
            closeModal();
            alert('Usuario guardado correctamente');
        });
    </script>
</body>
</html>
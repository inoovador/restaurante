<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Mesas - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #E32636;
            --secondary-color: #4d82bc;
        }
        
        .mesa-card {
            transition: all 0.3s ease;
        }
        
        .mesa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .mesa-disponible {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
        
        .mesa-ocupada {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        }
        
        .mesa-reservada {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        }
        
        .mesa-mantenimiento {
            background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%);
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
                            <h1 class="text-2xl font-bold text-gray-900">Gestión de Mesas</h1>
                            <p class="text-sm text-gray-500">Administra el estado y disponibilidad de las mesas</p>
                        </div>
                    </div>
                    <button onclick="openAddModal()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                        <i class="fas fa-plus mr-2"></i>
                        Nueva Mesa
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Mesas</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-chair text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Disponibles</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['disponibles'] }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Ocupadas</p>
                            <p class="text-3xl font-bold text-red-600">{{ $stats['ocupadas'] }}</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-users text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Reservadas</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $stats['reservadas'] }}</p>
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
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Filtrar por estado:</label>
                        <select onchange="filterMesas(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Todas</option>
                            <option value="disponible">Disponibles</option>
                            <option value="ocupada">Ocupadas</option>
                            <option value="reservada">Reservadas</option>
                            <option value="mantenimiento">En Mantenimiento</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Zona:</label>
                        <select onchange="filterZona(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Todas las zonas</option>
                            <option value="interior">Interior</option>
                            <option value="terraza">Terraza</option>
                            <option value="jardin">Jardín</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center gap-2 ml-auto">
                        <button onclick="viewMode('grid')" class="px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-th"></i>
                        </button>
                        <button onclick="viewMode('list')" class="px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Mesas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div id="mesas-container" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($mesas as $mesa)
                <div class="mesa-card bg-white rounded-xl shadow-lg overflow-hidden cursor-pointer" 
                     data-estado="{{ $mesa->estado }}"
                     data-zona="{{ $mesa->zona }}"
                     onclick="openEditModal({{ $mesa->id }})">
                    <div class="mesa-{{ $mesa->estado }} p-4 text-white">
                        <div class="text-center">
                            <i class="fas fa-chair text-3xl mb-2"></i>
                            <h3 class="text-xl font-bold">Mesa {{ $mesa->numero }}</h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Capacidad:</span>
                                <span class="font-medium">{{ $mesa->capacidad }} personas</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Zona:</span>
                                <span class="font-medium">{{ ucfirst($mesa->zona) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Estado:</span>
                                <span class="font-medium capitalize">{{ $mesa->estado }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t">
                            <select onchange="cambiarEstado({{ $mesa->id }}, this.value)" 
                                    onclick="event.stopPropagation()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                                <option value="disponible" {{ $mesa->estado == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="ocupada" {{ $mesa->estado == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                                <option value="reservada" {{ $mesa->estado == 'reservada' ? 'selected' : '' }}>Reservada</option>
                                <option value="mantenimiento" {{ $mesa->estado == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Agregar/Editar Mesa -->
    <div id="mesaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Nueva Mesa</h2>
            <form id="mesaForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de Mesa</label>
                        <input type="text" name="numero" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad</label>
                        <input type="number" name="capacidad" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Zona</label>
                        <select name="zona" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="interior">Interior</option>
                            <option value="terraza">Terraza</option>
                            <option value="jardin">Jardín</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="estado" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            <option value="disponible">Disponible</option>
                            <option value="ocupada">Ocupada</option>
                            <option value="reservada">Reservada</option>
                            <option value="mantenimiento">En Mantenimiento</option>
                        </select>
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
        // Filtrar mesas por estado
        function filterMesas(estado) {
            const mesas = document.querySelectorAll('.mesa-card');
            mesas.forEach(mesa => {
                if (!estado || mesa.dataset.estado === estado) {
                    mesa.style.display = '';
                } else {
                    mesa.style.display = 'none';
                }
            });
        }
        
        // Filtrar por zona
        function filterZona(zona) {
            const mesas = document.querySelectorAll('.mesa-card');
            mesas.forEach(mesa => {
                if (!zona || mesa.dataset.zona === zona) {
                    mesa.style.display = '';
                } else {
                    mesa.style.display = 'none';
                }
            });
        }
        
        // Cambiar estado de mesa
        function cambiarEstado(mesaId, nuevoEstado) {
            fetch(`/mesas/${mesaId}/estado`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ estado: nuevoEstado })
            })
            .then(response => response.json())
            .then(data => {
                location.reload(); // Recargar para ver cambios
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Modal functions
        function openAddModal() {
            document.getElementById('mesaModal').classList.remove('hidden');
        }
        
        function openEditModal(id) {
            // Aquí implementar lógica de edición
            console.log('Editar mesa:', id);
        }
        
        function closeModal() {
            document.getElementById('mesaModal').classList.add('hidden');
        }
        
        // Vista grid/lista
        function viewMode(mode) {
            const container = document.getElementById('mesas-container');
            if (mode === 'list') {
                container.className = 'space-y-2';
            } else {
                container.className = 'grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4';
            }
        }
    </script>
</body>
</html>
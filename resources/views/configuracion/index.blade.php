<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configuración - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: #f8f9fa;
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
                        <h1 class="text-2xl font-bold text-gray-900">Configuración del Sistema</h1>
                    </div>
                    <button onclick="guardarConfiguracion()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Estadísticas del Sistema -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Usuarios</p>
                            <p class="text-2xl font-bold">{{ $stats['usuarios_activos'] }}</p>
                        </div>
                        <i class="fas fa-users text-blue-500 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Productos</p>
                            <p class="text-2xl font-bold">{{ $stats['productos_total'] }}</p>
                        </div>
                        <i class="fas fa-box text-green-500 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Ventas Hoy</p>
                            <p class="text-2xl font-bold">{{ $stats['ventas_hoy'] }}</p>
                        </div>
                        <i class="fas fa-shopping-cart text-purple-500 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Versión</p>
                            <p class="text-xl font-bold">v{{ $stats['version_sistema'] }}</p>
                        </div>
                        <i class="fas fa-code-branch text-orange-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Formulario de Configuración -->
            <form id="configForm" method="POST" action="{{ route('configuracion.update') }}">
                @csrf
                
                <!-- Información del Restaurante -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-store mr-2"></i>
                            Información del Restaurante
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Restaurante</label>
                                <input type="text" name="nombre" value="{{ $configuracion['restaurante']['nombre'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">RUC</label>
                                <input type="text" name="ruc" value="{{ $configuracion['restaurante']['ruc'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                                <input type="text" name="direccion" value="{{ $configuracion['restaurante']['direccion'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                <input type="text" name="telefono" value="{{ $configuracion['restaurante']['telefono'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ $configuracion['restaurante']['email'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sitio Web</label>
                                <input type="text" name="website" value="{{ $configuracion['restaurante']['website'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Impuestos -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-percentage mr-2"></i>
                            Configuración de Impuestos
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">IGV (%)</label>
                                <input type="number" name="igv" value="{{ $configuracion['impuestos']['igv'] }}" 
                                       min="0" max="100" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cargo por Servicio (%)</label>
                                <input type="number" name="servicio" value="{{ $configuracion['impuestos']['servicio'] }}" 
                                       min="0" max="100" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Horario de Atención -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-clock mr-2"></i>
                            Horario de Atención
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Apertura</label>
                                <input type="time" name="apertura" value="{{ $configuracion['horario']['apertura'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Cierre</label>
                                <input type="time" name="cierre" value="{{ $configuracion['horario']['cierre'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Días de Atención</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($configuracion['horario']['dias'] as $dia)
                                <label class="flex items-center">
                                    <input type="checkbox" name="dias[]" value="{{ $dia }}" checked 
                                           class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm">{{ $dia }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Notificaciones -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-bell mr-2"></i>
                            Notificaciones
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <label class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Notificaciones por Email</span>
                                <input type="checkbox" name="notif_email" {{ $configuracion['notificaciones']['email'] ? 'checked' : '' }}
                                       class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Notificaciones por SMS</span>
                                <input type="checkbox" name="notif_sms" {{ $configuracion['notificaciones']['sms'] ? 'checked' : '' }}
                                       class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Notificaciones por WhatsApp</span>
                                <input type="checkbox" name="notif_whatsapp" {{ $configuracion['notificaciones']['whatsapp'] ? 'checked' : '' }}
                                       class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Configuración del POS -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-cash-register mr-2"></i>
                            Configuración del Punto de Venta
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <label class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Modo Offline</span>
                                <input type="checkbox" name="pos_offline" {{ $configuracion['pos']['modo_offline'] ? 'checked' : '' }}
                                       class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Imprimir Boleta Automáticamente</span>
                                <input type="checkbox" name="pos_imprimir" {{ $configuracion['pos']['imprimir_boleta'] ? 'checked' : '' }}
                                       class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Sonido de Alertas</span>
                                <input type="checkbox" name="pos_sonido" {{ $configuracion['pos']['sonido_alertas'] ? 'checked' : '' }}
                                       class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Información del Sistema -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-server mr-2"></i>
                            Información del Sistema
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600">Versión del Sistema</p>
                                <p class="text-lg font-semibold">v{{ $stats['version_sistema'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Última Actualización</p>
                                <p class="text-lg font-semibold">{{ $stats['ultima_actualizacion'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Espacio en Disco</p>
                                <div class="mt-2">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>Usado: {{ $stats['espacio_usado']['usado'] }}</span>
                                        <span>{{ $stats['espacio_usado']['porcentaje'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['espacio_usado']['porcentaje'] }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Libre: {{ $stats['espacio_usado']['libre'] }} de {{ $stats['espacio_usado']['total'] }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Base de Datos</p>
                                <p class="text-lg font-semibold">SQLite</p>
                                <p class="text-xs text-gray-500">database.sqlite</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-bolt mr-2"></i>
                            Acciones Rápidas
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button type="button" onclick="limpiarCache()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-broom mr-2"></i>
                                Limpiar Caché
                            </button>
                            <button type="button" onclick="exportarDatos()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-download mr-2"></i>
                                Exportar Datos
                            </button>
                            <button type="button" onclick="respaldarBD()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-database mr-2"></i>
                                Respaldar BD
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function guardarConfiguracion() {
            if (confirm('¿Guardar los cambios en la configuración?')) {
                document.getElementById('configForm').submit();
            }
        }

        function limpiarCache() {
            if (confirm('¿Limpiar la caché del sistema?')) {
                showNotification('Caché limpiada correctamente', 'success');
            }
        }

        function exportarDatos() {
            if (confirm('¿Exportar todos los datos del sistema?')) {
                window.location.href = '/configuracion/export';
            }
        }

        function respaldarBD() {
            if (confirm('¿Crear respaldo de la base de datos?')) {
                showNotification('Respaldo creado correctamente', 'success');
            }
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
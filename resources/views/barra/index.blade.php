<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barra - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #E32636;
            --secondary-color: #4d82bc;
        }
        
        .pedido-card {
            transition: all 0.3s ease;
        }
        
        .pedido-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .pedido-urgente {
            animation: pulse-red 2s infinite;
        }
        
        @keyframes pulse-red {
            0%, 100% { border-color: #ef4444; }
            50% { border-color: #dc2626; }
        }
        
        .timer {
            font-family: 'Courier New', monospace;
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
                            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-cocktail text-blue-600 mr-3"></i>
                                Barra
                            </h1>
                            <p class="text-sm text-gray-500">Panel de preparación de bebidas</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900" id="currentTime"></div>
                            <div class="text-sm text-gray-500">Hora actual</div>
                        </div>
                        <button onclick="actualizarPedidos()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pendientes</p>
                            <p class="text-3xl font-bold text-red-600">{{ $stats['pendientes'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-clock text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">En Preparación</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $stats['en_preparacion'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-blender text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completados Hoy</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['completados_hoy'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos Pendientes -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-900">Pedidos Pendientes</h2>
                <p class="text-sm text-gray-500">Bebidas por preparar</p>
            </div>

            @if($pedidosPendientes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($pedidosPendientes as $pedido)
                @php
                    $horaCreacion = \Carbon\Carbon::parse($pedido->hora_pedido);
                    $tiempoTranscurrido = $horaCreacion->diffInMinutes(now());
                    $esUrgente = $tiempoTranscurrido > 15;
                @endphp
                
                <div class="pedido-card bg-white rounded-lg shadow-lg overflow-hidden {{ $esUrgente ? 'pedido-urgente border-2' : 'border' }}">
                    <!-- Header del pedido -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-lg">#{{ $pedido->venta_id }}</h3>
                                <p class="text-sm opacity-90">
                                    @if($pedido->mesa_numero)
                                        Mesa {{ $pedido->mesa_numero }} - {{ ucfirst($pedido->mesa_zona) }}
                                    @else
                                        {{ ucfirst($pedido->tipo_pedido) }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="timer text-lg font-mono">{{ sprintf('%02d:%02d', floor($tiempoTranscurrido / 60), $tiempoTranscurrido % 60) }}</div>
                                <p class="text-xs opacity-80">{{ $horaCreacion->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del pedido -->
                    <div class="p-4">
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $pedido->producto_nombre }}</h4>
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                    x{{ $pedido->cantidad }}
                                </span>
                            </div>
                            
                            @if($pedido->notas)
                            <div class="bg-yellow-50 border border-yellow-200 rounded p-2 mb-3">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    {{ $pedido->notas }}
                                </p>
                            </div>
                            @endif

                            <!-- Tiempo de preparación estimado -->
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <i class="fas fa-stopwatch mr-1"></i>
                                Tiempo est.: {{ $pedido->tiempo_preparacion ?? 5 }} min
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex gap-2">
                            <button onclick="iniciarPreparacion({{ $pedido->id }})" 
                                    class="flex-1 px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm font-medium">
                                <i class="fas fa-play mr-1"></i>
                                Iniciar
                            </button>
                            <button onclick="marcarListo({{ $pedido->id }})" 
                                    class="flex-1 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                <i class="fas fa-check mr-1"></i>
                                Listo
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-cocktail text-4xl mb-3"></i>
                    <p class="text-lg font-medium">No hay pedidos pendientes</p>
                    <p class="text-sm mt-2">Los nuevos pedidos aparecerán aquí automáticamente</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Catálogo de Bebidas (Referencia) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Catálogo de Bebidas</h3>
                    <p class="text-sm text-gray-500">Referencia rápida de productos</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                        @foreach($bebidas->take(12) as $bebida)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="h-12 w-12 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-glass-martini-alt text-blue-600"></i>
                            </div>
                            <h4 class="font-medium text-sm text-gray-900 mb-1">{{ $bebida->nombre }}</h4>
                            <p class="text-xs text-gray-500">{{ $bebida->categoria_nombre }}</p>
                            @if($bebida->tiempo_preparacion)
                            <p class="text-xs text-blue-600 mt-1">{{ $bebida->tiempo_preparacion }} min</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Confirmar Acción</h2>
            <p id="modalMessage" class="text-gray-600 mb-6"></p>
            <div class="flex justify-end gap-3">
                <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button id="confirmButton" onclick="confirmarAccion()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <script>
        let accionPendiente = null;
        let pedidoIdPendiente = null;

        // Actualizar reloj cada segundo
        function actualizarReloj() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
        }
        
        // Actualizar timers de pedidos
        function actualizarTimers() {
            document.querySelectorAll('.timer').forEach(timer => {
                // Aquí se podría actualizar cada timer individualmente
            });
        }

        // Inicializar y actualizar cada segundo
        setInterval(() => {
            actualizarReloj();
            actualizarTimers();
        }, 1000);
        
        // Llamar inmediatamente
        actualizarReloj();

        function iniciarPreparacion(pedidoId) {
            accionPendiente = 'iniciar';
            pedidoIdPendiente = pedidoId;
            document.getElementById('modalTitle').textContent = 'Iniciar Preparación';
            document.getElementById('modalMessage').textContent = '¿Desea marcar este pedido como en preparación?';
            document.getElementById('confirmButton').className = 'px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700';
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function marcarListo(pedidoId) {
            accionPendiente = 'listo';
            pedidoIdPendiente = pedidoId;
            document.getElementById('modalTitle').textContent = 'Marcar como Listo';
            document.getElementById('modalMessage').textContent = '¿Confirma que este pedido está listo para servir?';
            document.getElementById('confirmButton').className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            accionPendiente = null;
            pedidoIdPendiente = null;
        }

        function confirmarAccion() {
            if (accionPendiente === 'iniciar') {
                // Implementar lógica para iniciar preparación
                console.log('Iniciando preparación del pedido:', pedidoIdPendiente);
            } else if (accionPendiente === 'listo') {
                // Implementar lógica para marcar como listo
                console.log('Marcando como listo el pedido:', pedidoIdPendiente);
            }
            
            closeModal();
            
            // Simular actualización
            setTimeout(() => {
                actualizarPedidos();
            }, 500);
        }

        function actualizarPedidos() {
            // Mostrar indicador de carga
            const btn = event ? event.target : document.querySelector('[onclick="actualizarPedidos()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualizando...';
            btn.disabled = true;
            
            // Simular actualización
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        // Auto-actualizar cada 30 segundos
        setInterval(() => {
            console.log('Auto-actualizando pedidos...');
            // En producción, aquí harías una petición AJAX
        }, 30000);

        // Notificación de sonido para pedidos urgentes
        function checkUrgentOrders() {
            const urgentOrders = document.querySelectorAll('.pedido-urgente');
            if (urgentOrders.length > 0) {
                // Aquí podrías reproducir un sonido de notificación
                console.log(`¡Atención! ${urgentOrders.length} pedido(s) urgente(s)`);
            }
        }

        // Verificar pedidos urgentes cada minuto
        setInterval(checkUrgentOrders, 60000);
    </script>
</body>
</html>
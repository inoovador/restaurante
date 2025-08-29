<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cocina - FoodPoint</title>
    
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
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .pedido-urgente {
            animation: pulse-red 2s infinite;
        }
        
        .pedido-preparando {
            animation: pulse-yellow 2s infinite;
        }
        
        @keyframes pulse-red {
            0%, 100% { border-color: #ef4444; }
            50% { border-color: #dc2626; }
        }
        
        @keyframes pulse-yellow {
            0%, 100% { border-color: #f59e0b; }
            50% { border-color: #d97706; }
        }
        
        .timer {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .priority-alta { border-left: 5px solid #ef4444; }
        .priority-media { border-left: 5px solid #f59e0b; }
        .priority-baja { border-left: 5px solid #10b981; }
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
                                <i class="fas fa-utensils text-red-600 mr-3"></i>
                                Cocina
                            </h1>
                            <p class="text-sm text-gray-500">Panel de preparación de alimentos</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900" id="currentTime"></div>
                            <div class="text-sm text-gray-500">Hora actual</div>
                        </div>
                        <button onclick="actualizarPedidos()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                            <i class="fas fa-fire text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Listos</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['listos'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-bell text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completados</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['completados_hoy'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Pedidos Pendientes -->
                <div>
                    <div class="mb-4">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-clock text-red-600 mr-2"></i>
                            Pedidos Pendientes ({{ $pedidosPendientes->count() }})
                        </h2>
                        <p class="text-sm text-gray-500">Ordenes esperando preparación</p>
                    </div>

                    <div class="space-y-4 max-h-screen overflow-y-auto">
                        @forelse($pedidosPendientes as $pedido)
                        @php
                            $horaCreacion = \Carbon\Carbon::parse($pedido->hora_pedido);
                            $tiempoTranscurrido = $horaCreacion->diffInMinutes(now());
                            $esUrgente = $tiempoTranscurrido > 20;
                            $priority = $tiempoTranscurrido > 30 ? 'alta' : ($tiempoTranscurrido > 15 ? 'media' : 'baja');
                        @endphp
                        
                        <div class="pedido-card bg-white rounded-lg shadow-lg border-l-4 priority-{{ $priority }} {{ $esUrgente ? 'pedido-urgente' : '' }}">
                            <!-- Header del pedido -->
                            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-4">
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
                                        <div class="timer text-lg">{{ sprintf('%02d:%02d', floor($tiempoTranscurrido / 60), $tiempoTranscurrido % 60) }}</div>
                                        <p class="text-xs opacity-80">{{ $horaCreacion->format('H:i') }}</p>
                                        @if($esUrgente)
                                            <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full mt-1 inline-block">
                                                ¡URGENTE!
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido del pedido -->
                            <div class="p-4">
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-900">{{ $pedido->producto_nombre }}</h4>
                                        <span class="bg-red-100 text-red-800 text-sm px-3 py-1 rounded-full font-medium">
                                            x{{ $pedido->cantidad }}
                                        </span>
                                    </div>
                                    
                                    @if($pedido->notas)
                                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-3">
                                        <p class="text-sm text-yellow-800 font-medium">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            Notas: {{ $pedido->notas }}
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Información adicional -->
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-stopwatch mr-1"></i>
                                            Tiempo est.: {{ $pedido->tiempo_preparacion ?? 15 }} min
                                        </div>
                                        <div class="flex items-center">
                                            <div class="h-2 w-2 rounded-full {{ $priority === 'alta' ? 'bg-red-500' : ($priority === 'media' ? 'bg-yellow-500' : 'bg-green-500') }} mr-1"></div>
                                            {{ ucfirst($priority) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex gap-2">
                                    <button onclick="iniciarPreparacion({{ $pedido->id }})" 
                                            class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">
                                        <i class="fas fa-play mr-2"></i>
                                        Iniciar Preparación
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-lg shadow p-8 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-utensils text-4xl mb-3"></i>
                                <p class="text-lg font-medium">No hay pedidos pendientes</p>
                                <p class="text-sm mt-2">¡Buen trabajo! No hay ordenes esperando</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pedidos En Preparación -->
                <div>
                    <div class="mb-4">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-fire text-yellow-600 mr-2"></i>
                            En Preparación ({{ $pedidosEnPreparacion->count() }})
                        </h2>
                        <p class="text-sm text-gray-500">Ordenes que se están cocinando</p>
                    </div>

                    <div class="space-y-4 max-h-screen overflow-y-auto">
                        @forelse($pedidosEnPreparacion as $pedido)
                        @php
                            $horaCreacion = \Carbon\Carbon::parse($pedido->hora_pedido);
                            $tiempoTranscurrido = $horaCreacion->diffInMinutes(now());
                            $tiempoEstimado = $pedido->tiempo_preparacion ?? 15;
                        @endphp
                        
                        <div class="pedido-card pedido-preparando bg-white rounded-lg shadow-lg border-2 border-yellow-500">
                            <!-- Header del pedido -->
                            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-4">
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
                                        <div class="timer text-lg">{{ sprintf('%02d:%02d', floor($tiempoTranscurrido / 60), $tiempoTranscurrido % 60) }}</div>
                                        <p class="text-xs opacity-80">Iniciado: {{ $horaCreacion->format('H:i') }}</p>
                                        <div class="mt-1">
                                            <div class="bg-white bg-opacity-20 rounded-full h-2">
                                                <div class="bg-white h-2 rounded-full" style="width: {{ min(100, ($tiempoTranscurrido / $tiempoEstimado) * 100) }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido del pedido -->
                            <div class="p-4">
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold text-gray-900">{{ $pedido->producto_nombre }}</h4>
                                        <span class="bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full font-medium">
                                            x{{ $pedido->cantidad }}
                                        </span>
                                    </div>
                                    
                                    @if($pedido->notas)
                                    <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-3">
                                        <p class="text-sm text-blue-800 font-medium">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            Notas: {{ $pedido->notas }}
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Estado de progreso -->
                                    <div class="bg-gray-50 rounded p-3 mb-3">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600">Progreso estimado:</span>
                                            <span class="font-medium text-yellow-600">
                                                {{ min(100, round(($tiempoTranscurrido / $tiempoEstimado) * 100)) }}%
                                            </span>
                                        </div>
                                        <div class="mt-2 bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ min(100, ($tiempoTranscurrido / $tiempoEstimado) * 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex gap-2">
                                    <button onclick="marcarListo({{ $pedido->id }})" 
                                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                        <i class="fas fa-check mr-2"></i>
                                        Marcar Listo
                                    </button>
                                    <button onclick="pausarPreparacion({{ $pedido->id }})" 
                                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-lg shadow p-8 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-fire text-4xl mb-3"></i>
                                <p class="text-lg font-medium">No hay pedidos en preparación</p>
                                <p class="text-sm mt-2">Los pedidos en cocción aparecerán aquí</p>
                            </div>
                        </div>
                        @endforelse
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

    <!-- Notificación de Pedido Listo -->
    <div id="pedidoListoNotification" class="hidden fixed top-4 right-4 bg-green-600 text-white p-4 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-bell mr-2"></i>
            <span>¡Pedido listo para servir!</span>
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
        
        // Actualizar progreso de pedidos
        function actualizarProgresos() {
            document.querySelectorAll('.timer').forEach((timer, index) => {
                // Actualizar timers individuales si es necesario
            });
        }

        // Inicializar y actualizar cada segundo
        setInterval(() => {
            actualizarReloj();
            actualizarProgresos();
        }, 1000);
        
        // Llamar inmediatamente
        actualizarReloj();

        function iniciarPreparacion(pedidoId) {
            accionPendiente = 'iniciar';
            pedidoIdPendiente = pedidoId;
            document.getElementById('modalTitle').textContent = 'Iniciar Preparación';
            document.getElementById('modalMessage').textContent = '¿Desea comenzar la preparación de este pedido?';
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

        function pausarPreparacion(pedidoId) {
            accionPendiente = 'pausar';
            pedidoIdPendiente = pedidoId;
            document.getElementById('modalTitle').textContent = 'Pausar Preparación';
            document.getElementById('modalMessage').textContent = '¿Desea pausar la preparación de este pedido?';
            document.getElementById('confirmButton').className = 'px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700';
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            accionPendiente = null;
            pedidoIdPendiente = null;
        }

        function confirmarAccion() {
            if (accionPendiente === 'iniciar') {
                console.log('Iniciando preparación del pedido:', pedidoIdPendiente);
            } else if (accionPendiente === 'listo') {
                console.log('Marcando como listo el pedido:', pedidoIdPendiente);
                mostrarNotificacionListo();
            } else if (accionPendiente === 'pausar') {
                console.log('Pausando preparación del pedido:', pedidoIdPendiente);
            }
            
            closeModal();
            
            // Simular actualización
            setTimeout(() => {
                actualizarPedidos();
            }, 500);
        }

        function mostrarNotificacionListo() {
            const notification = document.getElementById('pedidoListoNotification');
            notification.classList.remove('hidden');
            
            // Reproducir sonido (opcional)
            // new Audio('/sounds/notification.wav').play();
            
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        function actualizarPedidos() {
            const btn = event ? event.target : document.querySelector('[onclick="actualizarPedidos()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualizando...';
            btn.disabled = true;
            
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        // Auto-actualizar cada 30 segundos
        setInterval(() => {
            console.log('Auto-actualizando pedidos de cocina...');
            // En producción, hacer petición AJAX aquí
        }, 30000);

        // Revisar pedidos urgentes
        function checkUrgentOrders() {
            const urgentOrders = document.querySelectorAll('.pedido-urgente');
            if (urgentOrders.length > 0) {
                console.log(`¡ATENCIÓN! ${urgentOrders.length} pedido(s) urgente(s) en cocina`);
                // Aquí podrías reproducir una alerta sonora
            }
        }

        // Verificar cada minuto
        setInterval(checkUrgentOrders, 60000);
        
        // Verificar al cargar
        setTimeout(checkUrgentOrders, 1000);
    </script>
</body>
</html>
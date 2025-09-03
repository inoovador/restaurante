<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FoodPoint - Dashboard</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        .sidebar {
            background: white;
            border-right: 1px solid #e5e7eb;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.05);
        }
        
        .sidebar-item {
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 2px 0;
            position: relative;
        }
        
        .sidebar-item:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(59, 130, 246, 0.12) 100%);
            color: #1E40AF;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
            border-radius: 10px;
        }
        
        .sidebar-item.active {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
            border-radius: 10px;
        }
        
        .sidebar-item.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 28px;
            background: #1D4ED8;
            border-radius: 2px;
        }
        
        .tag-orange {
            background: #f59e0b;
            color: white;
        }
        
        /* Mejoras adicionales para los contadores */
        .notification-badge {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            animation: pulse-subtle 2s infinite;
        }
        
        @keyframes pulse-subtle {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* Efectos hover mejorados para los íconos */
        .sidebar-item i {
            transition: all 0.3s ease;
        }
        
        .sidebar-item:hover i {
            transform: scale(1.1);
        }
        
        .sidebar-item.active i {
            filter: drop-shadow(0 2px 4px rgba(255, 255, 255, 0.3));
        }
        
        /* Estilos optimizados para imágenes de productos */
        .product-image-container {
            position: relative;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: all 0.3s ease;
        }
        
        .product-image:hover {
            transform: scale(1.05);
        }
        
        .product-image-fallback {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            padding: 8px;
        }
        
        /* Contenedores específicos por contexto */
        .dashboard-product-image {
            width: 100%;
            height: 96px; /* h-24 = 96px */
            border-radius: 8px;
            background: #f1f5f9;
        }
        
        .cart-item-image {
            width: 48px; /* w-12 = 48px */
            height: 48px; /* h-12 = 48px */
            border-radius: 8px;
        }
        
        .product-card-image {
            width: 100%;
            height: 120px;
            border-radius: 12px;
        }
        
        /* Placeholder para imágenes sin cargar */
        .image-placeholder {
            background: linear-gradient(135deg, #e2e8f0 0%, #f1f5f9 100%);
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        /* Alertas de stock */
        .stock-warning {
            position: absolute;
            top: 4px;
            right: 4px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            animation: pulse-warning 2s infinite;
            z-index: 10;
        }
        
        .stock-critical {
            background: #dc2626 !important;
            box-shadow: 0 0 10px rgba(220, 38, 38, 0.5);
        }
        
        .stock-low-text {
            color: #f59e0b !important;
            font-weight: 600;
        }
        
        .stock-critical-text {
            color: #ef4444 !important;
            font-weight: 700;
        }
        
        @keyframes pulse-warning {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.1); }
        }
        
        /* Estilos para búsqueda inteligente */
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 50;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .suggestion-item {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.2s;
        }
        
        .suggestion-item:hover {
            background: #f8f9fa;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        /* Modal de promociones */
        .promo-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }
        
        .promo-content {
            background: white;
            padding: 24px;
            border-radius: 16px;
            max-width: 400px;
            margin: 20px;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .tag-green {
            background: #10b981;
            color: white;
        }
        
        .tag-canceled {
            background: #ef4444;
            color: white;
        }
        
        .order-badge {
            background: #007aff;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .product-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        
        .btn-primary {
            background: #10b981;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
        
        .search-box {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 16px;
            width: 100%;
        }
        
        .search-box:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .tab-active {
            color: #3b82f6;
            border-bottom: 2px solid #3b82f6;
        }
        
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Fix para el contenedor principal */
        .main-content {
            height: calc(100vh - 64px);
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Izquierdo -->
        <div class="sidebar w-64 flex flex-col text-gray-700 fixed h-full z-10">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.jpeg" alt="FoodPoint Logo" class="w-10 h-10 rounded-lg object-cover">
                    <span class="text-xl font-semibold text-gray-900">FoodPoint</span>
                </div>
            </div>
            
            <!-- Restaurante Actual -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="text-xs text-gray-500 mb-2">Restaurante actual</div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900">FoodPoint Sucursal 1</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </div>
                </div>
                <div class="text-xs text-gray-500 mt-1">Av. Principal 123, Centro</div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="flex-1 px-4 py-4 overflow-y-auto">
                <ul class="space-y-2">
                    <li>
                        <a href="/dashboard" class="sidebar-item active flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-th-large w-5 text-blue-600"></i>
                            <span class="text-blue-600 font-medium">Panel Principal</span>
                        </a>
                    </li>
                    <li>
                        <a href="/ventas" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm bg-green-50 border-l-4 border-green-500">
                            <i class="fas fa-cash-register w-5 text-green-600"></i>
                            <span class="font-semibold text-green-700">Punto de Venta</span>
                            <span class="ml-auto bg-green-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse">Nuevo</span>
                        </a>
                    </li>
                    <li>
                        <a href="/ventas" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-shopping-bag w-5"></i>
                            <span>Órdenes</span>
                        </a>
                    </li>
                    <li>
                        <a href="/inventario" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-boxes w-5"></i>
                            <span>Inventario</span>
                            <span class="ml-auto text-xs notification-badge text-white px-2 py-0.5 rounded-full font-semibold">2</span>
                        </a>
                    </li>
                    <li>
                        <a href="/productos" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-utensils w-5"></i>
                            <span>Productos</span>
                            <span class="ml-auto text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold shadow-sm">{{ $stats['productos'] ?? 10 }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="/categorias" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-tags w-5"></i>
                            <span>Categorías</span>
                            <span class="ml-auto text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-semibold shadow-sm">{{ $stats['categorias'] ?? 5 }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="/mesas" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-chair w-5"></i>
                            <span>Mesas</span>
                        </a>
                    </li>
                    <li>
                        <a href="/clientes" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-users w-5"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="/pedidos" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-list-alt w-5"></i>
                            <span>Lista de Pedidos</span>
                        </a>
                    </li>
                    <li>
                        <a href="/reportes" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Análisis</span>
                        </a>
                    </li>
                </ul>
                
                <!-- Sección de Configuración -->
                <div class="mt-8 pt-4 border-t border-gray-200">
                    <ul class="space-y-2">
                        <li>
                            <a href="/configuracion" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                                <i class="fas fa-cog w-5"></i>
                                <span>Configuración</span>
                            </a>
                        </li>
                        <li>
                            <a href="/ayuda" class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm">
                                <i class="fas fa-question-circle w-5"></i>
                                <span>Centro de Ayuda</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Cambiar Cuenta -->
            <div class="p-4 border-t border-gray-200">
                <div class="text-xs text-gray-500 mb-3">Cambiar cuenta</div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center shadow-md">
                        <span class="text-sm font-semibold text-white">AD</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">Administrador</div>
                        <div class="text-xs text-gray-500">admin@foodpoint.com</div>
                    </div>
                </div>
                
                <!-- Botón Agregar Cuenta -->
                <button class="w-full mt-3 flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Agregar cuenta</span>
                </button>
                
                <!-- Cerrar Sesión -->
                <form action="{{ route('logout') }}" method="POST" class="w-full mt-3">
                    @csrf
                    <button type="submit" class="w-full text-center text-red-400 text-sm hover:text-red-300 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Área de Contenido Principal -->
        <div class="flex-1 flex flex-col bg-gray-50 ml-64 h-screen overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-between w-full">
                        <h1 class="text-2xl font-semibold text-gray-900">Cola de órdenes</h1>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-500">{{ date('H:i') }}</span>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                            <span class="text-sm text-gray-500">Ver Todo</span>
                        </div>
                    </div>
                </div>
            </header>
            
            
            <!-- Sección de Estadísticas y Análisis -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Panel de Estadísticas -->
                <div class="flex-1 p-6 overflow-y-auto h-full scrollbar-thin" style="max-height: calc(100vh - 130px);">
                    <!-- Barra de Filtros y Periodo -->
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <select class="px-4 py-2 border rounded-lg text-sm">
                                <option>Últimos 7 días</option>
                                <option>Últimos 30 días</option>
                                <option>Este mes</option>
                                <option>Este año</option>
                            </select>
                            <button class="px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Personalizar fecha
                            </button>
                        </div>
                        <button class="p-2 bg-white rounded-lg border hover:bg-gray-50">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    
                    <!-- Tarjetas de Métricas Principales -->
                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-500 text-sm">Ventas Totales</span>
                                <i class="fas fa-chart-line text-emerald-500"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($stats['ventas_hoy'], 2, '.', ',') }}</p>
                            <p class="text-xs {{ $stats['porcentaje_cambio'] >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-2">
                                <i class="fas fa-arrow-{{ $stats['porcentaje_cambio'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($stats['porcentaje_cambio']) }}% vs ayer
                            </p>
                        </div>
                        
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-500 text-sm">Órdenes Activas</span>
                                <i class="fas fa-shopping-bag text-blue-500"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['ordenes_activas'] }}</p>
                            <p class="text-xs text-blue-600 mt-2">
                                <i class="fas fa-clock"></i> {{ $stats['ordenes_preparacion'] }} en preparación
                            </p>
                        </div>
                        
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-500 text-sm">Clientes Hoy</span>
                                <i class="fas fa-users text-blue-500"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['clientes_hoy'] }}</p>
                            <p class="text-xs {{ $stats['porcentaje_clientes'] >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-2">
                                <i class="fas fa-arrow-{{ $stats['porcentaje_clientes'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($stats['porcentaje_clientes']) }}% {{ $stats['porcentaje_clientes'] >= 0 ? 'más' : 'menos' }} que ayer
                            </p>
                        </div>
                        
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-500 text-sm">Ticket Promedio</span>
                                <i class="fas fa-receipt text-orange-500"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($stats['ticket_promedio'], 2, '.', ',') }}</p>
                            <p class="text-xs text-orange-600 mt-2">
                                <i class="fas fa-minus"></i> Sin cambios
                            </p>
                        </div>
                    </div>
                    
                    <!-- Gráficos y Análisis -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <!-- Gráfico de Ventas -->
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Tendencia de Ventas</h3>
                                <button class="text-sm text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </div>
                            <div class="relative" style="height: 250px;">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Gráfico de Categorías -->
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Ventas por Categoría</h3>
                                <button class="text-sm text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </div>
                            <div class="relative" style="height: 250px;">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de Productos Más Vendidos -->
                    <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Productos Más Vendidos</h3>
                            <a href="/productos" class="text-sm text-blue-600 hover:text-blue-700">Ver todos</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">#</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Producto</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Categoría</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Vendidos</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Ingresos</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Tendencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productos_vendidos as $index => $producto)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 text-sm">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4 text-sm font-medium">{{ $producto->producto }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ $producto->categoria }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $producto->vendidos }}</td>
                                        <td class="py-3 px-4 text-sm font-medium">S/ {{ number_format($producto->ingresos, 2, '.', ',') }}</td>
                                        <td class="py-3 px-4">
                                            @php
                                                $tendencia = rand(-5, 15);
                                            @endphp
                                            <span class="{{ $tendencia >= 0 ? 'text-emerald-600' : 'text-red-600' }} text-sm">
                                                <i class="fas fa-arrow-{{ $tendencia >= 0 ? 'up' : 'down' }}"></i> {{ abs($tendencia) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Salud del Sistema -->
                    <div class="grid grid-cols-3 gap-4 pb-6">
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Salud del Sistema</span>
                                <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">CPU</span>
                                    <span class="text-xs font-medium">23%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: 23%"></div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Memoria</span>
                                    <span class="text-xs font-medium">45%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Base de Datos</span>
                                <i class="fas fa-database text-blue-500"></i>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Consultas/seg</span>
                                    <span class="text-xs font-medium">127</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Conexiones activas</span>
                                    <span class="text-xs font-medium">18</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Tamaño</span>
                                    <span class="text-xs font-medium">2.3 GB</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Actividad Reciente</span>
                                <i class="fas fa-clock text-gray-500"></i>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Última venta</span>
                                    <span class="text-xs font-medium">{{ $stats['ultima_venta'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Último pedido</span>
                                    <span class="text-xs font-medium">Hace 30 seg</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Usuarios en línea</span>
                                    <span class="text-xs font-medium">{{ $stats['usuarios_online'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panel de Productos Disponibles -->
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Productos Disponibles</h3>
                            <span class="text-sm text-gray-500">({{ $productos->count() }} productos)</span>
                        </div>
                        
                        <!-- Búsqueda inteligente -->
                        <div class="relative mb-4">
                            <input type="text" 
                                   id="productSearch" 
                                   placeholder="🔍 Buscar productos..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   autocomplete="off"
                                   oninput="buscarProductosInteligente(this.value)"
                                   onfocus="mostrarSugerencias(true)"
                                   onblur="setTimeout(() => mostrarSugerencias(false), 200)">
                            
                            <!-- Sugerencias -->
                            <div id="searchSuggestions" class="search-suggestions hidden"></div>
                        </div>
                        
                        <!-- Filtros por categoría -->
                        <div class="flex gap-2 mb-4 overflow-x-auto">
                            <button onclick="filtrarPorCategoria('')" 
                                    class="category-filter active px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full whitespace-nowrap">
                                Todos
                            </button>
                            @php
                                $categorias = $productos->groupBy('categoria_nombre');
                            @endphp
                            @foreach($categorias as $categoria => $productos_cat)
                                <button onclick="filtrarPorCategoria('{{ $categoria }}')" 
                                        class="category-filter px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-full whitespace-nowrap hover:bg-gray-200">
                                    {{ $categoria }} ({{ $productos_cat->count() }})
                                </button>
                            @endforeach
                        </div>
                        @if($productos->isEmpty())
                            <div class="text-center py-8">
                                <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">No hay productos disponibles</p>
                                <a href="/productos" class="text-blue-500 hover:text-blue-700 text-sm mt-2 inline-block">
                                    Ir a gestión de productos →
                                </a>
                            </div>
                        @else
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($productos->take(8) as $producto)
                            <div class="bg-white border rounded-lg p-3 hover:shadow-lg transition-shadow cursor-pointer product-card relative"
                                 onclick="agregarProductoConPromo({
                                    id: {{ $producto->id }},
                                    nombre: '{{ $producto->nombre }}',
                                    precio_venta: {{ $producto->precio_venta }},
                                    categoria: '{{ $producto->categoria_nombre }}',
                                    imagen: '{{ $producto->imagen }}',
                                    stock: {{ $producto->stock ?? 0 }}
                                 })">
                                
                                @php
                                    $stock = $producto->stock ?? 0;
                                    $stockMinimo = $producto->stock_minimo ?? 10;
                                @endphp
                                
                                <!-- Alerta de stock bajo -->
                                @if($stock <= 5)
                                    <div class="stock-warning {{ $stock == 0 ? 'stock-critical' : '' }}" title="{{ $stock == 0 ? 'Sin stock' : 'Stock crítico: ' . $stock }}">
                                        @if($stock == 0)
                                            !
                                        @else
                                            {{ $stock }}
                                        @endif
                                    </div>
                                @elseif($stock <= $stockMinimo)
                                    <div class="stock-warning" style="background: #f59e0b;" title="Stock bajo: {{ $stock }}">
                                        {{ $stock }}
                                    </div>
                                @endif
                                
                                <div class="dashboard-product-image product-image-container">
                                    @php
                                        $imagenUrl = null;
                                        if ($producto->imagen_url) {
                                            $imagenUrl = $producto->imagen_url;
                                        } elseif ($producto->imagen && file_exists(public_path($producto->imagen))) {
                                            $imagenUrl = '/' . $producto->imagen;
                                        } elseif ($producto->imagen && file_exists(public_path('uploads/productos/' . basename($producto->imagen)))) {
                                            $imagenUrl = '/uploads/productos/' . basename($producto->imagen);
                                        }
                                    @endphp
                                    
                                    @if($imagenUrl)
                                        <img src="{{ $imagenUrl }}" 
                                             alt="{{ $producto->nombre }}" 
                                             class="product-image {{ $stock == 0 ? 'opacity-50' : '' }}"
                                             onerror="this.outerHTML='<div class=&quot;image-placeholder dashboard-product-image&quot;><i class=&quot;fas fa-utensils&quot;></i></div>'">
                                    @else
                                        <div class="image-placeholder dashboard-product-image {{ $stock == 0 ? 'opacity-50' : '' }}">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <h4 class="text-sm font-medium text-gray-900 mb-1 truncate {{ $stock == 0 ? 'opacity-50' : '' }}">{{ $producto->nombre }}</h4>
                                <p class="text-xs text-gray-500 mb-1">{{ $producto->categoria_nombre }}</p>
                                
                                <!-- Stock visual -->
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-bold text-blue-600">S/ {{ number_format($producto->precio_venta, 2) }}</p>
                                    <p class="text-xs 
                                        @if($stock == 0) stock-critical-text 
                                        @elseif($stock <= 5) stock-critical-text 
                                        @elseif($stock <= $stockMinimo) stock-low-text 
                                        @else text-green-600 
                                        @endif">
                                        @if($stock == 0)
                                            Sin stock
                                        @else
                                            Stock: {{ $stock }}
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="flex items-center justify-center">
                                    <button class="w-8 h-8 {{ $stock == 0 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }} rounded-full flex items-center justify-center transition"
                                            {{ $stock == 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Cart Sidebar -->
                <div class="w-96 bg-white border-l flex-shrink-0">
                    <div class="h-full flex flex-col overflow-hidden">
                        <!-- Tabs para Carrito e Historial -->
                        <div class="border-b">
                            <div class="flex">
                                <button onclick="mostrarTab('carrito')" id="tab-carrito" class="flex-1 px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Carrito Actual
                                </button>
                                <button onclick="mostrarTab('historial')" id="tab-historial" class="flex-1 px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-history mr-2"></i>
                                    Historial
                                </button>
                            </div>
                        </div>
                        
                        <!-- Contenido Tab Carrito -->
                        <div id="contenido-carrito" class="flex-1 flex flex-col overflow-hidden">
                            <!-- Encabezado del Carrito -->
                            <div class="p-4 border-b">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-semibold text-gray-900">Historial de Pedidos</h2>
                                    <button class="text-gray-400 hover:text-gray-600" onclick="limpiarTodosArticulos()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Tabs de tipo de orden -->
                                <div class="flex items-center gap-1 mb-4">
                                    <button onclick="cambiarTipoOrden('local')" id="btn-local" class="px-3 py-1 text-sm rounded text-gray-600 hover:text-blue-600 tipo-orden">Comer aquí</button>
                                    <button onclick="cambiarTipoOrden('llevar')" id="btn-llevar" class="px-3 py-1 text-sm rounded text-white bg-blue-600 tipo-orden activo">Para llevar</button>
                                    <button onclick="cambiarTipoOrden('entrega')" id="btn-entrega" class="px-3 py-1 text-sm rounded text-gray-600 hover:text-blue-600 tipo-orden">Entrega</button>
                                </div>
                            </div>
                        
                        <!-- Información del Cliente -->
                        <div class="px-4 py-3 border-b">
                            <h3 class="text-sm font-medium mb-3 text-gray-900">Información del cliente</h3>
                            <div class="space-y-3">
                                <input type="text" id="nombre-cliente" placeholder="Nombre del cliente" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <select id="mesa-select" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccionar mesa</option>
                                    @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">Mesa {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <button onclick="limpiarTodosArticulos()" class="w-full mt-4 text-blue-600 text-sm hover:text-blue-700 transition-colors">
                                Limpiar todos los artículos
                            </button>
                        </div>
                        
                        <!-- Artículos del Pedido -->
                        <div class="flex-1 px-4 py-3 overflow-y-auto">
                            <div id="cartItems" class="space-y-3">
                                <!-- Los items se cargarán dinámicamente aquí -->
                            </div>
                            
                            <div id="emptyCartMessage" class="text-center text-gray-400 py-8">
                                <i class="fas fa-shopping-cart text-4xl mb-3 opacity-30"></i>
                                <p class="text-sm">No hay artículos en el carrito</p>
                            </div>
                        </div>
                            
                        </div>
                        
                        <!-- Pie del Carrito -->
                        <div class="px-6 py-4 border-t">
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span>Subtotal</span>
                                    <span class="font-medium subtotal-amount">S/ 980.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Descuento (10%)</span>
                                    <span class="font-medium descuento-amount">- S/ 98.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Impuesto (11%)</span>
                                    <span class="font-medium impuesto-amount">S/ 97.02</span>
                                </div>
                                <div class="pt-2 border-t flex justify-between">
                                    <span class="font-semibold">Total</span>
                                    <span class="font-bold text-lg total-amount">S/ 979.02</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="text" placeholder="Ingresar código promo" class="flex-1 px-3 py-2 border rounded-lg text-sm">
                                <button class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200">Aplicar</button>
                            </div>
                            <button class="w-full mt-4 btn-primary" onclick="procesarPago()">
                                Procesar pago
                            </button>
                        </div>
                    </div>
                    
                    <!-- Contenido Tab Historial -->
                    <div id="contenido-historial" class="hidden flex-1 flex flex-col overflow-hidden">
                        <div class="p-6 border-b">
                            <h2 class="text-xl font-semibold">Historial de Pedidos</h2>
                            <p class="text-sm text-gray-500 mt-1">Últimos 10 pedidos</p>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4" id="lista-historial">
                            <!-- Se llenará dinámicamente con JavaScript -->
                            <div class="text-center text-gray-400 py-8">
                                <i class="fas fa-history text-5xl mb-4"></i>
                                <p>Cargando historial...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <!-- Scripts para Gráficos y Funcionalidad -->
    <script>
        // Configuración de gráficos con Chart.js
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando sistema...');
            
            // Cargar carrito guardado
            cargarCarritoLocal();
            
            // Si el carrito está vacío, cargar productos de prueba para demostrar funcionalidad
            if (carrito.length === 0) {
                cargarProductosPrueba();
            }
            
            // Verificar conexión con base de datos
            verificarConexionBD();
            cargarHistorialInicial();
            
            // Verificar que Chart.js esté cargado
            if (typeof Chart === 'undefined') {
                console.error('Chart.js no está cargado');
                return;
            }
            
            // Gráfico de Ventas
            const salesCtx = document.getElementById('salesChart');
            if (salesCtx) {
                console.log('Creando gráfico de ventas...');
                @php
                    $ventas_semana_datos = isset($stats['ventas_semana']) ? $stats['ventas_semana'] : [80, 85, 90, 88, 96, 125, 110];
                @endphp
                new Chart(salesCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                        datasets: [{
                            label: 'Ventas',
                            data: {!! json_encode($ventas_semana_datos) !!},
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'S/ ' + (value/1000).toFixed(0) + 'k';
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Gráfico de ventas creado exitosamente');
            } else {
                console.error('No se encontró el elemento salesChart');
            }
            
            // Gráfico de Categorías
            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx) {
                console.log('Creando gráfico de categorías...');
                @php
                    $labels = [];
                    $data = [];
                    
                    // Verificar si existe la variable y tiene datos
                    if (isset($ventas_categoria) && $ventas_categoria->isNotEmpty()) {
                        $labels = $ventas_categoria->pluck('nombre')->toArray();
                        $data = $ventas_categoria->pluck('total')->toArray();
                    }
                    
                    // Si no hay datos, usar datos de ejemplo
                    if (empty($labels)) {
                        $labels = ['Entradas', 'Platos Principales', 'Postres', 'Bebidas', 'Otros'];
                        $data = [25, 35, 15, 20, 5];
                    }
                @endphp
                new Chart(categoryCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($labels) !!},
                        datasets: [{
                            data: {!! json_encode($data) !!},
                            backgroundColor: [
                                'rgb(59, 130, 246)',
                                'rgb(16, 185, 129)',
                                'rgb(245, 158, 11)',
                                'rgb(139, 92, 246)',
                                'rgb(107, 114, 128)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Gráfico de categorías creado exitosamente');
            } else {
                console.error('No se encontró el elemento categoryChart');
            }
        });
        
        // Funciones del Dashboard
        let tipoOrden = 'llevar';
        let carrito = [];
        let todosLosProductos = @json($productos ?? []); // Para búsqueda inteligente
        let promociones = {
            'Hamburguesa Clásica': {
                combo: 'Coca Cola',
                descuento: 0.10,
                mensaje: '¿Agregar una Coca Cola con 10% de descuento?'
            },
            'Alitas BBQ': {
                combo: 'Cerveza Corona',
                descuento: 0.15,
                mensaje: '¿Agregar cerveza Corona con 15% de descuento?'
            },
            'Nachos con Queso': {
                combo: 'Coca Cola',
                descuento: 0.12,
                mensaje: '¿Agregar Coca Cola con 12% de descuento?'
            }
        };
        
        function cambiarPeriodo(periodo) {
            console.log('Cambiando periodo a:', periodo);
            // Recargar datos según el periodo seleccionado
            window.location.href = '/dashboard?periodo=' + periodo;
        }
        
        function abrirSelectorFecha() {
            const fechaInicio = prompt('Ingrese fecha de inicio (YYYY-MM-DD):');
            const fechaFin = prompt('Ingrese fecha de fin (YYYY-MM-DD):');
            if (fechaInicio && fechaFin) {
                window.location.href = '/dashboard?inicio=' + fechaInicio + '&fin=' + fechaFin;
            }
        }
        
        function descargarReporte() {
            if (confirm('¿Desea descargar el reporte de ventas?')) {
                const periodo = document.getElementById('periodo-select').value;
                // Crear una nueva ventana para la descarga
                const url = '/dashboard/export?periodo=' + periodo;
                const link = document.createElement('a');
                link.href = url;
                link.download = 'reporte_ventas.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
        
        function verTodasOrdenes() {
            window.location.href = '/ventas';
        }
        
        function limpiarCarrito() {
            if (carrito.length === 0) {
                mostrarNotificacion('El carrito ya está vacío', 'info');
                return;
            }
            
            if (confirm('¿Está seguro de limpiar todos los artículos del carrito?')) {
                carrito = [];
                localStorage.removeItem('carrito_actual');
                actualizarCarritoUI();
                actualizarTotales();
                mostrarNotificacion('Carrito limpiado', 'info');
            }
        }
        
        function guardarCarritoLocal() {
            localStorage.setItem('carrito_actual', JSON.stringify(carrito));
        }
        
        function cargarCarritoLocal() {
            const carritoGuardado = localStorage.getItem('carrito_actual');
            if (carritoGuardado) {
                try {
                    carrito = JSON.parse(carritoGuardado);
                    actualizarCarritoUI();
                    actualizarTotales();
                } catch (e) {
                    console.error('Error cargando carrito:', e);
                }
            }
        }
        
        function aplicarPromo() {
            const codigo = document.getElementById('codigo-promo').value;
            if (codigo) {
                alert('Código promocional aplicado: ' + codigo);
                // Aquí se aplicaría el descuento real
            } else {
                alert('Por favor ingrese un código promocional');
            }
        }
        
        function procesarPago() {
            const nombreCliente = document.getElementById('nombre-cliente') ? document.getElementById('nombre-cliente').value : 'Cliente General';
            const mesa = document.getElementById('mesa-select') ? document.getElementById('mesa-select').value : '1';
            
            if (carrito.length === 0) {
                alert('El carrito está vacío');
                return;
            }
            
            // Calcular totales
            let subtotal = 0;
            carrito.forEach(item => {
                const precio = item.precio_venta || item.precio || 0;
                const cantidad = item.quantity || item.cantidad || 1;
                subtotal += precio * cantidad;
            });
            const descuento = subtotal * 0.10; // 10% descuento como en modelo44.jpg
            const impuesto = (subtotal - descuento) * 0.11; // 11% impuesto como en modelo44.jpg
            const total = subtotal - descuento + impuesto;
            
            if (confirm(`¿Procesar el pago de S/ ${total.toFixed(2)}?`)) {
                // Enviar orden a la base de datos
                guardarVenta({
                    cliente: nombreCliente,
                    mesa_id: mesa,
                    tipo_orden: tipoOrden,
                    items: carrito,
                    subtotal: subtotal,
                    descuento: descuento,
                    impuesto: impuesto,
                    total: total
                });
            }
        }
        
        function guardarVenta(datos) {
            // Mostrar loading
            mostrarNotificacion('Procesando venta...', 'info');
            
            fetch('/api/ventas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('✅ Venta registrada exitosamente - ' + data.numero_venta, 'success');
                    
                    // Generar boleta con datos del servidor
                    if (data.boleta) {
                        generarBoleta(data.venta_id, data.numero_venta, datos, data.boleta);
                    } else {
                        generarBoleta(data.venta_id, data.numero_venta, datos);
                    }
                    
                    // Limpiar carrito
                    carrito = [];
                    localStorage.removeItem('carrito_actual');
                    actualizarCarritoUI();
                    
                    // Actualizar historial
                    cargarHistorial();
                    
                    // Actualizar estadísticas
                    actualizarEstadisticas();
                } else {
                    mostrarNotificacion('❌ Error: ' + (data.message || 'Error al registrar la venta'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Guardar en localStorage como respaldo
                guardarVentaLocal(datos);
                mostrarNotificacion('⚠️ Venta guardada localmente', 'warning');
                limpiarCarrito();
                cargarHistorial();
            });
        }
        
        function generarBoleta(ventaId, numeroVenta, datos, boletaData = null) {
            // Usar datos de boleta del servidor si están disponibles
            const boleta = boletaData || datos;
            
            // Crear contenido de la boleta con formato profesional
            const boletaHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: 'Courier New', monospace; max-width: 400px; margin: 0 auto; padding: 20px; }
                        .header { text-align: center; margin-bottom: 20px; }
                        .header h2 { margin: 0; font-size: 24px; }
                        .header p { margin: 5px 0; }
                        .info { margin: 15px 0; }
                        .info div { margin: 5px 0; }
                        .detalle { margin: 20px 0; }
                        .detalle table { width: 100%; border-collapse: collapse; }
                        .detalle th { text-align: left; border-bottom: 1px solid #000; padding: 5px 0; }
                        .detalle td { padding: 5px 0; }
                        .totales { margin: 20px 0; border-top: 2px solid #000; padding-top: 10px; }
                        .totales div { display: flex; justify-content: space-between; margin: 5px 0; }
                        .total-final { font-size: 18px; font-weight: bold; border-top: 1px solid #000; padding-top: 10px; margin-top: 10px; }
                        .footer { text-align: center; margin-top: 30px; }
                        .footer p { margin: 5px 0; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>FOODPOINT RESTAURANT</h2>
                        <p>RUC: 20123456789</p>
                        <p>Av. Principal 123, Centro</p>
                        <p>Tel: (01) 234-5678</p>
                        <hr>
                        <h3>BOLETA DE VENTA ELECTRÓNICA</h3>
                        <p><strong>${numeroVenta}</strong></p>
                    </div>
                    
                    <div class="info">
                        <div><strong>Fecha:</strong> ${boleta.fecha || new Date().toLocaleString('es-PE')}</div>
                        <div><strong>Cliente:</strong> ${boleta.cliente || datos.cliente || 'Cliente General'}</div>
                        <div><strong>Atención:</strong> ${boleta.mesa || (datos.mesa_id ? 'Mesa ' + datos.mesa_id : 'Para llevar')}</div>
                        <div><strong>Cajero:</strong> Admin</div>
                    </div>
                    
                    <div class="detalle">
                        <table>
                            <thead>
                                <tr>
                                    <th>Cant.</th>
                                    <th>Descripción</th>
                                    <th style="text-align: right;">P.Unit</th>
                                    <th style="text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${(boleta.items || datos.items).map(item => `
                                    <tr>
                                        <td>${item.cantidad || item.quantity || 1}</td>
                                        <td>${item.nombre || item.name || 'Producto'}</td>
                                        <td style="text-align: right;">S/ ${(item.precio_unitario || item.precio_venta || 0).toFixed(2)}</td>
                                        <td style="text-align: right;">S/ ${(item.subtotal || ((item.cantidad || item.quantity || 1) * (item.precio_unitario || item.precio_venta || 0))).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="totales">
                        <div><span>SUBTOTAL:</span> <span>S/ ${(boleta.subtotal || datos.subtotal).toFixed(2)}</span></div>
                        <div><span>DESCUENTO (10%):</span> <span>-S/ ${(boleta.descuento || datos.descuento).toFixed(2)}</span></div>
                        <div><span>OP. GRAVADA:</span> <span>S/ ${((boleta.subtotal || datos.subtotal) - (boleta.descuento || datos.descuento)).toFixed(2)}</span></div>
                        <div><span>IGV (11%):</span> <span>S/ ${(boleta.impuesto || datos.impuesto).toFixed(2)}</span></div>
                        <div class="total-final">
                            <span>IMPORTE TOTAL:</span> 
                            <span>S/ ${(boleta.total || datos.total).toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <p>¡Gracias por su preferencia!</p>
                        <p>Conserve su comprobante</p>
                        <p>No se aceptan devoluciones</p>
                        <hr>
                        <p style="font-size: 10px;">Autorizado mediante Resolución N° 0180050000XXX/SUNAT</p>
                        <p style="font-size: 10px;">Representación impresa de la Boleta Electrónica</p>
                        <p style="font-size: 10px;">Consulte en www.foodpoint.pe</p>
                    </div>
                </body>
                </html>
            `;
            
            // Abrir ventana para imprimir
            const ventanaImpresion = window.open('', '_blank', 'width=400,height=700');
            ventanaImpresion.document.write(boletaHTML);
            ventanaImpresion.document.close();
            
            // Auto-imprimir después de cargar
            setTimeout(() => {
                ventanaImpresion.print();
            }, 500);
        }
        
        function actualizarEstadisticas() {
            // Actualizar estadísticas en tiempo real
            fetch('/dashboard', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                console.log('Estadísticas actualizadas');
            });
        }
        
        function guardarVentaLocal(datos) {
            const ventasLocales = JSON.parse(localStorage.getItem('ventas') || '[]');
            datos.id = Date.now();
            datos.fecha = new Date().toISOString();
            ventasLocales.push(datos);
            localStorage.setItem('ventas', JSON.stringify(ventasLocales));
        }
        
        function mostrarTab(tab) {
            const tabCarrito = document.getElementById('tab-carrito');
            const tabHistorial = document.getElementById('tab-historial');
            const contenidoCarrito = document.getElementById('contenido-carrito');
            const contenidoHistorial = document.getElementById('contenido-historial');
            
            if (tab === 'carrito') {
                tabCarrito.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                tabCarrito.classList.remove('text-gray-500');
                tabHistorial.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                tabHistorial.classList.add('text-gray-500');
                contenidoCarrito.classList.remove('hidden');
                contenidoHistorial.classList.add('hidden');
            } else {
                tabHistorial.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                tabHistorial.classList.remove('text-gray-500');
                tabCarrito.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                tabCarrito.classList.add('text-gray-500');
                contenidoHistorial.classList.remove('hidden');
                contenidoCarrito.classList.add('hidden');
                cargarHistorial();
            }
        }
        
        function cargarHistorial() {
            const listaHistorial = document.getElementById('lista-historial');
            
            // Intentar cargar del servidor
            fetch('/api/ventas/historial', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                mostrarHistorial(data.ventas || []);
            })
            .catch(error => {
                console.error('Error cargando historial:', error);
                // Cargar del localStorage como respaldo
                const ventasLocales = JSON.parse(localStorage.getItem('ventas') || '[]');
                mostrarHistorial(ventasLocales);
            });
        }
        
        function mostrarHistorial(ventas) {
            const listaHistorial = document.getElementById('lista-historial');
            
            if (ventas.length === 0) {
                listaHistorial.innerHTML = `
                    <div class="text-center text-gray-400 py-8">
                        <i class="fas fa-inbox text-5xl mb-4"></i>
                        <p>No hay pedidos en el historial</p>
                    </div>
                `;
                return;
            }
            
            listaHistorial.innerHTML = ventas.slice(-10).reverse().map(venta => `
                <div class="bg-gray-50 rounded-lg p-4 mb-3">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="text-sm font-semibold">Pedido #${venta.numero_venta || venta.id}</span>
                            <span class="ml-2 text-xs bg-green-100 text-green-600 px-2 py-1 rounded">
                                ${venta.estado || 'Completado'}
                            </span>
                        </div>
                        <span class="text-xs text-gray-500">
                            ${new Date(venta.fecha || venta.created_at).toLocaleString('es-PE')}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p><strong>Cliente:</strong> ${venta.cliente || 'Cliente General'}</p>
                        <p><strong>Mesa:</strong> ${venta.mesa_id || 'N/A'}</p>
                        <p><strong>Items:</strong> ${venta.items ? venta.items.length : 0} productos</p>
                    </div>
                    
                    <!-- Mostrar items con imágenes si están disponibles -->
                    ${venta.items && venta.items.length > 0 ? `
                        <div class="mt-2 space-y-1">
                            ${venta.items.slice(0, 3).map(item => `
                                <div class="flex items-center gap-2 text-xs">
                                    ${item.imagen ? `
                                        <img src="/storage/${item.imagen}" 
                                             class="w-6 h-6 rounded object-cover" 
                                             onerror="this.src='${getImageByCategory(item.categoria)}'">
                                    ` : `
                                        <img src="${getImageByCategory(item.categoria)}" 
                                             class="w-6 h-6 rounded object-cover" 
                                             alt="${item.nombre}">
                                    `}
                                    <span class="text-gray-700">${item.cantidad}x ${item.nombre}</span>
                                    <span class="ml-auto text-gray-900 font-medium">S/ ${(item.subtotal || (item.cantidad * item.precio_unitario)).toFixed(2)}</span>
                                </div>
                            `).join('')}
                            ${venta.items.length > 3 ? `
                                <div class="text-xs text-gray-500 pl-8">
                                    ... y ${venta.items.length - 3} productos más
                                </div>
                            ` : ''}
                        </div>
                    ` : ''}
                    
                    <div class="mt-2 pt-2 border-t flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-900">
                            Total: S/ ${parseFloat(venta.total).toFixed(2)}
                        </span>
                        <button onclick="verDetalleVenta('${venta.id}', ${JSON.stringify(venta).replace(/"/g, '&quot;')})" 
                                class="text-xs text-blue-600 hover:text-blue-700">
                            Ver detalles →
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        function verDetalleVenta(ventaId) {
            alert(`Ver detalles de la venta #${ventaId}`);
            // Aquí puedes implementar un modal o expandir los detalles
        }
        
        function cambiarTipoOrden(tipo) {
            tipoOrden = tipo;
            
            // Actualizar estilos de botones
            document.querySelectorAll('.tipo-orden').forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600', 'pb-1', 'font-medium');
                btn.classList.add('text-gray-500');
            });
            
            const btnActivo = document.getElementById('btn-' + tipo);
            btnActivo.classList.remove('text-gray-500');
            btnActivo.classList.add('text-blue-600', 'border-b-2', 'border-blue-600', 'pb-1', 'font-medium');
            
            // Mostrar/ocultar selección de mesa
            const mesaSelect = document.getElementById('mesa-select');
            if (tipo === 'local') {
                mesaSelect.required = true;
                mesaSelect.parentElement.style.display = 'block';
            } else {
                mesaSelect.required = false;
                if (tipo === 'entrega') {
                    // Podría agregar campos de dirección
                }
            }
        }
        
        // El carrito se inicializa vacío y se llena dinámicamente
        // carrito se carga desde localStorage o productos de prueba en DOMContentLoaded
        
        function incrementarItem(itemId) {
            const qtyElement = document.getElementById('qty-item-' + itemId);
            let currentQty = parseInt(qtyElement.textContent);
            currentQty++;
            qtyElement.textContent = currentQty;
            
            // Actualizar en el carrito
            const item = carrito.find(i => i.id === itemId);
            if (item) {
                item.cantidad = currentQty;
            }
            actualizarTotales();
        }
        
        function decrementarItem(itemId) {
            const qtyElement = document.getElementById('qty-item-' + itemId);
            let currentQty = parseInt(qtyElement.textContent);
            if (currentQty > 1) {
                currentQty--;
                qtyElement.textContent = currentQty;
                
                // Actualizar en el carrito
                const item = carrito.find(i => i.id === itemId);
                if (item) {
                    item.cantidad = currentQty;
                }
            } else {
                // Eliminar item si cantidad es 0
                const itemElement = document.querySelector('[data-item-id="' + itemId + '"]');
                if (itemElement) {
                    itemElement.remove();
                }
                carrito = carrito.filter(i => i.id !== itemId);
            }
            actualizarTotales();
        }
        
        function actualizarTotales() {
            let subtotal = 0;
            carrito.forEach(item => {
                const precio = item.precio_venta || item.precio || 0;
                const cantidad = item.quantity || item.cantidad || 1;
                subtotal += precio * cantidad;
            });
            
            const descuento = subtotal * 0.10; // 10% descuento como en el modelo
            const impuesto = (subtotal - descuento) * 0.11; // 11% impuesto como en el modelo  
            const total = subtotal - descuento + impuesto;
            
            // Actualizar en la UI si existen los elementos
            const subtotalElement = document.querySelector('.subtotal-amount');
            const descuentoElement = document.querySelector('.descuento-amount');
            const impuestoElement = document.querySelector('.impuesto-amount');
            const totalElement = document.querySelector('.total-amount');
            
            if (subtotalElement) subtotalElement.textContent = 'S/ ' + subtotal.toFixed(2);
            if (descuentoElement) descuentoElement.textContent = '- S/ ' + descuento.toFixed(2);
            if (impuestoElement) impuestoElement.textContent = 'S/ ' + impuesto.toFixed(2);
            if (totalElement) totalElement.textContent = 'S/ ' + total.toFixed(2);
        }
        
        // Función para crear items del carrito según el modelo
        function crearItemCarrito(producto) {
            const precio = producto.precio_venta || producto.precio || 0;
            const cantidad = producto.quantity || producto.cantidad || 1;
            const total = precio * cantidad;
            
            // Determinar la URL de la imagen con prioridad al API
            let imagenUrl;
            if (producto.imagen_url && producto.imagen_url !== 'null' && producto.imagen_url !== null) {
                // Usar la URL procesada por el API del backend
                imagenUrl = producto.imagen_url;
            } else if (producto.imagen && producto.imagen !== 'null' && producto.imagen !== null) {
                // Fallback para compatibilidad con datos locales
                if (producto.imagen.startsWith('/storage/') || producto.imagen.startsWith('/uploads/')) {
                    imagenUrl = producto.imagen;
                } else if (producto.imagen.startsWith('http')) {
                    imagenUrl = producto.imagen;
                } else {
                    imagenUrl = '/uploads/productos/' + producto.imagen;
                }
            } else {
                // Usar imagen por categoría como fallback
                imagenUrl = getImageByCategory(producto.categoria || producto.categoria_nombre);
            }
            
            return `
                <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg bg-white" data-item-id="${producto.id}">
                    <!-- Imagen del producto optimizada -->
                    <div class="cart-item-image product-image-container flex-shrink-0">
                        ${imagenUrl ? 
                            `<img src="${imagenUrl}" 
                                  alt="${producto.nombre}" 
                                  class="product-image"
                                  onerror="this.outerHTML='<div class=\\"image-placeholder cart-item-image\\"><i class=\\"fas fa-utensils\\"></i></div>'">` :
                            `<div class="image-placeholder cart-item-image">
                                <i class="fas fa-utensils"></i>
                             </div>`
                        }
                    </div>
                    
                    <!-- Información del producto -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-sm mb-1">${producto.nombre}</h4>
                        <p class="text-xs text-gray-500 mb-0.5">Variante: Original</p>
                        <p class="text-xs text-gray-500 mb-1">Adición: ${producto.adiccion || 'Limón'}</p>
                        <p class="text-sm font-bold text-gray-900">S/ ${total.toFixed(2)}</p>
                    </div>
                    
                    <!-- Controles de cantidad -->
                    <div class="flex items-center gap-2 ml-auto">
                        <button onclick="restarCantidad(${producto.id})" class="w-7 h-7 rounded-full bg-gray-100 border flex items-center justify-center hover:bg-gray-200 text-gray-600">
                            <span class="text-sm font-medium">−</span>
                        </button>
                        <span class="w-8 text-center text-sm font-medium text-gray-900">${cantidad}</span>
                        <button onclick="sumarCantidad(${producto.id})" class="w-7 h-7 rounded-full bg-gray-100 border flex items-center justify-center hover:bg-gray-200 text-gray-600">
                            <span class="text-sm font-medium">+</span>
                        </button>
                    </div>
                </div>
            `;
        }
        
        function actualizarCarritoUI() {
            const cartItems = document.getElementById('cartItems') || document.getElementById('cart-items');
            const emptyMessage = document.getElementById('emptyCartMessage');
            
            if (!cartItems) return;
            
            if (carrito.length === 0) {
                cartItems.innerHTML = '';
                if (emptyMessage) emptyMessage.style.display = 'block';
            } else {
                if (emptyMessage) emptyMessage.style.display = 'none';
                cartItems.innerHTML = carrito.map(producto => crearItemCarrito(producto)).join('');
            }
            
            actualizarTotales();
        }
        
        function sumarCantidad(productoId) {
            const item = carrito.find(p => p.id === productoId);
            if (item) {
                item.quantity = (item.quantity || 1) + 1;
                guardarCarritoLocal();
                actualizarCarritoUI();
                actualizarTotales();
                mostrarNotificacion(`+1 ${item.nombre}`, 'info');
            }
        }
        
        function restarCantidad(productoId) {
            const itemIndex = carrito.findIndex(p => p.id === productoId);
            if (itemIndex !== -1) {
                const currentQuantity = carrito[itemIndex].quantity || 1;
                if (currentQuantity > 1) {
                    carrito[itemIndex].quantity -= 1;
                    mostrarNotificacion(`-1 ${carrito[itemIndex].nombre}`, 'info');
                } else {
                    const nombreProducto = carrito[itemIndex].nombre;
                    carrito.splice(itemIndex, 1);
                    mostrarNotificacion(`${nombreProducto} eliminado`, 'warning');
                }
                guardarCarritoLocal();
                actualizarCarritoUI();
            }
        }
        
        function limpiarTodosArticulos() {
            if (carrito.length === 0) {
                mostrarNotificacion('El carrito ya está vacío', 'info');
                return;
            }
            
            if (confirm('¿Limpiar todos los artículos del carrito?')) {
                carrito = [];
                localStorage.removeItem('carrito_actual');
                actualizarCarritoUI();
                mostrarNotificacion('Carrito limpiado', 'success');
            }
        }
        
        async function agregarProducto(producto) {
            try {
                // Usar API del backend para agregar producto con detalles completos
                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: producto.id,
                        cantidad: 1
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Actualizar carrito con datos completos del servidor (incluyendo imágenes)
                    carrito = data.carrito;
                    
                    // Actualizar interfaz con nuevos datos
                    actualizarCarritoUI();
                    
                    // Mostrar notificación
                    mostrarNotificacion(`${data.producto.nombre} agregado al carrito`, 'success');
                    
                    // Sincronizar con localStorage
                    localStorage.setItem('carrito', JSON.stringify(carrito));
                } else {
                    mostrarNotificacion(data.message || 'Error al agregar producto', 'error');
                }
            } catch (error) {
                console.error('Error API:', error);
                // Fallback al método original si falla la API
                const existingItem = carrito.find(item => item.id === producto.id);
                
                if (existingItem) {
                    existingItem.quantity = (existingItem.quantity || 1) + 1;
                    mostrarNotificacion(`+1 ${producto.nombre}`, 'info');
                } else {
                    const nuevoItem = {
                        ...producto,
                        quantity: 1,
                        adiccion: 'Ninguna',
                        imagen_url: producto.imagen || null
                    };
                    carrito.push(nuevoItem);
                    mostrarNotificacion(`${producto.nombre} agregado al carrito`, 'success');
                }
                
                guardarCarritoLocal();
                actualizarCarritoUI();
            }
        }
        
        function cargarProductosPrueba() {
            // Usar productos reales de la base de datos
            const productosDB = @json($productos ?? []);
            
            if (productosDB.length >= 2) {
                // Agregar los primeros 2 productos de la BD como demostración
                const productosPrueba = [
                    {
                        id: productosDB[0].id,
                        nombre: productosDB[0].nombre,
                        precio_venta: parseFloat(productosDB[0].precio_venta),
                        categoria: productosDB[0].categoria_nombre,
                        adiccion: "Limón",
                        quantity: 2
                    },
                    {
                        id: productosDB[1].id,
                        nombre: productosDB[1].nombre,
                        precio_venta: parseFloat(productosDB[1].precio_venta),
                        categoria: productosDB[1].categoria_nombre,
                        adiccion: "",
                        quantity: 3
                    }
                ];
                
                carrito = productosPrueba;
                guardarCarritoLocal();
                actualizarCarritoUI();
                mostrarNotificacion('Productos cargados desde la base de datos', 'info');
            } else {
                // Si no hay productos en BD, usar demostración
                const productosPrueba = [
                    {
                        id: 999,
                        nombre: "Shrimp fried spicy sauce",
                        precio_venta: 85.00,
                        adiccion: "Limón",
                        quantity: 2
                    },
                    {
                        id: 998,
                        nombre: "Spicy shrimp with rice", 
                        precio_venta: 105.00,
                        adiccion: "",
                        quantity: 3
                    }
                ];
                
                carrito = productosPrueba;
                guardarCarritoLocal();
                actualizarCarritoUI();
                mostrarNotificacion('Productos de demostración cargados', 'info');
            }
        }
        
        function cargarHistorialInicial() {
            // Intentar cargar historial del servidor
            fetch('/api/ventas/historial', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en servidor');
                }
                return response.json();
            })
            .then(data => {
                console.log('Historial cargado desde BD:', data);
                if (data.success) {
                    console.log(`Total de ventas en historial: ${data.ventas ? data.ventas.length : 0}`);
                }
            })
            .catch(error => {
                console.error('Error cargando historial:', error);
                mostrarNotificacion('⚠️ Usando almacenamiento local (BD no disponible)', 'warning');
            });
        }
        
        function verificarConexionBD() {
            fetch('/api/ventas/historial', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (response.ok) {
                    console.log('✅ Conexión con base de datos activa');
                    return response.json();
                } else {
                    console.log('❌ Base de datos sin conexión');
                    throw new Error('Sin conexión');
                }
            })
            .then(data => {
                // Actualizar indicador visual de conexión
                actualizarIndicadorConexion(true);
            })
            .catch(() => {
                console.log('❌ Base de datos sin conexión');
                actualizarIndicadorConexion(false);
            });
        }
        
        function actualizarIndicadorConexion(conectado) {
            const indicador = document.getElementById('db-status');
            if (!indicador) {
                // Crear indicador si no existe
                const nuevoIndicador = document.createElement('div');
                nuevoIndicador.id = 'db-status';
                nuevoIndicador.className = 'fixed bottom-4 left-4 px-3 py-2 rounded-full text-xs font-medium flex items-center gap-2 z-50';
                document.body.appendChild(nuevoIndicador);
            }
            
            // Ocultar indicador de BD
            const elemento = document.getElementById('db-status');
            if (elemento) {
                elemento.style.display = 'none';
            }
        }
        
        function mostrarNotificacion(mensaje, tipo = 'info') {
            const colores = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            };
            
            const notificacion = document.createElement('div');
            notificacion.className = `fixed top-4 right-4 ${colores[tipo]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-500`;
            notificacion.textContent = mensaje;
            document.body.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.style.opacity = '0';
                setTimeout(() => notificacion.remove(), 500);
            }, 3000);
        }
        
        // Verificar conexión cada 30 segundos
        setInterval(verificarConexionBD, 30000);
        
        // Función para obtener imagen por categoría
        function getImageByCategory(categoria) {
            const categoryImages = {
                'Bebidas': 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=24&h=24&fit=crop', // agua
                'Bebidas Frías': 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=24&h=24&fit=crop', // agua
                'Bebidas Calientes': 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=24&h=24&fit=crop', // café
                'Entradas': 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=24&h=24&fit=crop', // ensalada
                'Platos Principales': 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=24&h=24&fit=crop', // plato
                'Postres': 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=24&h=24&fit=crop', // postre
                'Vinos': 'https://images.unsplash.com/photo-1474722883778-792e7990302f?w=24&h=24&fit=crop', // vino
                'Cervezas': 'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=24&h=24&fit=crop', // cerveza
                'Cocteles': 'https://images.unsplash.com/photo-1569529465841-dfecdab7503b?w=24&h=24&fit=crop' // coctel
            };
            return categoryImages[categoria] || 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=24&h=24&fit=crop'; // default
        }
    </script>
    <!-- Botón Flotante para Punto de Venta -->
    <a href="/ventas" class="fixed bottom-8 right-8 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg z-40 flex items-center justify-center transition-all hover:scale-110 group">
        <i class="fas fa-cash-register text-xl"></i>
        <span class="absolute right-full mr-3 bg-gray-900 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
            Ir al Punto de Venta
        </span>
    </a>
</body>
</html>
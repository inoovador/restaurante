<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Punto de Venta - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- jsPDF para generar PDFs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- html2canvas para capturar HTML como imagen -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <style>
        :root {
            --primary-color: #E32636;
            --secondary-color: #4d82bc;
        }

        .categoria-tab {
            transition: all 0.3s ease;
        }

        .categoria-tab.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, #dc2626 100%);
            color: white;
        }

        .producto-card {
            transition: all 0.3s ease;
        }

        .producto-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .cart-item {
            transition: all 0.3s ease;
        }

        .category-bebidas { background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); }
        .category-pizzas { background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); }
        .category-ensaladas { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
        .category-carnes { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); }
        .category-postres { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); }
        .category-entradas { background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%); }
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
                                <i class="fas fa-cash-register text-red-600 mr-3"></i>
                                Punto de Venta
                            </h1>
                            <p class="text-sm text-gray-500">Sistema de ventas FoodPoint</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-lg font-semibold text-gray-900" id="currentTime"></div>
                            <div class="text-sm text-gray-500">Hora actual</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['ventas_hoy'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Ventas hoy</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">S/. {{ number_format($stats['total_ventas'] ?? 0, 2) }}</div>
                        <div class="text-sm text-gray-500">Total vendido</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $stats['productos_vendidos'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Productos vendidos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">S/. {{ number_format($stats['ticket_promedio'] ?? 0, 2) }}</div>
                        <div class="text-sm text-gray-500">Ticket promedio</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Panel de Productos -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Categorías -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categorías</h3>
                        <div class="flex flex-wrap gap-2" id="categorias-container">
                            <button onclick="filtrarCategoria('todos')" class="categoria-tab active px-4 py-2 rounded-lg font-medium">
                                <i class="fas fa-th-large mr-2"></i>
                                Todas
                            </button>
                            @foreach($categorias as $categoria)
                            <button onclick="filtrarCategoria('{{ $categoria->id }}')" 
                                    class="categoria-tab px-4 py-2 rounded-lg font-medium border border-gray-300 hover:border-red-500 hover:text-red-600"
                                    data-categoria="{{ strtolower($categoria->nombre) }}">
                                <i class="fas fa-{{ $categoria->icono ?? 'utensils' }} mr-2"></i>
                                {{ $categoria->nombre }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Búsqueda -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="relative">
                            <input type="text" 
                                   id="buscarProducto" 
                                   placeholder="Buscar productos..." 
                                   class="w-full px-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-lg">
                            <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Grid de Productos -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Productos</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" id="productos-grid">
                            @foreach($productos as $producto)
                            <div class="producto-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-red-500"
                                 data-categoria="{{ $producto->categoria_id }}"
                                 data-nombre="{{ strtolower($producto->nombre) }}"
                                 data-producto-id="{{ $producto->id }}"
                                 data-producto-nombre="{{ $producto->nombre }}"
                                 data-producto-precio="{{ $producto->precio_venta }}"
                                 data-producto-stock="{{ $producto->stock }}"
                                 onclick="agregarProducto({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', {{ $producto->precio_venta }}, {{ $producto->stock }})">
                                
                                <!-- Imagen del producto -->
                                <div class="h-24 bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                                    @if($producto->imagen)
                                        <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" class="h-full w-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-utensils text-3xl text-gray-400"></i>
                                    @endif
                                </div>

                                <!-- Info del producto -->
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1 text-sm">{{ $producto->nombre }}</h4>
                                    <p class="text-xs text-gray-500 mb-2">{{ $producto->categoria_nombre }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-red-600">S/. {{ number_format($producto->precio_venta, 2) }}</span>
                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">Stock: {{ $producto->stock }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Panel del Carrito -->
                <div class="space-y-6">
                    <!-- Información del pedido -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Pedido</h3>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente (Opcional)</label>
                                <select id="cliente_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="">Cliente general</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mesa (Opcional)</label>
                                <select id="mesa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="">Para llevar</option>
                                    @foreach($mesas as $mesa)
                                        <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero }} - {{ ucfirst($mesa->zona) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Comprobante</label>
                                <select id="tipo_comprobante" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="boleta">Boleta de Venta</option>
                                    <option value="factura">Factura Electrónica</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                                <select id="metodo_pago" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Carrito -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Carrito</h3>
                                <button onclick="limpiarCarrito()" class="text-red-600 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash mr-1"></i>
                                    Limpiar
                                </button>
                            </div>
                        </div>
                        
                        <div id="carrito-items" class="p-4 max-h-96 overflow-y-auto">
                            <!-- Los items del carrito se generan dinámicamente -->
                        </div>

                        <!-- Totales -->
                        <div class="p-4 border-t border-gray-200 bg-gray-50">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">S/. 0.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>IGV (18%):</span>
                                    <span id="igv">S/. 0.00</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t pt-2">
                                    <span>Total:</span>
                                    <span id="total">S/. 0.00</span>
                                </div>
                            </div>
                            
                            <button id="btn-procesar" 
                                    onclick="procesarVenta()" 
                                    disabled
                                    class="w-full mt-4 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                                <i class="fas fa-credit-card mr-2"></i>
                                Procesar Venta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Boleta/Factura -->
    <div id="boletaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <!-- Header del modal -->
            <div class="bg-red-600 text-white p-4 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold">Comprobante de Venta</h2>
                    <button onclick="closeBoleta()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Contenido de la boleta -->
            <div id="boleta-content" class="p-6">
                <!-- El contenido se genera dinámicamente -->
            </div>

            <!-- Footer con acciones -->
            <div class="border-t p-4 bg-gray-50 rounded-b-lg">
                <div class="flex gap-3">
                    <button onclick="imprimirBoleta()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-print mr-2"></i>
                        Imprimir
                    </button>
                    <button onclick="descargarPDF()" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-file-pdf mr-2"></i>
                        PDF
                    </button>
                    <button onclick="nuevaVenta()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-plus mr-2"></i>
                        Nueva Venta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let carrito = [];
        let productos = [];
        
        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            console.log('POS iniciando...');
            
            // Cargar productos desde PHP
            try {
                productos = {!! json_encode($productos, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) !!};
                console.log('Productos cargados:', productos.length);
            } catch(e) {
                console.error('Error cargando productos:', e);
                productos = [];
            }
            
            // Inicializar interfaz
            actualizarReloj();
            actualizarCarrito();
            
            // Agregar event listener de búsqueda
            const buscarInput = document.getElementById('buscarProducto');
            if (buscarInput) {
                buscarInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const productos = document.querySelectorAll('.producto-card');
                    
                    productos.forEach(producto => {
                        const nombre = producto.dataset.nombre;
                        if (nombre.includes(searchTerm)) {
                            producto.style.display = 'block';
                        } else {
                            producto.style.display = 'none';
                        }
                    });
                });
            }
            
            console.log('POS iniciado correctamente');
        });
        
        // Reloj
        function actualizarReloj() {
            const now = new Date();
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('es-ES', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });
            }
        }
        setInterval(actualizarReloj, 1000);

        // Filtrar por categoría (función global para onclick)
        window.filtrarCategoria = function(categoriaId) {
            // Actualizar tabs
            document.querySelectorAll('.categoria-tab').forEach(tab => {
                tab.classList.remove('active');
                tab.classList.add('border', 'border-gray-300');
            });
            event.target.classList.add('active');
            event.target.classList.remove('border', 'border-gray-300');

            // Filtrar productos
            const productos = document.querySelectorAll('.producto-card');
            productos.forEach(producto => {
                if (categoriaId === 'todos' || producto.dataset.categoria === categoriaId) {
                    producto.style.display = 'block';
                } else {
                    producto.style.display = 'none';
                }
            });
        }

        // Agregar producto al carrito (función global para onclick)
        window.agregarProducto = function(id, nombre, precio, stock) {
            console.log('Agregando producto:', id, nombre, precio, stock);
            
            // Validar parámetros
            if (!id || !nombre || precio === undefined || stock === undefined) {
                console.error('Parámetros inválidos para agregar producto');
                return;
            }
            
            // Buscar si ya existe
            let encontrado = false;
            for (let i = 0; i < carrito.length; i++) {
                if (carrito[i].id === id) {
                    if (carrito[i].cantidad < stock) {
                        carrito[i].cantidad++;
                        encontrado = true;
                        console.log('Cantidad incrementada a:', carrito[i].cantidad);
                    } else {
                        alert('No hay suficiente stock disponible');
                        return;
                    }
                    break;
                }
            }
            
            // Si no existe, agregar nuevo
            if (!encontrado) {
                carrito.push({
                    id: id,
                    nombre: nombre,
                    precio: parseFloat(precio),
                    cantidad: 1,
                    stock: stock
                });
                console.log('Producto agregado al carrito:', nombre);
            }
            
            actualizarCarrito();
        }

        // Actualizar carrito
        function actualizarCarrito() {
            console.log('Actualizando carrito con', carrito.length, 'items');
            
            const carritoItems = document.getElementById('carrito-items');
            const btnProcesar = document.getElementById('btn-procesar');
            
            // Limpiar contenido
            if (carritoItems) {
                carritoItems.innerHTML = '';
                
                if (carrito.length === 0) {
                    // Crear mensaje vacío
                    const divVacio = document.createElement('div');
                    divVacio.className = 'text-center py-8 text-gray-500';
                    divVacio.innerHTML = '<i class="fas fa-shopping-cart text-4xl mb-2"></i><p>El carrito está vacío</p><p class="text-sm">Agrega productos para comenzar</p>';
                    carritoItems.appendChild(divVacio);
                    
                    if (btnProcesar) btnProcesar.disabled = true;
                } else {
                    // Crear items del carrito
                    carrito.forEach((item, index) => {
                        const divItem = document.createElement('div');
                        divItem.className = 'cart-item flex justify-between items-center py-3 border-b border-gray-100';
                        
                        divItem.innerHTML = '<div class="flex-1">' +
                            '<h4 class="font-medium text-gray-900">' + item.nombre + '</h4>' +
                            '<p class="text-sm text-gray-500">S/. ' + item.precio.toFixed(2) + ' c/u</p>' +
                            '</div>' +
                            '<div class="flex items-center space-x-2">' +
                            '<button onclick="cambiarCantidad(' + index + ', -1)" class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center">' +
                            '<i class="fas fa-minus text-xs"></i>' +
                            '</button>' +
                            '<span class="w-8 text-center font-medium">' + item.cantidad + '</span>' +
                            '<button onclick="cambiarCantidad(' + index + ', 1)" class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center">' +
                            '<i class="fas fa-plus text-xs"></i>' +
                            '</button>' +
                            '<button onclick="eliminarDelCarrito(' + index + ')" class="w-8 h-8 rounded-full bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center ml-2">' +
                            '<i class="fas fa-trash text-xs"></i>' +
                            '</button>' +
                            '</div>';
                        
                        carritoItems.appendChild(divItem);
                    });
                    
                    if (btnProcesar) btnProcesar.disabled = false;
                }
            }
            
            calcularTotales();
        }

        // Cambiar cantidad (función global para onclick)
        window.cambiarCantidad = function(index, cambio) {
            const item = carrito[index];
            const nuevaCantidad = item.cantidad + cambio;
            
            if (nuevaCantidad <= 0) {
                carrito.splice(index, 1);
            } else if (nuevaCantidad <= item.stock) {
                item.cantidad = nuevaCantidad;
            } else {
                alert('No hay suficiente stock disponible');
                return;
            }
            
            actualizarCarrito();
        }

        // Eliminar del carrito (función global para onclick)
        window.eliminarDelCarrito = function(index) {
            carrito.splice(index, 1);
            actualizarCarrito();
        }

        // Limpiar carrito (función global para onclick)
        window.limpiarCarrito = function() {
            if (carrito.length > 0 && confirm('¿Está seguro de limpiar el carrito?')) {
                carrito = [];
                actualizarCarrito();
            }
        }

        // Calcular totales
        function calcularTotales() {
            const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
            const igv = subtotal * 0.18;
            const total = subtotal + igv;
            
            document.getElementById('subtotal').textContent = 'S/. ' + subtotal.toFixed(2);
            document.getElementById('igv').textContent = 'S/. ' + igv.toFixed(2);
            document.getElementById('total').textContent = 'S/. ' + total.toFixed(2);
        }

        // Generar comprobante (boleta o factura)
        function generarBoleta(ventaData) {
            console.log('Generando comprobante para:', ventaData);
            
            const modal = document.getElementById('boletaModal');
            const content = document.getElementById('boleta-content');
            
            if (!modal || !content) {
                console.error('Modal o contenido no encontrado');
                return;
            }
            
            // Obtener datos del formulario
            const clienteSelect = document.getElementById('cliente_id');
            const mesaSelect = document.getElementById('mesa_id');
            const metodoPago = document.getElementById('metodo_pago').value;
            const tipoComprobante = document.getElementById('tipo_comprobante').value;
            
            const clienteNombre = clienteSelect.options[clienteSelect.selectedIndex]?.text || 'Cliente general';
            const mesaTexto = mesaSelect.options[mesaSelect.selectedIndex]?.text || 'Para llevar';
            
            // Fecha actual
            const fecha = new Date();
            const fechaTexto = fecha.toLocaleDateString('es-PE');
            const horaTexto = fecha.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            
            let html = '';
            
            if (tipoComprobante === 'boleta') {
                html = generarBoletaHTML(ventaData, clienteNombre, mesaTexto, metodoPago, fechaTexto, horaTexto);
            } else {
                html = generarFacturaHTML(ventaData, clienteNombre, mesaTexto, metodoPago, fechaTexto, horaTexto);
            }
            
            // Actualizar título del modal
            const modalTitle = modal.querySelector('h2');
            if (modalTitle) {
                modalTitle.textContent = tipoComprobante === 'boleta' ? 'Boleta de Venta' : 'Factura Electrónica';
            }
            
            // Insertar contenido
            content.innerHTML = html;
            
            // Mostrar modal
            modal.classList.remove('hidden');
        }

        // Generar HTML para boleta estilo ticket (basado en MODEL22.JPG)
        function generarBoletaHTML(ventaData, cliente, mesa, pago, fecha, hora) {
            const subtotalGeneral = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
            const igv = subtotalGeneral * 0.18;
            const total = subtotalGeneral + igv;
            const numeroComprobante = ventaData.numero_venta || 'B001-4578';
            
            // Verificar si necesita datos del cliente (total >= 700)
            const requiereDatosCliente = total >= 700;
            
            // QR Data para boleta
            const qrData = '20601234567|03|' + numeroComprobante + '|' + igv.toFixed(2) + '|' + total.toFixed(2) + '|' + fecha + '|6|000050|1';
            const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' + encodeURIComponent(qrData);
            
            let html = '<div class="max-w-xs mx-auto bg-white p-4" style="font-family: Arial, sans-serif; font-size: 12px;">';
            
            // Header simple
            html += '<div class="text-center mb-4">';
            html += '<h1 class="font-bold text-lg mb-1">RESTAURANTE</h1>';
            html += '<h2 class="font-bold text-base mb-1">"SABOR AMAZÓNICO"</h2>';
            html += '<p class="text-xs">RUC: 20601234567</p>';
            html += '<p class="text-xs">DIR: AV. PRINCIPAL 123, LA MERCED - JUNÍN</p>';
            html += '</div>';
            
            // Tipo y número de comprobante
            html += '<div class="text-center mb-4">';
            html += '<p class="font-bold text-sm">BOLETA DE VENTA</p>';
            html += '<p class="font-bold text-sm">ELECTRÓNICA</p>';
            html += '<p class="font-bold">' + numeroComprobante + '</p>';
            html += '<p class="text-xs">' + fecha + ' ' + hora + '</p>';
            html += '</div>';
            
            // Datos del cliente solo si es requerido
            if (requiereDatosCliente && cliente !== 'Cliente general') {
                html += '<div class="mb-3 text-xs border-b pb-2">';
                html += '<p><strong>CLIENTE:</strong> ' + cliente.toUpperCase() + '</p>';
                html += '<p><strong>DNI:</strong> 00000000</p>';
                html += '</div>';
            }
            
            // Tabla de productos simplificada
            html += '<table class="w-full mb-3" style="font-size: 11px;">';
            html += '<thead>';
            html += '<tr class="border-b">';
            html += '<th class="text-left py-1">ITEM / DESCRIPCIÓN</th>';
            html += '<th class="text-center py-1">CANT.</th>';
            html += '<th class="text-right py-1">P.UNIT</th>';
            html += '<th class="text-right py-1">IMPORTE</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            
            carrito.forEach(item => {
                const subtotalItem = item.precio * item.cantidad;
                html += '<tr class="border-b">';
                html += '<td class="py-1 text-xs">' + item.nombre + '</td>';
                html += '<td class="text-center py-1">' + item.cantidad + '</td>';
                html += '<td class="text-right py-1">' + item.precio.toFixed(2) + '</td>';
                html += '<td class="text-right py-1">' + subtotalItem.toFixed(2) + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody>';
            html += '</table>';
            
            // Totales
            html += '<div class="text-right mb-4">';
            html += '<div class="flex justify-between py-1"><span>SUB TOTAL S/</span><span>' + subtotalGeneral.toFixed(2) + '</span></div>';
            html += '<div class="flex justify-between py-1"><span>IGV (18%) S/</span><span>' + igv.toFixed(2) + '</span></div>';
            html += '<div class="flex justify-between py-1 font-bold border-t pt-1"><span>TOTAL A PAGAR: S/</span><span>' + total.toFixed(2) + ' SOLES</span></div>';
            html += '</div>';
            
            // QR Code
            html += '<div class="text-center">';
            html += '<img src="' + qrUrl + '" alt="QR Code" class="mx-auto mb-2" style="width: 100px; height: 100px;">';
            html += '<p class="text-xs">Consulte su comprobante en sunat.gob.pe</p>';
            html += '</div>';
            
            html += '</div>';
            return html;
        }

        // Generar HTML para factura electrónica (basado en MODEL22.JPG)
        function generarFacturaHTML(ventaData, cliente, mesa, pago, fecha, hora) {
            const subtotalGeneral = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
            const igv = subtotalGeneral * 0.18;
            const total = subtotalGeneral + igv;
            const numeroComprobante = ventaData.numero_venta || 'F001-134';
            
            // QR Data para factura
            const qrData = '20601234567|01|' + numeroComprobante + '|' + igv.toFixed(2) + '|' + total.toFixed(2) + '|' + fecha + '|20000315543|1';
            const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' + encodeURIComponent(qrData);
            
            let html = '<div class="max-w-xs mx-auto bg-white p-4" style="font-family: Arial, sans-serif; font-size: 12px;">';
            
            // Header simple similar a boleta
            html += '<div class="text-center mb-4">';
            html += '<h1 class="font-bold text-lg mb-1">RESTAURANTE</h1>';
            html += '<h2 class="font-bold text-base mb-1">"SABOR AMAZÓNICO"</h2>';
            html += '<p class="text-xs">RUC: 20601234567</p>';
            html += '<p class="text-xs">DIR: AV. PRINCIPAL 123, LA MERCED - JUNÍN</p>';
            html += '</div>';
            
            // Tipo y número de comprobante
            html += '<div class="text-center mb-4">';
            html += '<p class="font-bold text-sm">FACTURA ELECTRÓNICA</p>';
            html += '<p class="font-bold text-sm">ELECTRÓNICA</p>';
            html += '<p class="font-bold">' + numeroComprobante + '</p>';
            html += '<p class="text-xs">' + fecha + ' ' + hora + '</p>';
            html += '</div>';
            
            // Información del cliente (obligatoria para factura)
            html += '<div class="mb-3 text-xs border-b pb-2">';
            html += '<p><strong>EMPRESA:</strong> ' + (cliente !== 'Cliente general' ? cliente.toUpperCase() : 'EMPRESA SOL S.A.C') + '</p>';
            html += '<p><strong>RUC:</strong> 20000315543</p>';
            html += '<p><strong>DIRECCIÓN:</strong> AV. LOS ALAMOS 123</p>';
            html += '</div>';
            
            // Tabla de productos simplificada (igual que boleta)
            html += '<table class="w-full mb-3" style="font-size: 11px;">';
            html += '<thead>';
            html += '<tr class="border-b">';
            html += '<th class="text-left py-1">ITEM / DESCRIPCIÓN</th>';
            html += '<th class="text-center py-1">CANT.</th>';
            html += '<th class="text-right py-1">V.UNIT</th>';
            html += '<th class="text-right py-1">IMPORTE</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            
            carrito.forEach(item => {
                const subtotalItem = item.precio * item.cantidad;
                html += '<tr class="border-b">';
                html += '<td class="py-1 text-xs">' + item.nombre + '</td>';
                html += '<td class="text-center py-1">' + item.cantidad + '</td>';
                html += '<td class="text-right py-1">' + item.precio.toFixed(2) + '</td>';
                html += '<td class="text-right py-1">' + subtotalItem.toFixed(2) + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody>';
            html += '</table>';
            
            // Totales (similar a boleta pero con detalles de empresa)
            html += '<div class="text-right mb-4">';
            html += '<div class="flex justify-between py-1"><span>DIREC.CG AGUA</span><span>IGV (18%) S/ ' + (total * 0.018).toFixed(2) + '</span></div>';
            html += '<div class="flex justify-between py-1"><span>OP. GRAVADAS:</span><span>S/ ' + subtotalGeneral.toFixed(2) + '</span></div>';
            html += '<div class="flex justify-between py-1"><span>IGV (18%):</span><span>S/ ' + igv.toFixed(2) + '</span></div>';
            html += '<div class="flex justify-between py-1 font-bold border-t pt-1"><span>IMPORTE TOTAL: S/</span><span>' + total.toFixed(2) + ' SOLES</span></div>';
            html += '</div>';
            
            // QR Code
            html += '<div class="text-center">';
            html += '<img src="' + qrUrl + '" alt="QR Code" class="mx-auto mb-2" style="width: 100px; height: 100px;">';
            html += '<p class="text-xs">Consulte su comprobante en sunat.gob.pe</p>';
            html += '</div>';
            html += '</div>';
            return html;
        }

        // Función para convertir número a letras (básica)
        function numeroALetras(num) {
            const unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
            const decenas = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
            const centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
            
            const parteEntera = Math.floor(num);
            const centavos = Math.round((num - parteEntera) * 100);
            
            if (parteEntera === 0) return 'CERO CON ' + centavos.toString().padStart(2, '0') + '/100 SOLES';
            
            let letras = '';
            if (parteEntera >= 100) {
                if (parteEntera === 100) letras = 'CIEN';
                else letras = centenas[Math.floor(parteEntera / 100)];
                parteEntera = parteEntera % 100;
            }
            
            if (parteEntera >= 20) {
                if (letras) letras += ' ';
                letras += decenas[Math.floor(parteEntera / 10)];
                parteEntera = parteEntera % 10;
            } else if (parteEntera >= 10) {
                const especiales = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
                if (letras) letras += ' ';
                letras += especiales[parteEntera - 10];
                parteEntera = 0;
            }
            
            if (parteEntera > 0) {
                if (letras) letras += ' Y ';
                letras += unidades[parteEntera];
            }
            
            return letras + ' CON ' + centavos.toString().padStart(2, '0') + '/100 SOLES';
        }

        // Cerrar boleta (función global para onclick)
        window.closeBoleta = function() {
            const modal = document.getElementById('boletaModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // Imprimir boleta (función global para onclick)
        window.imprimirBoleta = function() {
            const content = document.getElementById('boleta-content').innerHTML;
            const ventana = window.open('', 'PRINT', 'height=600,width=400');
            
            ventana.document.write('<html><head><title>Boleta de Venta</title>');
            ventana.document.write('<style>body{font-family: Arial, sans-serif; font-size: 12px; margin: 20px;} table{width:100%; border-collapse: collapse;} th,td{border-bottom: 1px solid #ccc; padding: 4px;}</style>');
            ventana.document.write('</head><body>');
            ventana.document.write(content);
            ventana.document.write('</body></html>');
            
            ventana.document.close();
            ventana.focus();
            ventana.print();
            ventana.close();
        }

        // Función auxiliar para generar PDF con formato nativo
        function generarPDFNativo() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: [80, 250] // Formato ticket más largo para incluir QR
            });
            
            // Configuración inicial
            doc.setFontSize(12);
            let y = 10;
            
            // Header con líneas decorativas
            doc.setLineWidth(0.5);
            doc.line(5, y-2, 75, y-2);
            
            doc.setFont(undefined, 'bold');
            doc.text('RESTAURANTE', 40, y, { align: 'center' });
            y += 5;
            doc.text('"SABOR AMAZÓNICO"', 40, y, { align: 'center' });
            y += 5;
            
            doc.setFontSize(8);
            doc.setFont(undefined, 'normal');
            doc.text('RUC: 20601234567', 40, y, { align: 'center' });
            y += 4;
            doc.text('DIR: AV. PRINCIPAL 123', 40, y, { align: 'center' });
            y += 4;
            doc.text('LA MERCED - JUNÍN', 40, y, { align: 'center' });
            y += 4;
            doc.text('TELF: 123-4567', 40, y, { align: 'center' });
            y += 4;
            doc.text('www.saboramazonico.com', 40, y, { align: 'center' });
            y += 5;
            
            doc.line(5, y, 75, y);
            y += 5;
            
            // Tipo de comprobante
            const tipoComprobante = document.getElementById('tipo_comprobante')?.value || 'boleta';
            doc.setFont(undefined, 'bold');
            doc.setFontSize(10);
            if (tipoComprobante === 'boleta') {
                doc.text('BOLETA DE VENTA', 40, y, { align: 'center' });
            } else {
                doc.text('FACTURA ELECTRÓNICA', 40, y, { align: 'center' });
            }
            y += 5;
            doc.text('ELECTRÓNICA', 40, y, { align: 'center' });
            y += 5;
            
            // Número de comprobante
            const prefijo = tipoComprobante === 'boleta' ? 'B001-' : 'F001-';
            const numeroComprobante = prefijo + Math.floor(Math.random() * 10000).toString().padStart(5, '0');
            doc.text(numeroComprobante, 40, y, { align: 'center' });
            y += 5;
            
            // Fecha y hora
            doc.setFontSize(8);
            doc.setFont(undefined, 'normal');
            const fecha = new Date().toLocaleDateString('es-PE');
            const hora = new Date().toLocaleTimeString('es-PE');
            doc.text('Fecha: ' + fecha, 5, y);
            doc.text('Hora: ' + hora, 75, y, { align: 'right' });
            y += 5;
            
            // Cliente info si es necesario
            const subtotalGeneral = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
            const igv = subtotalGeneral * 0.18;
            const total = subtotalGeneral + igv;
            
            if (total >= 700 || tipoComprobante === 'factura') {
                const clienteSelect = document.getElementById('cliente_id');
                const clienteNombre = clienteSelect?.options[clienteSelect.selectedIndex]?.text || 'CLIENTE GENERAL';
                doc.text('CLIENTE: ' + clienteNombre.toUpperCase(), 5, y);
                y += 4;
                if (tipoComprobante === 'factura') {
                    doc.text('RUC: 20000315543', 5, y);
                    y += 4;
                } else {
                    doc.text('DNI: 18137254', 5, y);
                    y += 4;
                }
            }
            y += 4;
            
            // Línea separadora
            doc.line(5, y, 75, y);
            y += 5;
            
            // Productos
            doc.setFontSize(7);
            doc.text('ITEM / DESCRIPCIÓN', 5, y);
            doc.text('CANT', 45, y);
            doc.text('P.UNIT', 55, y);
            doc.text('IMPORTE', 65, y);
            y += 3;
            doc.line(5, y, 75, y);
            y += 4;
            
            // Agregar productos del carrito
            carrito.forEach(item => {
                const subtotal = item.precio * item.cantidad;
                doc.text(item.nombre.substring(0, 30), 5, y);
                doc.text(item.cantidad.toString(), 47, y, { align: 'center' });
                doc.text(item.precio.toFixed(2), 58, y, { align: 'right' });
                doc.text(subtotal.toFixed(2), 73, y, { align: 'right' });
                y += 4;
            });
            
            // Línea separadora
            y += 2;
            doc.line(5, y, 75, y);
            y += 4;
            
            // Totales (ya calculados arriba)
            doc.text('SUB TOTAL S/', 45, y);
            doc.text(subtotalGeneral.toFixed(2), 73, y, { align: 'right' });
            y += 4;
            doc.text('IGV (18%) S/', 45, y);
            doc.text(igv.toFixed(2), 73, y, { align: 'right' });
            y += 4;
            
            doc.setFont(undefined, 'bold');
            doc.text('TOTAL A PAGAR:', 45, y);
            doc.text('S/ ' + total.toFixed(2), 73, y, { align: 'right' });
            y += 6;
            
            // Total en letras
            doc.setFont(undefined, 'normal');
            doc.setFontSize(7);
            const totalEnLetras = numeroALetras(total);
            doc.text('SON: ' + totalEnLetras, 5, y);
            y += 8;
            
            // Línea separadora
            doc.line(5, y, 75, y);
            y += 5;
            
            // QR Code
            doc.setFontSize(6);
            doc.text('Representación impresa del comprobante electrónico', 40, y, { align: 'center' });
            y += 4;
            
            // Generar QR Code real si es posible
            try {
                const qrData = '20601234567|' + (tipoComprobante === 'boleta' ? '03' : '01') + '|' + numeroComprobante + '|' + igv.toFixed(2) + '|' + total.toFixed(2) + '|' + fecha + '|6|00050|1';
                const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent(qrData);
                
                // Intentar agregar QR como imagen
                const qrSize = 25;
                const qrX = (80 - qrSize) / 2;
                
                // Agregar la imagen QR (esto funcionará si la imagen está disponible)
                doc.addImage(qrUrl, 'PNG', qrX, y, qrSize, qrSize);
                y += qrSize + 3;
            } catch (qrError) {
                // Si falla, dibujar un QR simulado
                const qrSize = 20;
                const qrX = (80 - qrSize) / 2;
                doc.rect(qrX, y, qrSize, qrSize);
                
                // Patrón básico de QR
                doc.setFillColor(0, 0, 0);
                // Esquinas del QR (patrones de posición)
                doc.rect(qrX, y, 6, 6, 'F');
                doc.rect(qrX + qrSize - 6, y, 6, 6, 'F');
                doc.rect(qrX, y + qrSize - 6, 6, 6, 'F');
                
                // Centro blanco de las esquinas
                doc.setFillColor(255, 255, 255);
                doc.rect(qrX + 1, y + 1, 4, 4, 'F');
                doc.rect(qrX + qrSize - 5, y + 1, 4, 4, 'F');
                doc.rect(qrX + 1, y + qrSize - 5, 4, 4, 'F');
                
                // Centro negro de las esquinas
                doc.setFillColor(0, 0, 0);
                doc.rect(qrX + 2, y + 2, 2, 2, 'F');
                doc.rect(qrX + qrSize - 4, y + 2, 2, 2, 'F');
                doc.rect(qrX + 2, y + qrSize - 4, 2, 2, 'F');
                
                // Datos aleatorios en el centro
                for (let i = 7; i < qrSize - 7; i += 2) {
                    for (let j = 7; j < qrSize - 7; j += 2) {
                        if (Math.random() > 0.4) {
                            doc.rect(qrX + i, y + j, 2, 2, 'F');
                        }
                    }
                }
                
                y += qrSize + 3;
            }
            
            // Código QR data
            const qrData = '20601234567|' + (tipoComprobante === 'boleta' ? '03' : '01') + '|' + numeroComprobante;
            doc.text(qrData, 40, y, { align: 'center' });
            y += 4;
            
            // Hash
            doc.text('HASH: RT1GSVwBOTmsxJlWEPAK+xrfAD=', 40, y, { align: 'center' });
            y += 5;
            
            // Footer
            doc.text('Consulte su comprobante en', 40, y, { align: 'center' });
            y += 3;
            doc.text('sunat.gob.pe', 40, y, { align: 'center' });
            y += 5;
            
            doc.line(5, y, 75, y);
            y += 3;
            
            doc.setFont(undefined, 'bold');
            doc.text('¡Gracias por su preferencia!', 40, y, { align: 'center' });
            y += 3;
            doc.setFont(undefined, 'normal');
            doc.text('Vuelva pronto', 40, y, { align: 'center' });
            
            // Guardar PDF
            return doc;
        }
        
        // Descargar PDF (función global para onclick)
        window.descargarPDF = async function() {
            try {
                // Mostrar mensaje de generación
                const btn = event.target.closest('button');
                const originalText = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando...';
                    btn.disabled = true;
                }
                
                // Verificar que jsPDF esté disponible
                if (typeof window.jspdf === 'undefined') {
                    alert('Error: La librería jsPDF no está cargada. Por favor, recargue la página.');
                    if (btn) {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                    return;
                }
                
                const { jsPDF } = window.jspdf;
                
                // Obtener el contenido del comprobante
                const content = document.getElementById('boleta-content');
                if (!content) {
                    alert('No hay comprobante para descargar');
                    return;
                }
                
                // Intentar primero con formato nativo (más confiable)
                try {
                    const doc = generarPDFNativo();
                    const fecha = new Date().toISOString().split('T')[0];
                    const tipoComprobante = document.getElementById('tipo_comprobante')?.value || 'comprobante';
                    doc.save(tipoComprobante + '_' + fecha + '.pdf');
                    
                    // Restaurar botón
                    if (btn) {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                    return;
                } catch (nativeError) {
                    console.log('Fallback a html2canvas:', nativeError);
                }
                
                // Opción 2: Generar PDF desde HTML usando html2canvas (mejor calidad visual)
                if (window.html2canvas) {
                    // Capturar el contenido como imagen
                    const canvas = await html2canvas(content, {
                        scale: 2,
                        useCORS: true,
                        logging: false,
                        backgroundColor: '#ffffff'
                    });
                    
                    // Crear PDF con la imagen
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    
                    // Calcular dimensiones para centrar en A4
                    const pdfWidth = 210; // A4 width in mm
                    const pdfHeight = 297; // A4 height in mm
                    const imgWidth = 80; // Ancho del ticket en mm
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    // Centrar horizontalmente
                    const x = (pdfWidth - imgWidth) / 2;
                    const y = 10; // Margen superior
                    
                    // Agregar imagen al PDF
                    pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
                    
                    // Obtener fecha y tipo de comprobante para el nombre
                    const fecha = new Date().toISOString().split('T')[0];
                    const tipoComprobante = document.getElementById('tipo_comprobante')?.value || 'comprobante';
                    const numeroComprobante = content.querySelector('p.font-bold')?.textContent || 'documento';
                    
                    // Descargar PDF
                    pdf.save(tipoComprobante + '_' + numeroComprobante + '_' + fecha + '.pdf');
                    
                } else {
                    // Opción 2: Generar PDF desde texto (más simple pero menos visual)
                    const pdf = new jsPDF('p', 'mm', [80, 297]); // Tamaño ticket
                    
                    // Configurar fuente
                    pdf.setFontSize(10);
                    
                    // Obtener texto del comprobante
                    const lines = content.innerText.split('\n');
                    let y = 10;
                    
                    lines.forEach(line => {
                        if (y > 280) {
                            pdf.addPage();
                            y = 10;
                        }
                        pdf.text(line, 10, y);
                        y += 5;
                    });
                    
                    // Descargar PDF
                    const fecha = new Date().toISOString().split('T')[0];
                    pdf.save('comprobante_' + fecha + '.pdf');
                }
                
                // Restaurar botón
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
                
            } catch (error) {
                console.error('Error generando PDF:', error);
                alert('Error al generar el PDF. Por favor, use la opción de imprimir.');
                
                // Restaurar botón en caso de error
                const btn = event.target?.closest('button');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-file-pdf mr-2"></i>PDF';
                    btn.disabled = false;
                }
            }
        }

        // Nueva venta (función global para onclick)
        window.nuevaVenta = function() {
            closeBoleta();
            location.reload();
        }

        // Procesar venta (función global para onclick)
        window.procesarVenta = async function() {
            console.log('Iniciando proceso de venta...');
            console.log('Carrito actual:', carrito);
            
            if (!carrito || carrito.length === 0) {
                alert('El carrito está vacío. Agrega productos antes de procesar la venta.');
                return;
            }
            
            // Calcular total para verificar límite SUNAT
            const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
            const igv = subtotal * 0.18;
            const total = subtotal + igv;
            
            // Obtener datos del formulario
            const clienteId = document.getElementById('cliente_id').value;
            const mesaId = document.getElementById('mesa_id').value;
            const metodoPago = document.getElementById('metodo_pago').value;
            
            // Verificar límite SUNAT de S/. 700
            if (total >= 700) {
                if (!clienteId || clienteId === '') {
                    alert('ATENCIÓN: Para montos iguales o mayores a S/. 700 es obligatorio registrar los datos del cliente según normativa SUNAT.\n\nPor favor, seleccione o registre un cliente.');
                    document.getElementById('cliente_id').focus();
                    return;
                }
            }
            
            // Ya calculamos subtotal, igv y total arriba para verificar límite SUNAT
            // No es necesario redeclarar

            const ventaData = {
                cliente_id: clienteId || null,
                mesa_id: mesaId || null,
                metodo_pago: metodoPago,
                productos: carrito.map(item => ({
                    id: item.id,
                    cantidad: item.cantidad,
                    precio: item.precio
                })),
                subtotal: subtotal,
                iva: igv,
                total: total
            };

            // Deshabilitar botón
            const btn = document.getElementById('btn-procesar');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
            btn.disabled = true;

            console.log('Enviando datos de venta:', ventaData);

            try {
                const response = await fetch('/ventas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(ventaData)
                });

                if (response.ok) {
                    const result = await response.json();
                    console.log('Respuesta del servidor:', result);
                    
                    if (result.success) {
                        // Venta exitosa - NO mostrar alert, solo console.log
                        console.log('¡Venta procesada exitosamente! Número: ' + (result.numero_venta || result.venta_id));
                        
                        // Generar e imprimir boleta directamente
                        generarBoleta(result);
                        
                        // Limpiar carrito y formulario
                        carrito = [];
                        document.getElementById('cliente_id').value = '';
                        document.getElementById('mesa_id').value = '';
                        document.getElementById('metodo_pago').value = 'efectivo';
                        actualizarCarrito();
                        
                        // NO recargar automáticamente - el usuario decide cuando cerrar la boleta
                    } else {
                        alert('Error: ' + result.message);
                    }
                } else {
                    // Error de respuesta HTTP
                    const errorText = await response.text();
                    console.error('Error HTTP:', response.status, errorText);
                    alert('Error del servidor: ' + response.status + '. Revisa la consola para más detalles.');
                }
            } catch (error) {
                console.error('Error de red:', error);
                alert('Error de conexión: ' + error.message);
            } finally {
                // Restaurar botón
                btn.innerHTML = originalText;
                btn.disabled = carrito.length === 0;
            }
        }
    </script>
</body>
</html>
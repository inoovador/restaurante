<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pedido #{{ $pedido->numero_venta ?? $pedido->id }} - FoodPoint</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
                visibility: visible;
            }
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b no-print">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <a href="/pedidos" class="text-gray-600 hover:text-gray-900 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Detalle del Pedido</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-print mr-2"></i>
                            Imprimir
                        </button>
                        <button onclick="generarBoleta()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-file-invoice mr-2"></i>
                            Generar Boleta
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="printArea">
            <!-- Información del Pedido -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Pedido #{{ $pedido->numero_venta ?? $pedido->id }}
                        </h2>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($pedido->estado == 'pendiente') bg-yellow-100 text-yellow-800
                            @elseif($pedido->estado == 'preparando') bg-blue-100 text-blue-800
                            @elseif($pedido->estado == 'listo') bg-green-100 text-green-800
                            @elseif($pedido->estado == 'completado' || $pedido->estado == 'pagado') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($pedido->estado ?? 'pendiente') }}
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Información del Cliente</h3>
                            <div class="space-y-1">
                                <p class="text-gray-900">
                                    <i class="fas fa-user text-gray-400 w-5"></i>
                                    {{ $pedido->cliente_nombre ?? 'Cliente General' }}
                                </p>
                                @if($pedido->cliente_telefono)
                                <p class="text-gray-900">
                                    <i class="fas fa-phone text-gray-400 w-5"></i>
                                    {{ $pedido->cliente_telefono }}
                                </p>
                                @endif
                                <p class="text-gray-900">
                                    <i class="fas fa-chair text-gray-400 w-5"></i>
                                    @if($pedido->mesa_numero)
                                        Mesa {{ $pedido->mesa_numero }}
                                    @else
                                        Para llevar
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Información del Pedido</h3>
                            <div class="space-y-1">
                                <p class="text-gray-900">
                                    <i class="fas fa-calendar text-gray-400 w-5"></i>
                                    {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-gray-900">
                                    <i class="fas fa-credit-card text-gray-400 w-5"></i>
                                    {{ ucfirst($pedido->tipo_pago ?? 'Efectivo') }}
                                </p>
                                <p class="text-gray-900 font-semibold">
                                    <i class="fas fa-dollar-sign text-gray-400 w-5"></i>
                                    Total: S/ {{ number_format($pedido->total, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($pedido->observaciones)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-comment text-gray-400 mr-2"></i>
                            {{ $pedido->observaciones }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Productos del Pedido</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Producto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio Unit.
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pedido->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($item->imagen)
                                        <img src="/storage/{{ $item->imagen }}" alt="{{ $item->producto_nombre }}" 
                                             class="h-10 w-10 rounded-lg object-cover mr-3">
                                        @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item->producto_nombre ?? 'Producto' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->cantidad }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    S/ {{ number_format($item->precio_unitario, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    S/ {{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No hay productos en este pedido
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Totales -->
                <div class="px-6 py-4 bg-gray-50">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">S/ {{ number_format($pedido->subtotal, 2) }}</span>
                        </div>
                        @if($pedido->descuento > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Descuento:</span>
                            <span class="font-medium text-red-600">- S/ {{ number_format($pedido->descuento, 2) }}</span>
                        </div>
                        @endif
                        @if($pedido->impuesto > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">IGV (18%):</span>
                            <span class="font-medium">S/ {{ number_format($pedido->impuesto, 2) }}</span>
                        </div>
                        @endif
                        <div class="pt-2 border-t border-gray-300">
                            <div class="flex justify-between">
                                <span class="text-base font-semibold text-gray-900">Total:</span>
                                <span class="text-lg font-bold text-gray-900">S/ {{ number_format($pedido->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="bg-white rounded-lg shadow p-6 no-print">
                <div class="flex flex-wrap gap-3">
                    <button onclick="cambiarEstado('preparando')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-utensils mr-2"></i>
                        Marcar como Preparando
                    </button>
                    <button onclick="cambiarEstado('listo')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-check mr-2"></i>
                        Marcar como Listo
                    </button>
                    <button onclick="cambiarEstado('completado')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-check-double mr-2"></i>
                        Marcar como Completado
                    </button>
                    <button onclick="cambiarEstado('cancelado')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cambiarEstado(nuevoEstado) {
            if (confirm(`¿Cambiar el estado del pedido a ${nuevoEstado}?`)) {
                fetch(`/pedidos/{{ $pedido->id }}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ estado: nuevoEstado })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error al actualizar el estado');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar el estado');
                });
            }
        }

        function generarBoleta() {
            const ventana = window.open('', '_blank', 'width=400,height=700');
            const pedido = @json($pedido);
            
            const boletaHTML = `
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <title>Boleta #${pedido.numero_venta || pedido.id}</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        body { 
                            font-family: 'Courier New', Courier, monospace; 
                            width: 380px; 
                            margin: 0 auto; 
                            padding: 20px;
                            font-size: 12px;
                            color: #000;
                        }
                        .header { 
                            text-align: center; 
                            margin-bottom: 20px;
                            padding-bottom: 10px;
                            border-bottom: 1px dashed #000;
                        }
                        .header h2 { 
                            font-size: 20px;
                            margin-bottom: 5px;
                        }
                        .header p {
                            margin: 3px 0;
                        }
                        .titulo-boleta {
                            font-size: 14px;
                            font-weight: bold;
                            margin: 10px 0;
                        }
                        .info-row {
                            margin: 5px 0;
                            display: flex;
                            justify-content: space-between;
                        }
                        .info-label {
                            font-weight: bold;
                        }
                        table { 
                            width: 100%;
                            margin: 15px 0;
                            border-collapse: collapse;
                        }
                        table th {
                            text-align: left;
                            padding: 5px 0;
                            border-bottom: 1px solid #000;
                            font-size: 11px;
                        }
                        table td {
                            padding: 5px 0;
                            font-size: 11px;
                        }
                        .text-right {
                            text-align: right;
                        }
                        .separador {
                            border-top: 1px dashed #000;
                            margin: 10px 0;
                        }
                        .totales {
                            margin-top: 10px;
                        }
                        .totales .row {
                            display: flex;
                            justify-content: space-between;
                            margin: 3px 0;
                        }
                        .total-final { 
                            font-size: 14px; 
                            font-weight: bold; 
                            margin-top: 5px;
                            padding-top: 5px;
                            border-top: 2px solid #000;
                        }
                        .footer {
                            text-align: center;
                            margin-top: 20px;
                            padding-top: 10px;
                            border-top: 1px dashed #000;
                        }
                        .footer p {
                            margin: 5px 0;
                            font-size: 11px;
                        }
                        
                        @media print {
                            body {
                                width: 100%;
                                padding: 10px;
                            }
                        }
                    </style>
                </head>
                <body onload="window.print(); setTimeout(() => window.close(), 1000);">
                    <div class="header">
                        <h2>FOODPOINT RESTAURANT</h2>
                        <p>RUC: 20123456789</p>
                        <p>Av. Principal 123, Centro</p>
                        <p>Tel: (01) 234-5678</p>
                    </div>
                    
                    <div class="titulo-boleta">
                        BOLETA DE VENTA ELECTRÓNICA<br>
                        ${pedido.numero_venta || 'B001-' + String(pedido.id).padStart(6, '0')}
                    </div>
                    
                    <div class="separador"></div>
                    
                    <div class="info-row">
                        <span class="info-label">Fecha:</span>
                        <span>${new Date(pedido.created_at).toLocaleString('es-PE', { 
                            timeZone: 'America/Lima',
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cliente:</span>
                        <span>${pedido.cliente_nombre || 'Cliente General'}</span>
                    </div>
                    ${pedido.mesa_numero ? `
                    <div class="info-row">
                        <span class="info-label">Mesa:</span>
                        <span>${pedido.mesa_numero}</span>
                    </div>` : ''}
                    
                    <div class="separador"></div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 15%">Cant</th>
                                <th style="width: 55%">Descripción</th>
                                <th style="width: 15%" class="text-right">P.Unit</th>
                                <th style="width: 15%" class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pedido.items.map(item => `
                                <tr>
                                    <td>${item.cantidad}</td>
                                    <td>${item.producto_nombre || 'Producto'}</td>
                                    <td class="text-right">${parseFloat(item.precio_unitario).toFixed(2)}</td>
                                    <td class="text-right">${parseFloat(item.subtotal).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    
                    <div class="separador"></div>
                    
                    <div class="totales">
                        <div class="row">
                            <span>SUBTOTAL:</span>
                            <span>S/ ${parseFloat(pedido.subtotal).toFixed(2)}</span>
                        </div>
                        ${pedido.descuento > 0 ? `
                        <div class="row">
                            <span>DESCUENTO:</span>
                            <span>-S/ ${parseFloat(pedido.descuento).toFixed(2)}</span>
                        </div>` : ''}
                        ${pedido.impuesto > 0 ? `
                        <div class="row">
                            <span>IGV (18%):</span>
                            <span>S/ ${parseFloat(pedido.impuesto).toFixed(2)}</span>
                        </div>` : ''}
                        <div class="row total-final">
                            <span>TOTAL:</span>
                            <span>S/ ${parseFloat(pedido.total).toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <p>¡Gracias por su preferencia!</p>
                        <p>Conserve su comprobante</p>
                        <p>No se aceptan devoluciones</p>
                    </div>
                </body>
                </html>
            `;
            
            ventana.document.write(boletaHTML);
            ventana.document.close();
        }

        // No hacer nada automático si viene print=1, el controlador maneja la vista de impresión
    </script>
</body>
</html>
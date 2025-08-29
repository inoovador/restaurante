{{-- Vista Blade del POS - Solo contenido HTML, sin layout --}}
<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Panel de Productos --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Productos</h2>
            
            {{-- Filtros --}}
            <div class="flex gap-4 mb-4">
                <input type="text" 
                       id="searchProduct" 
                       placeholder="Buscar producto..." 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Grid de Productos --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-96 overflow-y-auto">
                @foreach($productos as $producto)
                <div class="producto-card border rounded-lg p-3 hover:shadow-lg transition-shadow cursor-pointer" 
                     data-id="{{ $producto->id }}"
                     data-nombre="{{ $producto->nombre }}"
                     data-precio="{{ $producto->precio_venta }}"
                     data-categoria="{{ $producto->categoria_id }}"
                     onclick="agregarProducto({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->precio_venta }})">
                    <div class="w-full h-24 bg-gray-200 rounded flex items-center justify-center mb-2">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-sm mb-1">{{ $producto->nombre }}</h3>
                    <p class="text-blue-600 font-bold">${{ number_format($producto->precio_venta, 2) }}</p>
                    <p class="text-xs text-gray-500">Stock: {{ $producto->stock }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Panel de Carrito --}}
        <div class="bg-gray-50 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Orden Actual</h2>
            
            {{-- Selección Mesa/Cliente --}}
            <div class="space-y-3 mb-4">
                <select id="mesaSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Seleccionar mesa...</option>
                    @foreach($mesas as $mesa)
                        <option value="{{ $mesa->id }}" {{ $mesa->estado != 'disponible' ? 'disabled' : '' }}>
                            Mesa {{ $mesa->numero }} - {{ ucfirst($mesa->estado) }}
                        </option>
                    @endforeach
                </select>

                <select id="clienteSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Cliente general</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Items del Carrito --}}
            <div class="border-t pt-4">
                <div id="cartItems" class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                    {{-- Los items se agregarán dinámicamente con JavaScript --}}
                </div>

                {{-- Totales --}}
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>IVA (16%):</span>
                        <span id="iva">$0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span id="total">$0.00</span>
                    </div>
                </div>

                {{-- Método de Pago --}}
                <select id="metodoPago" class="w-full px-3 py-2 border border-gray-300 rounded-md mt-4">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                </select>

                {{-- Botones --}}
                <div class="mt-6 space-y-2">
                    <button onclick="procesarVenta()" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 font-semibold">
                        Procesar Venta
                    </button>
                    <button onclick="limpiarCarrito()" class="w-full bg-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-400">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carrito de compras
    let carrito = [];
    
    // Hacer funciones globales para que los onclick funcionen
    window.agregarProducto = function(id, nombre, precio) {
        const itemExistente = carrito.find(item => item.id === id);
        
        if (itemExistente) {
            itemExistente.cantidad++;
        } else {
            carrito.push({
                id: id,
                nombre: nombre,
                precio: precio,
                cantidad: 1
            });
        }
        
        actualizarCarrito();
    };

    window.cambiarCantidad = function(index, delta) {
        carrito[index].cantidad += delta;
        if (carrito[index].cantidad <= 0) {
            carrito.splice(index, 1);
        }
        actualizarCarrito();
    };

    window.eliminarItem = function(index) {
        carrito.splice(index, 1);
        actualizarCarrito();
    };

    window.limpiarCarrito = function() {
        if (confirm('¿Limpiar el carrito?')) {
            carrito = [];
            actualizarCarrito();
        }
    };

    window.procesarVenta = function() {
        if (carrito.length === 0) {
            alert('El carrito está vacío');
            return;
        }

        const mesa = document.getElementById('mesaSelect').value;
        const cliente = document.getElementById('clienteSelect').value;
        const metodoPago = document.getElementById('metodoPago').value;

        if (!mesa && !cliente) {
            alert('Seleccione una mesa o un cliente');
            return;
        }

        const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        const iva = subtotal * 0.16;
        const total = subtotal + iva;

        const datos = {
            mesa_id: mesa,
            cliente_id: cliente,
            metodo_pago: metodoPago,
            productos: carrito,
            subtotal: subtotal,
            iva: iva,
            total: total
        };

        fetch('{{ route("ventas.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Venta procesada exitosamente');
                carrito = [];
                actualizarCarrito();
                document.getElementById('mesaSelect').value = '';
                document.getElementById('clienteSelect').value = '';
            } else {
                alert('Error: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la venta');
        });
    };

function actualizarCarrito() {
    const cartItemsDiv = document.getElementById('cartItems');
    cartItemsDiv.innerHTML = '';
    
    let subtotal = 0;
    
    carrito.forEach((item, index) => {
        const itemTotal = item.precio * item.cantidad;
        subtotal += itemTotal;
        
        const itemDiv = document.createElement('div');
        itemDiv.className = 'flex justify-between items-center p-2 bg-white rounded';
        itemDiv.innerHTML = `
            <div>
                <p class="font-semibold text-sm">${item.nombre}</p>
                <p class="text-xs text-gray-500">$${item.precio.toFixed(2)} x ${item.cantidad}</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="cambiarCantidad(${index}, -1)" class="w-6 h-6 bg-gray-200 rounded hover:bg-gray-300">-</button>
                <span>${item.cantidad}</span>
                <button onclick="cambiarCantidad(${index}, 1)" class="w-6 h-6 bg-gray-200 rounded hover:bg-gray-300">+</button>
                <button onclick="eliminarItem(${index})" class="text-red-500 hover:text-red-700 ml-2">×</button>
            </div>
        `;
        cartItemsDiv.appendChild(itemDiv);
    });
    
    const iva = subtotal * 0.16;
    const total = subtotal + iva;
    
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('iva').textContent = `$${iva.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

    // Filtros
    const searchProduct = document.getElementById('searchProduct');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (searchProduct) {
        searchProduct.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.producto-card').forEach(producto => {
                const nombre = (producto.dataset.nombre || '').toLowerCase();
                producto.style.display = nombre.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', function(e) {
            const categoriaId = e.target.value;
            document.querySelectorAll('.producto-card').forEach(producto => {
                if (!categoriaId || producto.dataset.categoria === categoriaId) {
                    producto.style.display = 'block';
                } else {
                    producto.style.display = 'none';
                }
            });
        });
    }
    
}); // Cerrar DOMContentLoaded

// También escuchar el evento personalizado de HybridPage
window.addEventListener('hybrid-content-loaded', function() {
    // Re-inicializar filtros si es necesario
    const searchProduct = document.getElementById('searchProduct');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (searchProduct && !searchProduct.hasAttribute('data-listener')) {
        searchProduct.setAttribute('data-listener', 'true');
        searchProduct.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.producto-card').forEach(producto => {
                const nombre = (producto.dataset.nombre || '').toLowerCase();
                producto.style.display = nombre.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    if (categoryFilter && !categoryFilter.hasAttribute('data-listener')) {
        categoryFilter.setAttribute('data-listener', 'true');
        categoryFilter.addEventListener('change', function(e) {
            const categoriaId = e.target.value;
            document.querySelectorAll('.producto-card').forEach(producto => {
                if (!categoriaId || producto.dataset.categoria === categoriaId) {
                    producto.style.display = 'block';
                } else {
                    producto.style.display = 'none';
                }
            });
        });
    }
});
</script>
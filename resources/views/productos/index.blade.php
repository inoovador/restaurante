<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Productos - FoodPoint</title>
    
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
                            <h1 class="text-2xl font-bold text-gray-900">Gestión de Productos</h1>
                            <p class="text-sm text-gray-500">Administra tu menú y precios</p>
                        </div>
                    </div>
                    <button onclick="openAddModal()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all">
                        <i class="fas fa-plus mr-2"></i>
                        Nuevo Producto
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Productos</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Activos</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['activos'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Stock Bajo</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $stats['stock_bajo'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Sin Stock</p>
                            <p class="text-3xl font-bold text-red-600">{{ $stats['sin_stock'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Productos -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Todos los Productos</h3>
                    <div class="flex items-center space-x-3">
                        <!-- Buscador -->
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchProduct" placeholder="Buscar producto..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                   onkeyup="searchProducts()">
                        </div>
                        <!-- Filtro por categoría -->
                        <select id="filterCategory" onchange="filterByCategory()" 
                                class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="productosTable">
                            @forelse($productos as $producto)
                            <tr class="hover:bg-gray-50" data-categoria="{{ $producto->categoria_id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if($producto->imagen)
                                                <img class="h-12 w-12 rounded-lg object-cover" 
                                                     src="/{{ $producto->imagen }}" 
                                                     alt="{{ $producto->nombre }}"
                                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%23d1d5db\'%3E%3Cpath d=\'M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zM2 3.993A1 1 0 0 1 2.992 3h18.016c.548 0 .992.445.992.993v16.014a1 1 0 0 1-.992.993H2.992A.993.993 0 0 1 2 20.007V3.993zM8 11l5 6 3-4 4 5H4l4-7z\'/%3E%3C/svg%3E';">
                                            @else
                                                <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400 text-xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($producto->descripcion, 30) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $producto->codigo }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          style="background-color: {{ $producto->categoria_color }}20; color: {{ $producto->categoria_color }}">
                                        {{ $producto->categoria_nombre }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    S/. {{ number_format($producto->precio_venta, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($producto->stock <= 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Sin stock
                                        </span>
                                    @elseif($producto->stock <= $producto->stock_minimo)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $producto->stock }} unidades
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $producto->stock }} unidades
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($producto->activo)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editProducto({{ json_encode($producto) }})" class="text-indigo-600 hover:text-indigo-900 mr-2" title="Editar producto">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="editProductImage({{ json_encode($producto) }})" class="text-green-600 hover:text-green-900 mr-2" title="Cambiar imagen">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button onclick="deleteProducto({{ $producto->id }})" class="text-red-600 hover:text-red-900" title="Eliminar producto">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No hay productos registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar producto -->
    <div id="productoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Nuevo Producto</h3>
            </div>
            <form id="productoForm" onsubmit="saveProducto(event)" enctype="multipart/form-data">
                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" id="producto_id">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                            <input type="text" id="codigo" name="codigo" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría *</label>
                            <select id="categoria_id" name="categoria_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Seleccionar categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Precio Venta *</label>
                            <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Inicial *</label>
                            <input type="number" id="stock" name="stock" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                    
                    <!-- Upload de Imagen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imagen del Producto</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-md p-4">
                            <div class="text-center">
                                <div id="imagePreview" class="mb-4 hidden">
                                    <img id="previewImg" class="mx-auto h-32 w-32 object-cover rounded-lg">
                                    <div class="mt-2 space-x-2">
                                        <button type="button" onclick="changeImage()" class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-edit mr-1"></i>Cambiar imagen
                                        </button>
                                        <button type="button" onclick="removeImage()" class="text-red-600 hover:text-red-800 text-sm">
                                            <i class="fas fa-trash mr-1"></i>Eliminar imagen
                                        </button>
                                    </div>
                                </div>
                                <div id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600">Arrastra una imagen aquí o</p>
                                    <label class="cursor-pointer inline-block mt-2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                        Seleccionar archivo
                                        <input type="file" id="imagen" name="imagen" accept="image/*" class="hidden" onchange="previewImage(this)">
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF hasta 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="activo" name="activo" checked
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-600">Producto activo</span>
                        </label>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para abrir modal de nuevo producto
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Nuevo Producto';
            document.getElementById('productoForm').reset();
            document.getElementById('producto_id').value = '';
            resetImagePreview();
            document.getElementById('productoModal').classList.remove('hidden');
        }

        // Función para editar producto
        function editProducto(producto) {
            document.getElementById('modalTitle').textContent = 'Editar Producto';
            document.getElementById('producto_id').value = producto.id;
            document.getElementById('codigo').value = producto.codigo;
            document.getElementById('nombre').value = producto.nombre;
            document.getElementById('descripcion').value = producto.descripcion || '';
            document.getElementById('categoria_id').value = producto.categoria_id;
            document.getElementById('precio_venta').value = producto.precio_venta;
            document.getElementById('stock').value = producto.stock;
            document.getElementById('activo').checked = producto.activo;
            
            // Mostrar imagen actual si existe
            if (producto.imagen) {
                showImagePreview('/' + producto.imagen);
            } else {
                resetImagePreview();
            }
            
            document.getElementById('productoModal').classList.remove('hidden');
        }

        // Función para cerrar modal
        function closeModal() {
            document.getElementById('productoModal').classList.add('hidden');
        }

        // Función para preview de imagen
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    showImagePreview(e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function showImagePreview(src) {
            document.getElementById('previewImg').src = src;
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('uploadPlaceholder').classList.add('hidden');
        }

        function changeImage() {
            document.getElementById('imagen').click();
        }

        function removeImage() {
            document.getElementById('imagen').value = '';
            resetImagePreview();
        }

        function resetImagePreview() {
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('uploadPlaceholder').classList.remove('hidden');
        }

        // Función para guardar producto
        async function saveProducto(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const id = document.getElementById('producto_id').value;
            const url = id ? `/productos/${id}` : '/productos';
            
            // Si es actualización, agregar método PUT
            if (id) {
                formData.append('_method', 'PUT');
            }
            
            formData.append('activo', document.getElementById('activo').checked ? 1 : 0);
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if (response.ok) {
                    closeModal();
                    location.reload();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'No se pudo guardar el producto'));
                }
            } catch (error) {
                alert('Error de conexión: ' + error.message);
            }
        }

        // Función para eliminar producto
        async function deleteProducto(id) {
            if (!confirm('¿Está seguro de eliminar este producto?')) {
                return;
            }
            
            try {
                const response = await fetch(`/productos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    location.reload();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'No se pudo eliminar el producto'));
                }
            } catch (error) {
                alert('Error de conexión: ' + error.message);
            }
        }

        // Función para buscar productos
        function searchProducts() {
            const searchValue = document.getElementById('searchProduct').value.toLowerCase();
            const categoryFilter = document.getElementById('filterCategory').value;
            const rows = document.querySelectorAll('#productosTable tr');
            
            rows.forEach(row => {
                const productName = row.cells[0]?.textContent.toLowerCase() || '';
                const productCode = row.cells[1]?.textContent.toLowerCase() || '';
                const categoryId = row.getAttribute('data-categoria') || '';
                
                const matchesSearch = productName.includes(searchValue) || productCode.includes(searchValue);
                const matchesCategory = !categoryFilter || categoryId === categoryFilter;
                
                row.style.display = matchesSearch && matchesCategory ? '' : 'none';
            });
        }

        // Función para filtrar por categoría
        function filterByCategory() {
            searchProducts();
        }

        // Función para editar solo la imagen del producto
        function editProductImage(producto) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg w-full max-w-md mx-4">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Cambiar imagen de ${producto.nombre}</h3>
                    </div>
                    <form id="imageForm">
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <div class="border-2 border-dashed border-gray-300 rounded-md p-4">
                                    <div class="text-center">
                                        ${producto.imagen ? `
                                            <img src="/${producto.imagen}" class="mx-auto h-32 w-32 object-cover rounded-lg mb-4">
                                        ` : `
                                            <div class="h-32 w-32 mx-auto rounded-lg bg-gray-100 flex items-center justify-center mb-4">
                                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                                            </div>
                                        `}
                                        <div id="newImagePreview" class="hidden mb-4">
                                            <p class="text-sm text-gray-600 mb-2">Nueva imagen:</p>
                                            <img id="newPreviewImg" class="mx-auto h-32 w-32 object-cover rounded-lg">
                                        </div>
                                        <label class="cursor-pointer inline-block mt-2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                            Seleccionar nueva imagen
                                            <input type="file" id="newImage" name="imagen" accept="image/*" class="hidden" onchange="previewNewImage(this)">
                                        </label>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF hasta 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t flex justify-end space-x-3">
                            <button type="button" onclick="closeImageModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="button" onclick="updateProductImage(${producto.id})"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Actualizar Imagen
                            </button>
                        </div>
                    </form>
                </div>
            `;
            document.body.appendChild(modal);
            window.currentImageModal = modal;
        }

        function previewNewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('newPreviewImg').src = e.target.result;
                    document.getElementById('newImagePreview').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function closeImageModal() {
            if (window.currentImageModal) {
                window.currentImageModal.remove();
                window.currentImageModal = null;
            }
        }

        async function updateProductImage(productId) {
            const fileInput = document.getElementById('newImage');
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Por favor seleccione una imagen');
                return;
            }

            const formData = new FormData();
            formData.append('imagen', fileInput.files[0]);
            formData.append('_method', 'POST');
            
            try {
                const response = await fetch(`/productos/${productId}/imagen`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if (response.ok) {
                    closeImageModal();
                    location.reload();
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'No se pudo actualizar la imagen'));
                }
            } catch (error) {
                alert('Error de conexión: ' + error.message);
            }
        }
    </script>
</body>
</html>
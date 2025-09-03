// Búsqueda y filtros optimizados para el dashboard
document.addEventListener('DOMContentLoaded', function() {
    
    // Debounce function para optimizar búsquedas
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Cache para resultados de búsqueda
    const searchCache = new Map();
    const CACHE_EXPIRY = 5 * 60 * 1000; // 5 minutos

    // Elementos del DOM
    const searchInput = document.querySelector('#search-input');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const productGrid = document.querySelector('.products-grid');
    const resultsInfo = document.querySelector('.results-info');
    const paginationContainer = document.querySelector('.pagination-container');
    const perPageSelect = document.querySelector('#per-page-select');

    // Estado actual de filtros
    let currentFilters = {
        search: '',
        category: '',
        perPage: 12,
        page: 1
    };

    // Función para obtener productos con cache
    async function fetchProducts(filters) {
        const cacheKey = JSON.stringify(filters);
        const cached = searchCache.get(cacheKey);
        
        if (cached && (Date.now() - cached.timestamp) < CACHE_EXPIRY) {
            return cached.data;
        }

        try {
            const params = new URLSearchParams();
            if (filters.search) params.append('search', filters.search);
            if (filters.category) params.append('categoria_id', filters.category);
            if (filters.perPage) params.append('per_page', filters.perPage);
            if (filters.page) params.append('page', filters.page);

            const response = await fetch(`${window.location.pathname}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            // Guardar en cache
            searchCache.set(cacheKey, {
                data: data,
                timestamp: Date.now()
            });

            return data;
        } catch (error) {
            console.error('Error al buscar productos:', error);
            return null;
        }
    }

    // Función para actualizar la URL sin recargar la página
    function updateURL(filters) {
        const params = new URLSearchParams();
        if (filters.search) params.append('search', filters.search);
        if (filters.category) params.append('categoria_id', filters.category);
        if (filters.perPage !== 12) params.append('per_page', filters.perPage);
        if (filters.page !== 1) params.append('page', filters.page);

        const newURL = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.replaceState({}, '', newURL);
    }

    // Función para mostrar skeleton loader
    function showLoadingState() {
        if (productGrid) {
            productGrid.classList.add('loading');
            productGrid.innerHTML = Array(currentFilters.perPage).fill(0).map(() => `
                <div class="bg-white rounded-lg shadow-sm overflow-hidden animate-pulse">
                    <div class="h-48 bg-gray-200"></div>
                    <div class="p-4">
                        <div class="h-4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded mb-2"></div>
                        <div class="h-6 bg-gray-200 rounded"></div>
                    </div>
                </div>
            `).join('');
        }
    }

    // Función para renderizar productos
    function renderProducts(products) {
        if (!productGrid) return;

        productGrid.classList.remove('loading');
        
        if (products.length === 0) {
            productGrid.innerHTML = `
                <div class="col-span-full flex flex-col items-center justify-center py-12">
                    <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                    <p class="text-lg text-gray-500">No se encontraron productos</p>
                    <p class="text-sm text-gray-400">Intenta con otros términos de búsqueda</p>
                </div>
            `;
            return;
        }

        productGrid.innerHTML = products.map(product => `
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group">
                <div class="relative h-48 overflow-hidden bg-gray-100">
                    <img 
                        data-src="${product.imagen_url || getDefaultImage(product.categoria_nombre)}" 
                        alt="${product.nombre}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200 lazy"
                    >
                    ${product.stock < 10 ? `<span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">¡Últimas ${product.stock}!</span>` : ''}
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-medium text-gray-900 line-clamp-2">${product.nombre}</h3>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">${product.categoria_nombre}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-green-600">$${product.precio_venta}</span>
                        <button 
                            onclick="addToCart(${product.id})"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors"
                        >
                            <i class="fas fa-plus mr-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        // Re-inicializar lazy loading para las nuevas imágenes
        if (window.lazyLoadingUtils) {
            window.lazyLoadingUtils.initLazyLoading();
        }
    }

    // Función para obtener imagen por defecto según categoría
    function getDefaultImage(categoria) {
        const defaultImages = {
            'Bebidas': '/images/categories/bebidas.jpg',
            'Pizzas': '/images/categories/pizzas.jpg',
            'Ensaladas': '/images/categories/ensaladas.jpg',
            'Postres': '/images/categories/postres.jpg',
            'Platos Principales': '/images/categories/platos.jpg'
        };
        return defaultImages[categoria] || '/images/no-image.jpg';
    }

    // Función para actualizar información de resultados
    function updateResultsInfo(total, showing) {
        if (resultsInfo) {
            resultsInfo.textContent = `Mostrando ${showing} de ${total} productos`;
        }
    }

    // Función principal para aplicar filtros
    const applyFilters = debounce(async function() {
        showLoadingState();
        updateURL(currentFilters);
        
        const data = await fetchProducts(currentFilters);
        
        if (data && data.products) {
            renderProducts(data.products.data || data.products);
            updateResultsInfo(data.total || data.products.length, data.products.length);
            
            // Actualizar paginación si existe
            if (data.pagination && paginationContainer) {
                updatePagination(data.pagination);
            }
        }
    }, 300);

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            currentFilters.search = e.target.value;
            currentFilters.page = 1;
            applyFilters();
        });
    }

    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Actualizar estado visual
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            currentFilters.category = this.dataset.categoryId || '';
            currentFilters.page = 1;
            applyFilters();
        });
    });

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function(e) {
            currentFilters.perPage = parseInt(e.target.value);
            currentFilters.page = 1;
            applyFilters();
        });
    }

    // Función para limpiar cache viejo
    function cleanupCache() {
        const now = Date.now();
        for (const [key, value] of searchCache.entries()) {
            if (now - value.timestamp > CACHE_EXPIRY) {
                searchCache.delete(key);
            }
        }
    }

    // Limpiar cache cada 5 minutos
    setInterval(cleanupCache, 5 * 60 * 1000);

    // Inicializar filtros desde URL
    function initFromURL() {
        const params = new URLSearchParams(window.location.search);
        currentFilters.search = params.get('search') || '';
        currentFilters.category = params.get('categoria_id') || '';
        currentFilters.perPage = parseInt(params.get('per_page')) || 12;
        currentFilters.page = parseInt(params.get('page')) || 1;

        // Actualizar UI
        if (searchInput) searchInput.value = currentFilters.search;
        if (perPageSelect) perPageSelect.value = currentFilters.perPage;
        
        categoryButtons.forEach(btn => {
            if (btn.dataset.categoryId === currentFilters.category) {
                btn.classList.add('active');
            }
        });
    }

    // Inicializar
    initFromURL();

    console.log('Sistema de búsqueda y filtros optimizado inicializado');
});
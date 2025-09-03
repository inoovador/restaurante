// Lazy Loading de Imágenes y Optimizaciones de Performance
document.addEventListener('DOMContentLoaded', function() {
    // Configuración del Intersection Observer para lazy loading
    const lazyImageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.getAttribute('data-src');
                
                if (src) {
                    // Crear una nueva imagen para precargar
                    const newImg = new Image();
                    
                    // Mostrar skeleton loader
                    img.classList.add('loading');
                    
                    newImg.onload = function() {
                        img.src = src;
                        img.classList.remove('loading');
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    };
                    
                    newImg.onerror = function() {
                        // Usar imagen por defecto si falla la carga
                        img.src = '/images/no-image.jpg';
                        img.classList.remove('loading');
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    };
                    
                    newImg.src = src;
                }
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '50px'
    });

    // Aplicar lazy loading a todas las imágenes con data-src
    function initLazyLoading() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            lazyImageObserver.observe(img);
        });
    }

    // Optimización de scroll suave
    let ticking = false;
    function optimizeScroll() {
        if (!ticking) {
            requestAnimationFrame(() => {
                // Lógica de scroll optimizada aquí
                ticking = false;
            });
            ticking = true;
        }
    }

    // Debounce para búsqueda
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

    // Cache simple para productos
    const productCache = new Map();
    
    function cacheProduct(id, data) {
        productCache.set(id, {
            data: data,
            timestamp: Date.now()
        });
    }
    
    function getCachedProduct(id, maxAge = 300000) { // 5 minutos
        const cached = productCache.get(id);
        if (cached && (Date.now() - cached.timestamp) < maxAge) {
            return cached.data;
        }
        return null;
    }

    // Optimización de imágenes de productos existentes
    function optimizeExistingImages() {
        const images = document.querySelectorAll('.product-image');
        images.forEach(img => {
            // Si la imagen no tiene data-src, convertirla a lazy loading
            if (!img.hasAttribute('data-src') && img.src) {
                const src = img.src;
                img.setAttribute('data-src', src);
                img.src = '';
                img.classList.add('lazy');
                lazyImageObserver.observe(img);
            }
        });
    }

    // Precargar imágenes críticas
    function preloadCriticalImages() {
        const criticalImages = [
            '/images/logo.jpeg',
            // Agregar más imágenes críticas aquí
        ];
        
        criticalImages.forEach(src => {
            const img = new Image();
            img.src = src;
        });
    }

    // Optimización de memoria - limpiar cache viejo
    function cleanupCache() {
        const now = Date.now();
        const maxAge = 600000; // 10 minutos
        
        for (const [key, value] of productCache.entries()) {
            if (now - value.timestamp > maxAge) {
                productCache.delete(key);
            }
        }
    }

    // Virtual scrolling simple para listas largas
    function initVirtualScrolling(container, itemHeight = 200) {
        if (!container) return;
        
        const items = container.querySelectorAll('.product-item');
        if (items.length < 20) return; // Solo si hay muchos items
        
        let scrollTop = 0;
        const containerHeight = container.clientHeight;
        const visibleCount = Math.ceil(containerHeight / itemHeight) + 2;
        
        function updateVisibleItems() {
            const startIndex = Math.floor(scrollTop / itemHeight);
            const endIndex = Math.min(startIndex + visibleCount, items.length);
            
            items.forEach((item, index) => {
                if (index >= startIndex && index < endIndex) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        container.addEventListener('scroll', debounce(() => {
            scrollTop = container.scrollTop;
            updateVisibleItems();
        }, 16));
        
        updateVisibleItems();
    }

    // Inicializar todas las optimizaciones
    function initialize() {
        preloadCriticalImages();
        initLazyLoading();
        optimizeExistingImages();
        
        // Limpiar cache cada 5 minutos
        setInterval(cleanupCache, 300000);
        
        // Configurar virtual scrolling para contenedores de productos
        const productContainer = document.querySelector('.products-grid');
        if (productContainer) {
            initVirtualScrolling(productContainer);
        }
        
        console.log('Optimizaciones de performance inicializadas');
    }

    // Inicializar cuando el DOM esté listo
    initialize();

    // Exponer funciones útiles globalmente
    window.lazyLoadingUtils = {
        initLazyLoading,
        cacheProduct,
        getCachedProduct,
        debounce
    };
});

// CSS para skeleton loader (agregar al head)
const style = document.createElement('style');
style.textContent = `
    .lazy {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    .loading {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    .loaded {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .product-image {
        transition: opacity 0.3s ease;
    }
`;
document.head.appendChild(style);
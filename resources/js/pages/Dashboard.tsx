import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
    Search,
    ShoppingCart,
    Plus,
    Minus,
    X,
    Coffee,
    Pizza,
    Salad,
    Soup,
    IceCream,
    Wine,
    Utensils,
    ChefHat,
    TrendingUp,
    Package,
    Users,
    DollarSign,
    Clock,
    Filter,
    Grid3x3,
    List,
    Star,
    Receipt,
    CreditCard,
    BarChart3,
    Settings,
    Calendar,
    AlertTriangle,
    CheckCircle,
    XCircle,
    ArrowUpRight,
    ArrowDownRight,
    MapPin,
    UserCheck,
    Store
} from 'lucide-react';
import { useState } from 'react';

interface DashboardProps {
    stats: {
        categorias: number;
        productos: number;
        mesas: number;
        clientes: number;
        ventas_hoy: number;
        mesas_disponibles: number;
        ingresos_hoy: number;
        ingresos_mes: number;
        total_mesas: number;
        mesas_ocupadas: number;
        productos_stock_bajo: number;
    };
    categorias: Array<{
        id: number;
        nombre: string;
        tipo: string;
        area: string;
        color: string;
    }>;
    productos_recientes: Array<{
        id: number;
        codigo: string;
        nombre: string;
        precio_venta: number;
        stock: number;
        categoria_nombre: string;
    }>;
    mesas: Array<{
        numero: string;
        capacidad: number;
        estado: string;
        zona: string;
    }>;
    alertas?: Array<{
        tipo: 'warning' | 'error' | 'info';
        mensaje: string;
        modulo: string;
    }>;
    actividad_reciente?: Array<{
        tipo: string;
        descripcion: string;
        tiempo: string;
        usuario: string;
    }>;
}

// Colores corporativos
const COLORS = {
    primary: '#E32636',
    secondary: '#4d82bc',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    dark: '#1f2937',
};

// Iconos para categorías
const categoryIcons: { [key: string]: any } = {
    'Bebidas': Coffee,
    'Pizzas': Pizza,
    'Ensaladas': Salad,
    'Sopas': Soup,
    'Postres': IceCream,
    'Vinos': Wine,
    'Platos Principales': Utensils,
    'default': ChefHat
};

// Generar productos de ejemplo con imágenes placeholder
const generateProducts = (categorias: any[], productos: any[]) => {
    const productImages = [
        'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=400&h=300&fit=crop',
    ];

    return productos.map((producto, index) => ({
        ...producto,
        image: productImages[index % productImages.length],
        description: `Delicioso ${producto.nombre.toLowerCase()} preparado con ingredientes frescos`,
        rating: (4 + Math.random()).toFixed(1),
        tiempo: `${15 + Math.floor(Math.random() * 20)} min`,
        calorias: `${200 + Math.floor(Math.random() * 300)} cal`
    }));
};

export default function Dashboard({ stats, categorias, productos_recientes, mesas, alertas = [], actividad_reciente = [] }: DashboardProps) {
    const [selectedCategory, setSelectedCategory] = useState<number | null>(null);
    const [searchTerm, setSearchTerm] = useState('');
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
    const [cart, setCart] = useState<any[]>([]);
    const [showCart, setShowCart] = useState(true);
    
    const products = generateProducts(categorias, productos_recientes);
    
    // Filtrar productos
    const filteredProducts = products.filter(product => {
        const matchesSearch = product.nombre.toLowerCase().includes(searchTerm.toLowerCase());
        const matchesCategory = !selectedCategory || product.categoria_id === selectedCategory;
        return matchesSearch && matchesCategory;
    });

    // Funciones del carrito
    const addToCart = (product: any) => {
        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            setCart(cart.map(item => 
                item.id === product.id 
                    ? { ...item, quantity: item.quantity + 1 }
                    : item
            ));
        } else {
            setCart([...cart, { ...product, quantity: 1 }]);
        }
    };

    const updateQuantity = (productId: number, delta: number) => {
        setCart(cart.map(item => {
            if (item.id === productId) {
                const newQuantity = item.quantity + delta;
                return newQuantity > 0 ? { ...item, quantity: newQuantity } : null;
            }
            return item;
        }).filter(Boolean) as any[]);
    };

    const removeFromCart = (productId: number) => {
        setCart(cart.filter(item => item.id !== productId));
    };

    const getSubtotal = () => {
        return cart.reduce((sum, item) => sum + (item.precio_venta * item.quantity), 0);
    };

    const getTax = () => {
        return getSubtotal() * 0.16;
    };

    const getTotal = () => {
        return getSubtotal() + getTax();
    };

    return (
        <>
            <Head title="FoodPoint - Dashboard" />
            
            <div className="flex h-screen bg-gray-50">
                {/* Sidebar Izquierdo - Navegación */}
                <motion.div 
                    initial={{ x: -300 }}
                    animate={{ x: 0 }}
                    className="w-64 bg-white border-r border-gray-200 flex flex-col"
                >
                    {/* Logo y Título */}
                    <div className="p-6 border-b">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-lg flex items-center justify-center" 
                                 style={{ backgroundColor: COLORS.primary }}>
                                <Utensils className="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h1 className="text-xl font-bold text-gray-900">FoodPoint</h1>
                                <p className="text-xs text-gray-500">Sistema de Gestión</p>
                            </div>
                        </div>
                    </div>

                    {/* Estadísticas Rápidas */}
                    <div className="p-4 space-y-2">
                        <div className="text-xs font-semibold text-gray-500 mb-2">RESUMEN DE HOY</div>
                        <div className="space-y-2">
                            <div className="flex justify-between items-center p-2 rounded-lg bg-green-50">
                                <span className="text-sm text-gray-700">Ventas</span>
                                <span className="text-sm font-bold text-green-600">
                                    ${stats.ventas_hoy?.toLocaleString() || 0}
                                </span>
                            </div>
                            <div className="flex justify-between items-center p-2 rounded-lg bg-blue-50">
                                <span className="text-sm text-gray-700">Pedidos</span>
                                <span className="text-sm font-bold text-blue-600">
                                    {Math.floor(stats.ventas_hoy / 150)}
                                </span>
                            </div>
                            <div className="flex justify-between items-center p-2 rounded-lg bg-purple-50">
                                <span className="text-sm text-gray-700">Mesas Libres</span>
                                <span className="text-sm font-bold text-purple-600">
                                    {stats.mesas_disponibles}/{stats.mesas}
                                </span>
                            </div>
                        </div>
                    </div>

                    <Separator className="my-2" />

                    {/* Categorías */}
                    <div className="flex-1 overflow-y-auto p-4">
                        <div className="text-xs font-semibold text-gray-500 mb-3">CATEGORÍAS</div>
                        <div className="space-y-1">
                            <motion.button
                                whileHover={{ x: 5 }}
                                whileTap={{ scale: 0.95 }}
                                onClick={() => setSelectedCategory(null)}
                                className={`w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-colors ${
                                    !selectedCategory 
                                        ? 'bg-red-50 text-red-700 border-l-4 border-red-500' 
                                        : 'hover:bg-gray-50 text-gray-700'
                                }`}
                            >
                                <Grid3x3 className="w-4 h-4" />
                                <span className="text-sm font-medium">Todos los productos</span>
                                <Badge variant="secondary" className="ml-auto">
                                    {productos_recientes?.length || 0}
                                </Badge>
                            </motion.button>

                            {categorias?.map((categoria) => {
                                const Icon = categoryIcons[categoria.nombre] || categoryIcons['default'];
                                const productCount = productos_recientes?.filter(p => p.categoria_id === categoria.id).length || 0;
                                
                                return (
                                    <motion.button
                                        key={categoria.id}
                                        whileHover={{ x: 5 }}
                                        whileTap={{ scale: 0.95 }}
                                        onClick={() => setSelectedCategory(categoria.id)}
                                        className={`w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-colors ${
                                            selectedCategory === categoria.id 
                                                ? 'bg-red-50 text-red-700 border-l-4 border-red-500' 
                                                : 'hover:bg-gray-50 text-gray-700'
                                        }`}
                                    >
                                        <Icon className="w-4 h-4" />
                                        <span className="text-sm font-medium">{categoria.nombre}</span>
                                        <Badge variant="secondary" className="ml-auto">
                                            {productCount}
                                        </Badge>
                                    </motion.button>
                                );
                            })}
                        </div>
                    </div>

                    {/* Footer Sidebar */}
                    <div className="p-4 border-t">
                        <div className="flex items-center gap-2 text-xs text-gray-500">
                            <Clock className="w-3 h-3" />
                            <span>Actualizado: {new Date().toLocaleTimeString()}</span>
                        </div>
                    </div>
                </motion.div>

                {/* Contenido Principal */}
                <div className="flex-1 flex flex-col overflow-hidden">
                    {/* Header */}
                    <motion.div 
                        initial={{ y: -50, opacity: 0 }}
                        animate={{ y: 0, opacity: 1 }}
                        className="bg-white border-b border-gray-200 p-4"
                    >
                        <div className="flex items-center justify-between">
                            <div className="flex-1 max-w-2xl">
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                                    <Input
                                        type="text"
                                        placeholder="Buscar productos..."
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        className="pl-10 pr-4 py-2 w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            <div className="flex items-center gap-4 ml-4">
                                {/* Filtros */}
                                <Button variant="outline" size="sm" className="gap-2">
                                    <Filter className="w-4 h-4" />
                                    Filtros
                                </Button>

                                {/* Vista */}
                                <div className="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                                    <button
                                        onClick={() => setViewMode('grid')}
                                        className={`p-1.5 rounded transition-colors ${
                                            viewMode === 'grid' 
                                                ? 'bg-white text-gray-900 shadow-sm' 
                                                : 'text-gray-500 hover:text-gray-700'
                                        }`}
                                    >
                                        <Grid3x3 className="w-4 h-4" />
                                    </button>
                                    <button
                                        onClick={() => setViewMode('list')}
                                        className={`p-1.5 rounded transition-colors ${
                                            viewMode === 'list' 
                                                ? 'bg-white text-gray-900 shadow-sm' 
                                                : 'text-gray-500 hover:text-gray-700'
                                        }`}
                                    >
                                        <List className="w-4 h-4" />
                                    </button>
                                </div>

                                {/* Botón Carrito Móvil */}
                                <Button
                                    onClick={() => setShowCart(!showCart)}
                                    className="lg:hidden relative gap-2"
                                    style={{ backgroundColor: COLORS.primary }}
                                >
                                    <ShoppingCart className="w-4 h-4" />
                                    {cart.length > 0 && (
                                        <Badge className="absolute -top-2 -right-2 bg-yellow-500">
                                            {cart.reduce((sum, item) => sum + item.quantity, 0)}
                                        </Badge>
                                    )}
                                </Button>
                            </div>
                        </div>
                    </motion.div>

                    {/* Grid de Productos */}
                    <div className="flex-1 overflow-y-auto p-6">
                        <AnimatePresence mode="wait">
                            {viewMode === 'grid' ? (
                                <motion.div 
                                    key="grid"
                                    initial={{ opacity: 0, scale: 0.9 }}
                                    animate={{ opacity: 1, scale: 1 }}
                                    exit={{ opacity: 0, scale: 0.9 }}
                                    className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4"
                                >
                                    {filteredProducts.map((product, index) => (
                                        <motion.div
                                            key={product.id}
                                            initial={{ opacity: 0, y: 20 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            transition={{ delay: index * 0.05 }}
                                            whileHover={{ y: -5, transition: { duration: 0.2 } }}
                                            className="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all cursor-pointer overflow-hidden group"
                                        >
                                            {/* Imagen del Producto */}
                                            <div className="relative h-48 overflow-hidden bg-gray-100">
                                                <img 
                                                    src={product.image} 
                                                    alt={product.nombre}
                                                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                                />
                                                {product.stock < 10 && (
                                                    <Badge className="absolute top-2 left-2 bg-red-500 text-white">
                                                        ¡Últimas {product.stock}!
                                                    </Badge>
                                                )}
                                                <div className="absolute top-2 right-2 flex items-center gap-1 bg-white/90 backdrop-blur px-2 py-1 rounded-full">
                                                    <Star className="w-3 h-3 fill-yellow-400 text-yellow-400" />
                                                    <span className="text-xs font-medium">{product.rating}</span>
                                                </div>
                                            </div>

                                            {/* Contenido */}
                                            <div className="p-4">
                                                <div className="mb-2">
                                                    <Badge variant="outline" className="text-xs mb-1">
                                                        {product.categoria_nombre}
                                                    </Badge>
                                                    <h3 className="font-semibold text-gray-900 line-clamp-1">
                                                        {product.nombre}
                                                    </h3>
                                                    <p className="text-xs text-gray-500 line-clamp-2 mt-1">
                                                        {product.description}
                                                    </p>
                                                </div>

                                                {/* Info adicional */}
                                                <div className="flex items-center gap-3 text-xs text-gray-500 mb-3">
                                                    <span className="flex items-center gap-1">
                                                        <Clock className="w-3 h-3" />
                                                        {product.tiempo}
                                                    </span>
                                                    <span>•</span>
                                                    <span>{product.calorias}</span>
                                                </div>

                                                {/* Precio y botón */}
                                                <div className="flex items-center justify-between">
                                                    <div>
                                                        <span className="text-2xl font-bold" style={{ color: COLORS.primary }}>
                                                            ${product.precio_venta}
                                                        </span>
                                                        <span className="text-xs text-gray-500 ml-1">/porción</span>
                                                    </div>
                                                    <motion.button
                                                        whileTap={{ scale: 0.9 }}
                                                        onClick={() => addToCart(product)}
                                                        className="p-2 rounded-lg transition-colors hover:bg-red-50"
                                                        style={{ backgroundColor: COLORS.primary + '15' }}
                                                    >
                                                        <Plus className="w-5 h-5" style={{ color: COLORS.primary }} />
                                                    </motion.button>
                                                </div>
                                            </div>
                                        </motion.div>
                                    ))}
                                </motion.div>
                            ) : (
                                <motion.div
                                    key="list"
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    exit={{ opacity: 0 }}
                                    className="space-y-2"
                                >
                                    {filteredProducts.map((product) => (
                                        <motion.div
                                            key={product.id}
                                            whileHover={{ x: 5 }}
                                            className="bg-white rounded-lg shadow-sm hover:shadow-md transition-all p-4 flex items-center gap-4"
                                        >
                                            <img 
                                                src={product.image} 
                                                alt={product.nombre}
                                                className="w-20 h-20 rounded-lg object-cover"
                                            />
                                            <div className="flex-1">
                                                <div className="flex items-start justify-between">
                                                    <div>
                                                        <Badge variant="outline" className="text-xs mb-1">
                                                            {product.categoria_nombre}
                                                        </Badge>
                                                        <h3 className="font-semibold text-gray-900">
                                                            {product.nombre}
                                                        </h3>
                                                        <p className="text-sm text-gray-500 mt-1">
                                                            {product.description}
                                                        </p>
                                                    </div>
                                                    <div className="text-right">
                                                        <span className="text-xl font-bold" style={{ color: COLORS.primary }}>
                                                            ${product.precio_venta}
                                                        </span>
                                                        <div className="mt-1">
                                                            <Button
                                                                size="sm"
                                                                onClick={() => addToCart(product)}
                                                                style={{ backgroundColor: COLORS.primary }}
                                                            >
                                                                <Plus className="w-4 h-4" />
                                                            </Button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </motion.div>
                                    ))}
                                </motion.div>
                            )}
                        </AnimatePresence>

                        {filteredProducts.length === 0 && (
                            <div className="flex flex-col items-center justify-center h-64 text-gray-500">
                                <Package className="w-12 h-12 mb-2" />
                                <p className="text-lg font-medium">No se encontraron productos</p>
                                <p className="text-sm">Intenta con otra búsqueda o categoría</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Panel del Carrito - Derecha */}
                <AnimatePresence>
                    {showCart && (
                        <motion.div
                            initial={{ x: 400 }}
                            animate={{ x: 0 }}
                            exit={{ x: 400 }}
                            className="w-96 bg-white border-l border-gray-200 flex flex-col hidden lg:flex"
                        >
                            {/* Header del Carrito */}
                            <div className="p-6 border-b">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <h2 className="text-lg font-bold text-gray-900">Orden Actual</h2>
                                        <p className="text-sm text-gray-500">
                                            {cart.reduce((sum, item) => sum + item.quantity, 0)} items
                                        </p>
                                    </div>
                                    <Badge variant="outline" className="gap-1">
                                        <Clock className="w-3 h-3" />
                                        Mesa 5
                                    </Badge>
                                </div>
                            </div>

                            {/* Items del Carrito */}
                            <div className="flex-1 overflow-y-auto p-4">
                                {cart.length === 0 ? (
                                    <div className="flex flex-col items-center justify-center h-64 text-gray-400">
                                        <ShoppingCart className="w-12 h-12 mb-2" />
                                        <p className="text-sm">El carrito está vacío</p>
                                        <p className="text-xs mt-1">Agrega productos para comenzar</p>
                                    </div>
                                ) : (
                                    <div className="space-y-3">
                                        <AnimatePresence>
                                            {cart.map((item) => (
                                                <motion.div
                                                    key={item.id}
                                                    initial={{ opacity: 0, x: 50 }}
                                                    animate={{ opacity: 1, x: 0 }}
                                                    exit={{ opacity: 0, x: 50 }}
                                                    className="bg-gray-50 rounded-lg p-3"
                                                >
                                                    <div className="flex items-start gap-3">
                                                        <img 
                                                            src={item.image} 
                                                            alt={item.nombre}
                                                            className="w-12 h-12 rounded-lg object-cover"
                                                        />
                                                        <div className="flex-1">
                                                            <h4 className="text-sm font-medium text-gray-900">
                                                                {item.nombre}
                                                            </h4>
                                                            <p className="text-xs text-gray-500">
                                                                ${item.precio_venta} c/u
                                                            </p>
                                                        </div>
                                                        <button
                                                            onClick={() => removeFromCart(item.id)}
                                                            className="text-gray-400 hover:text-red-500 transition-colors"
                                                        >
                                                            <X className="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                    
                                                    <div className="flex items-center justify-between mt-2">
                                                        <div className="flex items-center gap-2">
                                                            <button
                                                                onClick={() => updateQuantity(item.id, -1)}
                                                                className="w-6 h-6 rounded-full bg-white border hover:bg-gray-100 flex items-center justify-center transition-colors"
                                                            >
                                                                <Minus className="w-3 h-3" />
                                                            </button>
                                                            <span className="text-sm font-medium w-8 text-center">
                                                                {item.quantity}
                                                            </span>
                                                            <button
                                                                onClick={() => updateQuantity(item.id, 1)}
                                                                className="w-6 h-6 rounded-full bg-white border hover:bg-gray-100 flex items-center justify-center transition-colors"
                                                            >
                                                                <Plus className="w-3 h-3" />
                                                            </button>
                                                        </div>
                                                        <span className="text-sm font-bold" style={{ color: COLORS.primary }}>
                                                            ${(item.precio_venta * item.quantity).toFixed(2)}
                                                        </span>
                                                    </div>
                                                </motion.div>
                                            ))}
                                        </AnimatePresence>
                                    </div>
                                )}
                            </div>

                            {/* Footer del Carrito - Totales y Acciones */}
                            {cart.length > 0 && (
                                <div className="border-t p-4 space-y-4">
                                    {/* Totales */}
                                    <div className="space-y-2 text-sm">
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Subtotal</span>
                                            <span className="font-medium">${getSubtotal().toFixed(2)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">IVA (16%)</span>
                                            <span className="font-medium">${getTax().toFixed(2)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Descuento</span>
                                            <span className="font-medium text-green-600">-$0.00</span>
                                        </div>
                                        <Separator />
                                        <div className="flex justify-between text-lg font-bold">
                                            <span>Total</span>
                                            <span style={{ color: COLORS.primary }}>
                                                ${getTotal().toFixed(2)}
                                            </span>
                                        </div>
                                    </div>

                                    {/* Botones de Acción */}
                                    <div className="space-y-2">
                                        <Button 
                                            className="w-full"
                                            size="lg"
                                            style={{ backgroundColor: COLORS.primary }}
                                        >
                                            Procesar Pago
                                        </Button>
                                        <Button 
                                            variant="outline" 
                                            className="w-full"
                                            onClick={() => setCart([])}
                                        >
                                            Limpiar Carrito
                                        </Button>
                                    </div>
                                </div>
                            )}
                        </motion.div>
                    )}
                </AnimatePresence>
            </div>

        </>
    );
}
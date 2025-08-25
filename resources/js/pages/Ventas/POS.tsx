import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { ShoppingCart, Plus, Minus, Trash2, CreditCard, DollarSign } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Punto de Venta', href: '/ventas' },
];

interface Producto {
    id: number;
    codigo: string;
    nombre: string;
    precio_venta: number;
    stock: number;
    categoria_nombre: string;
    categoria_tipo: string;
}

interface CartItem extends Producto {
    cantidad: number;
}

interface POSProps {
    productos: Producto[];
    categorias: any[];
    mesas: any[];
    clientes: any[];
}

export default function POS({ productos, categorias, mesas, clientes }: POSProps) {
    const [cart, setCart] = useState<CartItem[]>([]);
    const [selectedCategory, setSelectedCategory] = useState<string>('all');
    
    const { post } = useForm();

    const addToCart = (producto: Producto) => {
        const existingItem = cart.find(item => item.id === producto.id);
        
        if (existingItem) {
            if (existingItem.cantidad < producto.stock) {
                setCart(cart.map(item =>
                    item.id === producto.id
                        ? { ...item, cantidad: item.cantidad + 1 }
                        : item
                ));
            }
        } else {
            setCart([...cart, { ...producto, cantidad: 1 }]);
        }
    };

    const updateQuantity = (productId: number, newQuantity: number) => {
        if (newQuantity === 0) {
            setCart(cart.filter(item => item.id !== productId));
        } else {
            setCart(cart.map(item =>
                item.id === productId
                    ? { ...item, cantidad: newQuantity }
                    : item
            ));
        }
    };

    const removeFromCart = (productId: number) => {
        setCart(cart.filter(item => item.id !== productId));
    };

    const getTotal = () => {
        return cart.reduce((sum, item) => sum + (item.precio_venta * item.cantidad), 0);
    };

    const handleCheckout = (tipoPago: string) => {
        if (cart.length === 0) return;

        const items = cart.map(item => ({
            producto_id: item.id,
            cantidad: item.cantidad,
            precio: item.precio_venta
        }));

        post('/ventas', {
            data: {
                items: items,
                tipo_pago: tipoPago,
                subtotal: getTotal(),
                total: getTotal(),
            },
            onSuccess: () => {
                setCart([]);
            }
        });
    };

    const filteredProducts = selectedCategory === 'all'
        ? productos
        : productos.filter(p => p.categoria_nombre === selectedCategory);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Punto de Venta" />
            <div className="flex h-full gap-4 p-4">
                {/* Productos */}
                <div className="flex-1">
                    <div className="mb-4">
                        <div className="flex gap-2 flex-wrap">
                            <Button
                                variant={selectedCategory === 'all' ? 'default' : 'outline'}
                                size="sm"
                                onClick={() => setSelectedCategory('all')}
                            >
                                Todos
                            </Button>
                            {categorias.map(cat => (
                                <Button
                                    key={cat.id}
                                    variant={selectedCategory === cat.nombre ? 'default' : 'outline'}
                                    size="sm"
                                    onClick={() => setSelectedCategory(cat.nombre)}
                                    style={{
                                        borderColor: selectedCategory === cat.nombre ? cat.color : undefined,
                                        backgroundColor: selectedCategory === cat.nombre ? cat.color : undefined,
                                    }}
                                >
                                    {cat.nombre}
                                </Button>
                            ))}
                        </div>
                    </div>

                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        {filteredProducts.map(producto => (
                            <Card
                                key={producto.id}
                                className="cursor-pointer hover:shadow-lg transition-shadow"
                                onClick={() => addToCart(producto)}
                            >
                                <CardHeader className="p-3">
                                    <CardTitle className="text-sm">{producto.nombre}</CardTitle>
                                    <p className="text-xs text-muted-foreground">
                                        {producto.categoria_nombre}
                                    </p>
                                </CardHeader>
                                <CardContent className="p-3 pt-0">
                                    <div className="flex justify-between items-center">
                                        <span className="font-bold">${producto.precio_venta}</span>
                                        <Badge variant={producto.stock > 10 ? 'default' : 'secondary'}>
                                            Stock: {producto.stock}
                                        </Badge>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                </div>

                {/* Carrito */}
                <Card className="w-96">
                    <CardHeader className="pb-3">
                        <CardTitle className="flex items-center gap-2">
                            <ShoppingCart className="h-5 w-5" />
                            Orden Actual
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-2 max-h-96 overflow-y-auto">
                            {cart.length === 0 ? (
                                <p className="text-center text-muted-foreground py-8">
                                    Carrito vacío
                                </p>
                            ) : (
                                cart.map(item => (
                                    <div key={item.id} className="flex items-center gap-2 p-2 border rounded">
                                        <div className="flex-1">
                                            <p className="font-medium text-sm">{item.nombre}</p>
                                            <p className="text-xs text-muted-foreground">
                                                ${item.precio_venta} c/u
                                            </p>
                                        </div>
                                        <div className="flex items-center gap-1">
                                            <Button
                                                size="icon"
                                                variant="outline"
                                                className="h-6 w-6"
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    updateQuantity(item.id, item.cantidad - 1);
                                                }}
                                            >
                                                <Minus className="h-3 w-3" />
                                            </Button>
                                            <span className="w-8 text-center">{item.cantidad}</span>
                                            <Button
                                                size="icon"
                                                variant="outline"
                                                className="h-6 w-6"
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    if (item.cantidad < item.stock) {
                                                        updateQuantity(item.id, item.cantidad + 1);
                                                    }
                                                }}
                                            >
                                                <Plus className="h-3 w-3" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                className="h-6 w-6 text-red-600"
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    removeFromCart(item.id);
                                                }}
                                            >
                                                <Trash2 className="h-3 w-3" />
                                            </Button>
                                        </div>
                                        <div className="text-right">
                                            <p className="font-bold">
                                                ${(item.precio_venta * item.cantidad).toFixed(2)}
                                            </p>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>

                        {cart.length > 0 && (
                            <>
                                <div className="border-t mt-4 pt-4">
                                    <div className="flex justify-between text-xl font-bold">
                                        <span>Total:</span>
                                        <span>${getTotal().toFixed(2)}</span>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-2 mt-4">
                                    <Button
                                        className="gap-2"
                                        onClick={() => handleCheckout('efectivo')}
                                    >
                                        <DollarSign className="h-4 w-4" />
                                        Efectivo
                                    </Button>
                                    <Button
                                        variant="outline"
                                        className="gap-2"
                                        onClick={() => handleCheckout('tarjeta')}
                                    >
                                        <CreditCard className="h-4 w-4" />
                                        Tarjeta
                                    </Button>
                                </div>
                            </>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
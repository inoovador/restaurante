import { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { Plus, Edit, Trash2, Package, AlertTriangle } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Productos', href: '/productos' },
];

interface Categoria {
    id: number;
    nombre: string;
    color: string;
}

interface Producto {
    id: number;
    codigo: string;
    nombre: string;
    descripcion: string | null;
    categoria_id: number;
    categoria_nombre: string;
    categoria_color: string;
    precio_venta: number;
    precio_compra: number;
    stock: number;
    stock_minimo: number;
    activo: boolean;
}

interface ProductosProps {
    productos: Producto[];
    categorias: Categoria[];
}

export default function ProductosIndex({ productos, categorias }: ProductosProps) {
    const [isOpen, setIsOpen] = useState(false);
    const [editingProduct, setEditingProduct] = useState<Producto | null>(null);

    const { data, setData, post, put, reset, errors } = useForm({
        codigo: '',
        nombre: '',
        descripcion: '',
        categoria_id: '',
        precio_venta: '',
        precio_compra: '',
        stock: '',
        stock_minimo: '10',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (editingProduct) {
            put(`/productos/${editingProduct.id}`, {
                onSuccess: () => {
                    setIsOpen(false);
                    reset();
                    setEditingProduct(null);
                },
            });
        } else {
            post('/productos', {
                onSuccess: () => {
                    setIsOpen(false);
                    reset();
                },
            });
        }
    };

    const handleEdit = (producto: Producto) => {
        setEditingProduct(producto);
        setData({
            codigo: producto.codigo,
            nombre: producto.nombre,
            descripcion: producto.descripcion || '',
            categoria_id: producto.categoria_id.toString(),
            precio_venta: producto.precio_venta.toString(),
            precio_compra: producto.precio_compra.toString(),
            stock: producto.stock.toString(),
            stock_minimo: producto.stock_minimo.toString(),
        });
        setIsOpen(true);
    };

    const handleDelete = (id: number) => {
        if (confirm('¿Estás seguro de eliminar este producto?')) {
            useForm().delete(`/productos/${id}`);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Productos" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Productos</h1>
                        <p className="text-muted-foreground">Gestiona el inventario del restaurante</p>
                    </div>
                    <Button onClick={() => setIsOpen(true)} className="gap-2">
                        <Plus className="h-4 w-4" />
                        Nuevo Producto
                    </Button>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {productos.map((producto) => (
                        <Card key={producto.id} className="relative">
                            {producto.stock <= producto.stock_minimo && (
                                <div className="absolute -top-2 -right-2 z-10">
                                    <div className="bg-yellow-500 text-white rounded-full p-1">
                                        <AlertTriangle className="h-4 w-4" />
                                    </div>
                                </div>
                            )}
                            <CardHeader className="pb-3">
                                <div className="flex items-start justify-between">
                                    <div className="flex items-center gap-2">
                                        <Package className="h-5 w-5 text-muted-foreground" />
                                        <div>
                                            <CardTitle className="text-base">{producto.nombre}</CardTitle>
                                            <p className="text-xs text-muted-foreground">{producto.codigo}</p>
                                        </div>
                                    </div>
                                    <div 
                                        className="w-3 h-3 rounded-full" 
                                        style={{ backgroundColor: producto.categoria_color }}
                                    />
                                </div>
                                <CardDescription className="text-xs">
                                    {producto.categoria_nombre}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Precio:</span>
                                        <span className="font-bold text-lg">${producto.precio_venta}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Stock:</span>
                                        <span className={`font-medium ${
                                            producto.stock <= producto.stock_minimo 
                                                ? 'text-yellow-600' 
                                                : 'text-green-600'
                                        }`}>
                                            {producto.stock} unidades
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Costo:</span>
                                        <span>${producto.precio_compra}</span>
                                    </div>
                                </div>
                                <div className="flex gap-2 mt-4">
                                    <Button 
                                        variant="outline" 
                                        size="sm" 
                                        className="flex-1"
                                        onClick={() => handleEdit(producto)}
                                    >
                                        <Edit className="h-3 w-3" />
                                    </Button>
                                    <Button 
                                        variant="outline" 
                                        size="sm" 
                                        className="flex-1 text-red-600 hover:text-red-700"
                                        onClick={() => handleDelete(producto.id)}
                                    >
                                        <Trash2 className="h-3 w-3" />
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogContent className="sm:max-w-[500px]">
                        <form onSubmit={handleSubmit}>
                            <DialogHeader>
                                <DialogTitle>
                                    {editingProduct ? 'Editar Producto' : 'Nuevo Producto'}
                                </DialogTitle>
                                <DialogDescription>
                                    {editingProduct 
                                        ? 'Modifica los datos del producto' 
                                        : 'Ingresa los datos del nuevo producto'}
                                </DialogDescription>
                            </DialogHeader>
                            
                            <div className="grid gap-4 py-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="codigo">Código</Label>
                                        <Input
                                            id="codigo"
                                            value={data.codigo}
                                            onChange={e => setData('codigo', e.target.value)}
                                            disabled={!!editingProduct}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <Label htmlFor="nombre">Nombre</Label>
                                        <Input
                                            id="nombre"
                                            value={data.nombre}
                                            onChange={e => setData('nombre', e.target.value)}
                                            required
                                        />
                                    </div>
                                </div>

                                <div>
                                    <Label htmlFor="descripcion">Descripción</Label>
                                    <Input
                                        id="descripcion"
                                        value={data.descripcion}
                                        onChange={e => setData('descripcion', e.target.value)}
                                    />
                                </div>

                                <div>
                                    <Label htmlFor="categoria">Categoría</Label>
                                    <Select 
                                        value={data.categoria_id} 
                                        onValueChange={value => setData('categoria_id', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecciona una categoría" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {categorias.map(cat => (
                                                <SelectItem key={cat.id} value={cat.id.toString()}>
                                                    {cat.nombre}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="precio_venta">Precio Venta</Label>
                                        <Input
                                            id="precio_venta"
                                            type="number"
                                            step="0.01"
                                            value={data.precio_venta}
                                            onChange={e => setData('precio_venta', e.target.value)}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <Label htmlFor="precio_compra">Precio Compra</Label>
                                        <Input
                                            id="precio_compra"
                                            type="number"
                                            step="0.01"
                                            value={data.precio_compra}
                                            onChange={e => setData('precio_compra', e.target.value)}
                                            required
                                        />
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="stock">Stock Actual</Label>
                                        <Input
                                            id="stock"
                                            type="number"
                                            value={data.stock}
                                            onChange={e => setData('stock', e.target.value)}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <Label htmlFor="stock_minimo">Stock Mínimo</Label>
                                        <Input
                                            id="stock_minimo"
                                            type="number"
                                            value={data.stock_minimo}
                                            onChange={e => setData('stock_minimo', e.target.value)}
                                            required
                                        />
                                    </div>
                                </div>
                            </div>

                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setIsOpen(false)}>
                                    Cancelar
                                </Button>
                                <Button type="submit">
                                    {editingProduct ? 'Actualizar' : 'Guardar'}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface DashboardProps {
    stats: {
        categorias: number;
        productos: number;
        mesas: number;
        clientes: number;
        ventas_hoy: number;
        mesas_disponibles: number;
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
}

export default function Dashboard({ stats, categorias, productos_recientes, mesas }: DashboardProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard - Restaurante" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Estadísticas principales */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Card style={{ borderTop: `4px solid #E32636` }}>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Categorías</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.categorias}</div>
                            <p className="text-xs text-muted-foreground">Categorías activas</p>
                        </CardContent>
                    </Card>
                    
                    <Card style={{ borderTop: `4px solid #4d82bc` }}>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Productos</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.productos}</div>
                            <p className="text-xs text-muted-foreground">En inventario</p>
                        </CardContent>
                    </Card>
                    
                    <Card style={{ borderTop: `4px solid #E32636` }}>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Mesas Disponibles</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.mesas_disponibles}/{stats.mesas}</div>
                            <p className="text-xs text-muted-foreground">Disponibles de {stats.mesas} totales</p>
                        </CardContent>
                    </Card>
                    
                    <Card style={{ borderTop: `4px solid #4d82bc` }}>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Clientes</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.clientes}</div>
                            <p className="text-xs text-muted-foreground">Registrados</p>
                        </CardContent>
                    </Card>
                    
                    <Card style={{ borderTop: `4px solid #E32636` }}>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Ventas Hoy</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">${stats.ventas_hoy.toFixed(2)}</div>
                            <p className="text-xs text-muted-foreground">Total del día</p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    {/* Categorías */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Categorías del Menú</CardTitle>
                            <CardDescription>Todas las categorías activas</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                {categorias.map((categoria) => (
                                    <div key={categoria.id} className="flex items-center justify-between p-2 rounded-lg border">
                                        <div className="flex items-center gap-3">
                                            <div 
                                                className="w-4 h-4 rounded" 
                                                style={{ backgroundColor: categoria.color }}
                                            />
                                            <div>
                                                <p className="font-medium">{categoria.nombre}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    {categoria.tipo} - {categoria.area}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Productos Recientes */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Productos Recientes</CardTitle>
                            <CardDescription>Últimos productos agregados</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                {productos_recientes.map((producto) => (
                                    <div key={producto.id} className="flex items-center justify-between p-2 rounded-lg border">
                                        <div>
                                            <p className="font-medium">{producto.nombre}</p>
                                            <p className="text-xs text-muted-foreground">
                                                {producto.categoria_nombre} - Stock: {producto.stock}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p className="font-bold">${producto.precio_venta}</p>
                                            <p className="text-xs text-muted-foreground">{producto.codigo}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Estado de Mesas */}
                <Card>
                    <CardHeader>
                        <CardTitle>Estado de Mesas</CardTitle>
                        <CardDescription>Vista rápida del estado actual</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            {mesas.map((mesa) => (
                                <div 
                                    key={mesa.numero} 
                                    className={`p-3 rounded-lg border text-center ${
                                        mesa.estado === 'disponible' 
                                            ? 'bg-green-50 border-green-200 dark:bg-green-900/20' 
                                            : mesa.estado === 'ocupada'
                                            ? 'bg-red-50 border-red-200 dark:bg-red-900/20'
                                            : 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20'
                                    }`}
                                >
                                    <p className="font-bold">{mesa.numero}</p>
                                    <p className="text-xs">{mesa.zona}</p>
                                    <p className="text-xs">Cap: {mesa.capacidad}</p>
                                    <p className={`text-xs font-medium ${
                                        mesa.estado === 'disponible' 
                                            ? 'text-green-700 dark:text-green-400' 
                                            : mesa.estado === 'ocupada'
                                            ? 'text-red-700 dark:text-red-400'
                                            : 'text-yellow-700 dark:text-yellow-400'
                                    }`}>
                                        {mesa.estado}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
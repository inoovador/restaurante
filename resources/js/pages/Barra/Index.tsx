import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import AppLayout from "@/layouts/app-layout";
import { type BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/react";
import { Wine, Coffee, Beer, GlassWater, Clock, CheckCircle } from "lucide-react";
import { useState } from "react";

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Barra", href: "/barra" },
];

interface Pedido {
    id: number;
    venta_id: number;
    producto_id: number;
    producto_nombre: string;
    cantidad: number;
    observaciones: string | null;
    estado_cocina: string;
    categoria_tipo: string;
    tipo_pedido: string;
    hora_pedido: string;
    mesa_numero: number | null;
    mesa_zona: string | null;
}

interface Bebida {
    id: number;
    nombre: string;
    precio: number;
    stock: number;
    categoria_nombre: string;
    tipo: string;
    activo: boolean;
}

export default function BarraIndex({ 
    pedidosPendientes, 
    bebidas 
}: { 
    pedidosPendientes: Pedido[], 
    bebidas: Bebida[] 
}) {
    const [pedidosSeleccionados, setPedidosSeleccionados] = useState<number[]>([]);

    const getIconByTipo = (tipo: string) => {
        switch(tipo) {
            case 'bebida':
                return <GlassWater className="h-4 w-4" />;
            case 'cafe':
                return <Coffee className="h-4 w-4" />;
            case 'cerveza':
                return <Beer className="h-4 w-4" />;
            case 'vino':
                return <Wine className="h-4 w-4" />;
            default:
                return <GlassWater className="h-4 w-4" />;
        }
    };

    const bebidasPorCategoria = bebidas.reduce((acc, bebida) => {
        if (!acc[bebida.categoria_nombre]) {
            acc[bebida.categoria_nombre] = [];
        }
        acc[bebida.categoria_nombre].push(bebida);
        return acc;
    }, {} as Record<string, Bebida[]>);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Barra" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Barra de Bebidas</h1>
                        <p className="text-muted-foreground">Control de pedidos de bebidas</p>
                    </div>
                    <Badge variant="secondary" className="text-lg px-4 py-2">
                        <Clock className="h-4 w-4 mr-2" />
                        {pedidosPendientes.length} Pedidos Pendientes
                    </Badge>
                </div>

                <div className="grid gap-4 md:grid-cols-3">
                    {/* Pedidos Pendientes */}
                    <Card className="md:col-span-2">
                        <CardHeader className="bg-blue-50 dark:bg-blue-950/20">
                            <CardTitle className="flex items-center gap-2">
                                <Wine className="h-5 w-5 text-blue-600" />
                                Pedidos de Bebidas
                            </CardTitle>
                            <CardDescription>Bebidas pendientes de preparación</CardDescription>
                        </CardHeader>
                        <CardContent className="p-4">
                            <div className="space-y-3">
                                {pedidosPendientes.length === 0 ? (
                                    <div className="text-center py-8 text-muted-foreground">
                                        <GlassWater className="h-12 w-12 mx-auto mb-2 opacity-50" />
                                        <p>No hay pedidos de bebidas pendientes</p>
                                    </div>
                                ) : (
                                    pedidosPendientes.map((pedido) => (
                                        <div 
                                            key={pedido.id} 
                                            className={`border rounded-lg p-3 transition-colors ${
                                                pedidosSeleccionados.includes(pedido.id) 
                                                    ? 'bg-blue-50 dark:bg-blue-950/20 border-blue-500' 
                                                    : 'hover:bg-gray-50 dark:hover:bg-gray-900'
                                            }`}
                                        >
                                            <div className="flex items-start justify-between">
                                                <div className="flex-1">
                                                    <div className="flex items-center gap-2 mb-1">
                                                        {getIconByTipo(pedido.categoria_tipo)}
                                                        <span className="font-bold text-lg">
                                                            {pedido.cantidad}x {pedido.producto_nombre}
                                                        </span>
                                                    </div>
                                                    <div className="flex items-center gap-3 text-sm">
                                                        {pedido.mesa_numero ? (
                                                            <Badge variant="outline">
                                                                Mesa {pedido.mesa_numero} - {pedido.mesa_zona}
                                                            </Badge>
                                                        ) : (
                                                            <Badge variant="outline">
                                                                {pedido.tipo_pedido}
                                                            </Badge>
                                                        )}
                                                        <span className="text-muted-foreground">
                                                            Pedido #{pedido.venta_id}
                                                        </span>
                                                    </div>
                                                    {pedido.observaciones && (
                                                        <p className="text-sm mt-2 p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded">
                                                            Nota: {pedido.observaciones}
                                                        </p>
                                                    )}
                                                </div>
                                                <Button 
                                                    size="sm" 
                                                    variant={pedidosSeleccionados.includes(pedido.id) ? "default" : "outline"}
                                                    onClick={() => {
                                                        setPedidosSeleccionados(prev =>
                                                            prev.includes(pedido.id)
                                                                ? prev.filter(id => id !== pedido.id)
                                                                : [...prev, pedido.id]
                                                        );
                                                    }}
                                                >
                                                    <CheckCircle className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                    ))
                                )}
                            </div>
                            {pedidosSeleccionados.length > 0 && (
                                <div className="mt-4 p-3 bg-green-50 dark:bg-green-950/20 rounded-lg flex items-center justify-between">
                                    <span className="font-medium">
                                        {pedidosSeleccionados.length} bebidas seleccionadas
                                    </span>
                                    <Button variant="success">
                                        Marcar como Listas
                                    </Button>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Inventario Rápido */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Inventario Rápido</CardTitle>
                            <CardDescription>Stock de bebidas disponibles</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4 max-h-[600px] overflow-y-auto">
                                {Object.entries(bebidasPorCategoria).map(([categoria, items]) => (
                                    <div key={categoria}>
                                        <h4 className="font-semibold text-sm mb-2">{categoria}</h4>
                                        <div className="space-y-1">
                                            {items.slice(0, 5).map((bebida) => (
                                                <div key={bebida.id} className="flex items-center justify-between p-2 text-sm border rounded">
                                                    <div className="flex items-center gap-2">
                                                        {getIconByTipo(bebida.tipo)}
                                                        <span>{bebida.nombre}</span>
                                                    </div>
                                                    <Badge variant={bebida.stock > 10 ? "default" : bebida.stock > 0 ? "secondary" : "destructive"}>
                                                        {bebida.stock}
                                                    </Badge>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Estadísticas */}
                <div className="grid gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Bebidas Servidas</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">0</p>
                            <p className="text-xs text-muted-foreground">Hoy</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Pendientes</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold text-yellow-600">{pedidosPendientes.length}</p>
                            <p className="text-xs text-muted-foreground">En cola</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Bebida Más Pedida</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-lg font-bold">Coca Cola</p>
                            <p className="text-xs text-muted-foreground">15 unidades</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Stock Bajo</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold text-red-600">
                                {bebidas.filter(b => b.stock <= 5).length}
                            </p>
                            <p className="text-xs text-muted-foreground">Productos</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
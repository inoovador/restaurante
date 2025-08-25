import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import AppLayout from "@/layouts/app-layout";
import { type BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/react";
import { ChefHat, Clock, AlertCircle, CheckCircle, Timer, Utensils } from "lucide-react";
import { useState, useEffect } from "react";

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Cocina", href: "/cocina" },
];

interface Pedido {
    id: number;
    venta_id: number;
    producto_id: number;
    producto_nombre: string;
    cantidad: number;
    observaciones: string | null;
    estado_cocina: string;
    tiempo_preparacion: number | null;
    tipo_pedido: string;
    hora_pedido: string;
    mesa_numero: number | null;
    mesa_zona: string | null;
}

export default function CocinaIndex({ 
    pedidosPendientes, 
    pedidosEnPreparacion 
}: { 
    pedidosPendientes: Pedido[], 
    pedidosEnPreparacion: Pedido[] 
}) {
    const [tiempoActual, setTiempoActual] = useState(new Date());

    useEffect(() => {
        const interval = setInterval(() => {
            setTiempoActual(new Date());
        }, 60000); // Actualizar cada minuto
        return () => clearInterval(interval);
    }, []);

    const calcularTiempoEspera = (horaPedido: string) => {
        const ahora = tiempoActual.getTime();
        const pedido = new Date(horaPedido).getTime();
        const minutos = Math.floor((ahora - pedido) / 60000);
        
        if (minutos < 5) return { texto: `${minutos} min`, color: "text-green-600" };
        if (minutos < 10) return { texto: `${minutos} min`, color: "text-yellow-600" };
        return { texto: `${minutos} min`, color: "text-red-600" };
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Cocina" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Cocina</h1>
                        <p className="text-muted-foreground">Control de pedidos y preparación</p>
                    </div>
                    <div className="flex gap-4">
                        <Badge variant="secondary" className="text-lg px-4 py-2">
                            <AlertCircle className="h-4 w-4 mr-2" />
                            {pedidosPendientes.length} Pendientes
                        </Badge>
                        <Badge variant="default" className="text-lg px-4 py-2">
                            <Timer className="h-4 w-4 mr-2" />
                            {pedidosEnPreparacion.length} En Preparación
                        </Badge>
                    </div>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    {/* Pedidos Pendientes */}
                    <Card>
                        <CardHeader className="bg-red-50 dark:bg-red-950/20">
                            <CardTitle className="flex items-center gap-2">
                                <AlertCircle className="h-5 w-5 text-red-600" />
                                Pedidos Pendientes
                            </CardTitle>
                            <CardDescription>Pedidos esperando preparación</CardDescription>
                        </CardHeader>
                        <CardContent className="p-4">
                            <div className="space-y-3">
                                {pedidosPendientes.length === 0 ? (
                                    <div className="text-center py-8 text-muted-foreground">
                                        <ChefHat className="h-12 w-12 mx-auto mb-2 opacity-50" />
                                        <p>No hay pedidos pendientes</p>
                                    </div>
                                ) : (
                                    pedidosPendientes.map((pedido) => {
                                        const tiempo = calcularTiempoEspera(pedido.hora_pedido);
                                        return (
                                            <div key={pedido.id} className="border rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-900">
                                                <div className="flex items-start justify-between">
                                                    <div className="flex-1">
                                                        <div className="flex items-center gap-2 mb-1">
                                                            <span className="font-bold text-lg">
                                                                {pedido.cantidad}x {pedido.producto_nombre}
                                                            </span>
                                                        </div>
                                                        <div className="flex items-center gap-3 text-sm text-muted-foreground">
                                                            {pedido.mesa_numero ? (
                                                                <Badge variant="outline">
                                                                    Mesa {pedido.mesa_numero}
                                                                </Badge>
                                                            ) : (
                                                                <Badge variant="outline">
                                                                    {pedido.tipo_pedido}
                                                                </Badge>
                                                            )}
                                                            <div className={`flex items-center gap-1 ${tiempo.color}`}>
                                                                <Clock className="h-3 w-3" />
                                                                {tiempo.texto}
                                                            </div>
                                                        </div>
                                                        {pedido.observaciones && (
                                                            <p className="text-sm mt-2 p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded">
                                                                Nota: {pedido.observaciones}
                                                            </p>
                                                        )}
                                                    </div>
                                                    <Button size="sm" className="ml-2">
                                                        Preparar
                                                    </Button>
                                                </div>
                                            </div>
                                        );
                                    })
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Pedidos en Preparación */}
                    <Card>
                        <CardHeader className="bg-yellow-50 dark:bg-yellow-950/20">
                            <CardTitle className="flex items-center gap-2">
                                <Timer className="h-5 w-5 text-yellow-600" />
                                En Preparación
                            </CardTitle>
                            <CardDescription>Pedidos siendo preparados</CardDescription>
                        </CardHeader>
                        <CardContent className="p-4">
                            <div className="space-y-3">
                                {pedidosEnPreparacion.length === 0 ? (
                                    <div className="text-center py-8 text-muted-foreground">
                                        <Utensils className="h-12 w-12 mx-auto mb-2 opacity-50" />
                                        <p>No hay pedidos en preparación</p>
                                    </div>
                                ) : (
                                    pedidosEnPreparacion.map((pedido) => {
                                        const tiempo = calcularTiempoEspera(pedido.hora_pedido);
                                        return (
                                            <div key={pedido.id} className="border rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-900">
                                                <div className="flex items-start justify-between">
                                                    <div className="flex-1">
                                                        <div className="flex items-center gap-2 mb-1">
                                                            <span className="font-bold text-lg">
                                                                {pedido.cantidad}x {pedido.producto_nombre}
                                                            </span>
                                                        </div>
                                                        <div className="flex items-center gap-3 text-sm text-muted-foreground">
                                                            {pedido.mesa_numero ? (
                                                                <Badge variant="outline">
                                                                    Mesa {pedido.mesa_numero}
                                                                </Badge>
                                                            ) : (
                                                                <Badge variant="outline">
                                                                    {pedido.tipo_pedido}
                                                                </Badge>
                                                            )}
                                                            <div className={`flex items-center gap-1 ${tiempo.color}`}>
                                                                <Clock className="h-3 w-3" />
                                                                {tiempo.texto}
                                                            </div>
                                                            {pedido.tiempo_preparacion && (
                                                                <div className="flex items-center gap-1">
                                                                    <Timer className="h-3 w-3" />
                                                                    {pedido.tiempo_preparacion} min
                                                                </div>
                                                            )}
                                                        </div>
                                                        {pedido.observaciones && (
                                                            <p className="text-sm mt-2 p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded">
                                                                Nota: {pedido.observaciones}
                                                            </p>
                                                        )}
                                                    </div>
                                                    <Button size="sm" variant="success" className="ml-2">
                                                        <CheckCircle className="h-4 w-4 mr-1" />
                                                        Listo
                                                    </Button>
                                                </div>
                                            </div>
                                        );
                                    })
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Estadísticas del día */}
                <div className="grid gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Pedidos Completados</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">0</p>
                            <p className="text-xs text-muted-foreground">Hoy</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Tiempo Promedio</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">15 min</p>
                            <p className="text-xs text-muted-foreground">Por pedido</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">En Cola</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">{pedidosPendientes.length}</p>
                            <p className="text-xs text-muted-foreground">Esperando</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Activos</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">{pedidosEnPreparacion.length}</p>
                            <p className="text-xs text-muted-foreground">Preparando</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
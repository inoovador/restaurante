import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import AppLayout from "@/layouts/app-layout";
import { type BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/react";
import { ShoppingCart, Plus, Package, Calendar, DollarSign, Truck } from "lucide-react";

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Compras", href: "/compras" },
];

interface Proveedor {
    id: number;
    nombre: string;
    ruc: string;
    telefono: string | null;
}

interface Compra {
    id: number;
    proveedor_id: number | null;
    proveedor_nombre: string | null;
    proveedor_ruc: string | null;
    fecha: string;
    numero_factura: string | null;
    total: number;
    estado: string;
    observaciones: string | null;
}

export default function ComprasIndex({ compras, proveedores }: { compras: Compra[], proveedores: Proveedor[] }) {
    const comprasData = compras || [];
    const totalCompras = comprasData.reduce((sum, compra) => sum + compra.total, 0);
    const comprasPendientes = comprasData.filter(c => c.estado === 'pendiente').length;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Compras" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Gestión de Compras</h1>
                        <p className="text-muted-foreground">Control de compras y proveedores</p>
                    </div>
                    <Button className="gap-2">
                        <Plus className="h-4 w-4" />
                        Nueva Compra
                    </Button>
                </div>

                <div className="grid gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Total Compras</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">${totalCompras.toFixed(2)}</p>
                            <p className="text-xs text-muted-foreground">Este mes</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Órdenes</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">{comprasData.length}</p>
                            <p className="text-xs text-muted-foreground">Total registradas</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Pendientes</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold text-yellow-600">{comprasPendientes}</p>
                            <p className="text-xs text-muted-foreground">Por recibir</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Proveedores</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">{proveedores.length}</p>
                            <p className="text-xs text-muted-foreground">Activos</p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-4 md:grid-cols-3">
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle>Compras Recientes</CardTitle>
                            <CardDescription>Últimas órdenes de compra registradas</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {comprasData.slice(0, 5).map((compra) => (
                                    <div key={compra.id} className="flex items-center justify-between p-3 border rounded-lg">
                                        <div className="flex items-center gap-3">
                                            <div className="bg-primary/10 p-2 rounded">
                                                <ShoppingCart className="h-4 w-4" />
                                            </div>
                                            <div>
                                                <p className="font-medium">
                                                    {compra.numero_factura || `Orden #${compra.id}`}
                                                </p>
                                                <div className="flex items-center gap-2 text-xs text-muted-foreground">
                                                    <Truck className="h-3 w-3" />
                                                    {compra.proveedor_nombre || "Sin proveedor"}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className="font-bold">${compra.total.toFixed(2)}</p>
                                            <Badge variant={
                                                compra.estado === 'completada' ? 'default' :
                                                compra.estado === 'pendiente' ? 'secondary' : 'destructive'
                                            }>
                                                {compra.estado}
                                            </Badge>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Proveedores Frecuentes</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                {proveedores.slice(0, 5).map((proveedor) => (
                                    <div key={proveedor.id} className="flex items-center justify-between p-2 border rounded">
                                        <div>
                                            <p className="font-medium text-sm">{proveedor.nombre}</p>
                                            <p className="text-xs text-muted-foreground">RUC: {proveedor.ruc}</p>
                                        </div>
                                        <Button variant="ghost" size="sm">Ver</Button>
                                    </div>
                                ))}
                            </div>
                            <Button variant="outline" className="w-full mt-4">
                                Ver todos los proveedores
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
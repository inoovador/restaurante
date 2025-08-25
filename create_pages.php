<?php

// Crear todas las páginas que faltan

// USUARIOS
file_put_contents(__DIR__ . '/resources/js/pages/Usuarios/Index.tsx', '
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import AppLayout from "@/layouts/app-layout";
import { type BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/react";
import { Users, UserPlus, Mail } from "lucide-react";

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Usuarios", href: "/usuarios" },
];

interface Usuario {
    id: number;
    name: string;
    email: string;
    created_at: string;
}

export default function UsuariosIndex({ usuarios }: { usuarios: Usuario[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Usuarios" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Usuarios</h1>
                        <p className="text-muted-foreground">Gestión de usuarios del sistema</p>
                    </div>
                    <Button className="gap-2">
                        <UserPlus className="h-4 w-4" />
                        Nuevo Usuario
                    </Button>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {usuarios.map((usuario) => (
                        <Card key={usuario.id}>
                            <CardHeader>
                                <div className="flex items-center gap-3">
                                    <div className="bg-primary/10 p-2 rounded-full">
                                        <Users className="h-5 w-5" />
                                    </div>
                                    <div>
                                        <CardTitle className="text-base">{usuario.name}</CardTitle>
                                        <CardDescription className="text-xs flex items-center gap-1">
                                            <Mail className="h-3 w-3" />
                                            {usuario.email}
                                        </CardDescription>
                                    </div>
                                </div>
                            </CardHeader>
                        </Card>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}');

// CLIENTES
file_put_contents(__DIR__ . '/resources/js/pages/Clientes/Index.tsx', '
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import AppLayout from "@/layouts/app-layout";
import { type BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/react";
import { Users, UserPlus, Phone, Mail } from "lucide-react";

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Clientes", href: "/clientes" },
];

interface Cliente {
    id: number;
    nombre: string;
    apellido: string | null;
    telefono: string | null;
    email: string | null;
    visitas: number;
    total_gastado: number;
}

export default function ClientesIndex({ clientes }: { clientes: Cliente[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Clientes" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Clientes</h1>
                        <p className="text-muted-foreground">Gestión de clientes del restaurante</p>
                    </div>
                    <Button className="gap-2">
                        <UserPlus className="h-4 w-4" />
                        Nuevo Cliente
                    </Button>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {clientes.map((cliente) => (
                        <Card key={cliente.id}>
                            <CardHeader>
                                <CardTitle className="text-lg">
                                    {cliente.nombre} {cliente.apellido}
                                </CardTitle>
                                <CardDescription>
                                    {cliente.telefono && (
                                        <div className="flex items-center gap-1">
                                            <Phone className="h-3 w-3" />
                                            {cliente.telefono}
                                        </div>
                                    )}
                                    {cliente.email && (
                                        <div className="flex items-center gap-1">
                                            <Mail className="h-3 w-3" />
                                            {cliente.email}
                                        </div>
                                    )}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="flex justify-between text-sm">
                                    <span>Visitas: {cliente.visitas}</span>
                                    <span className="font-bold">${cliente.total_gastado}</span>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}');

// CAJA
file_put_contents(__DIR__ . '/resources/js/pages/Caja/Index.tsx', '
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import AppLayout from "@/layouts/app-layout";
import { type BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/react";
import { DollarSign, TrendingUp, TrendingDown, Calculator } from "lucide-react";

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Caja", href: "/caja" },
];

interface Caja {
    id: number;
    estado: string;
    monto_apertura: number;
    ventas_efectivo: number;
    ventas_tarjeta: number;
    gastos: number;
    fecha_apertura: string;
}

export default function CajaIndex({ caja, movimientos }: any) {
    const cajaActual = caja || {
        estado: "cerrada",
        monto_apertura: 0,
        ventas_efectivo: 0,
        ventas_tarjeta: 0,
        gastos: 0
    };

    const total = cajaActual.monto_apertura + cajaActual.ventas_efectivo + cajaActual.ventas_tarjeta - cajaActual.gastos;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Caja" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Control de Caja</h1>
                        <p className="text-muted-foreground">Gestión de movimientos de efectivo</p>
                    </div>
                    <Badge variant={cajaActual.estado === "abierta" ? "default" : "secondary"} className="text-lg px-4 py-2">
                        Caja {cajaActual.estado}
                    </Badge>
                </div>

                <div className="grid gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Apertura</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">${cajaActual.monto_apertura}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Ventas Efectivo</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold text-green-600">${cajaActual.ventas_efectivo}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Ventas Tarjeta</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold text-blue-600">${cajaActual.ventas_tarjeta}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm">Total en Caja</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">${total.toFixed(2)}</p>
                        </CardContent>
                    </Card>
                </div>

                {cajaActual.estado === "cerrada" ? (
                    <Card>
                        <CardContent className="py-8 text-center">
                            <Calculator className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
                            <p className="text-lg mb-4">La caja está cerrada</p>
                            <Button size="lg">Abrir Caja</Button>
                        </CardContent>
                    </Card>
                ) : (
                    <Card>
                        <CardHeader>
                            <CardTitle>Acciones Rápidas</CardTitle>
                        </CardHeader>
                        <CardContent className="flex gap-4">
                            <Button variant="outline">Registrar Gasto</Button>
                            <Button variant="outline">Ver Movimientos</Button>
                            <Button variant="destructive">Cerrar Caja</Button>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}');

// INVENTARIO
file_put_contents(__DIR__ . '/resources/js/pages/Inventario/Index.tsx', '
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import AppLayout from "@/layouts/app-layout";
import { type BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/react";
import { Package, AlertTriangle, TrendingUp, TrendingDown } from "lucide-react";

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Inventario", href: "/inventario" },
];

export default function InventarioIndex({ productos, movimientos }: any) {
    const productosConStock = productos || [];
    const productosBajoStock = productosConStock.filter((p: any) => p.stock <= p.stock_minimo);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Inventario" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Control de Inventario</h1>
                        <p className="text-muted-foreground">Gestión de stock y movimientos</p>
                    </div>
                    {productosBajoStock.length > 0 && (
                        <Badge variant="destructive" className="gap-1">
                            <AlertTriangle className="h-3 w-3" />
                            {productosBajoStock.length} productos con stock bajo
                        </Badge>
                    )}
                </div>

                <div className="grid gap-4 md:grid-cols-3">
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle>Productos con Stock Bajo</CardTitle>
                            <CardDescription>Requieren reabastecimiento</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                {productosBajoStock.slice(0, 5).map((producto: any) => (
                                    <div key={producto.id} className="flex items-center justify-between p-2 border rounded">
                                        <div className="flex items-center gap-2">
                                            <Package className="h-4 w-4 text-muted-foreground" />
                                            <div>
                                                <p className="font-medium">{producto.nombre}</p>
                                                <p className="text-xs text-muted-foreground">{producto.codigo}</p>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className="font-bold text-yellow-600">{producto.stock} unidades</p>
                                            <p className="text-xs">Mínimo: {producto.stock_minimo}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Resumen</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <p className="text-sm text-muted-foreground">Total Productos</p>
                                <p className="text-2xl font-bold">{productosConStock.length}</p>
                            </div>
                            <div>
                                <p className="text-sm text-muted-foreground">Stock Bajo</p>
                                <p className="text-2xl font-bold text-yellow-600">{productosBajoStock.length}</p>
                            </div>
                            <div>
                                <p className="text-sm text-muted-foreground">Sin Stock</p>
                                <p className="text-2xl font-bold text-red-600">
                                    {productosConStock.filter((p: any) => p.stock === 0).length}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}');

echo "Páginas creadas!";
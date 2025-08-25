
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
}
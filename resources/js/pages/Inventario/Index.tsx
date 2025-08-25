
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
}

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
}
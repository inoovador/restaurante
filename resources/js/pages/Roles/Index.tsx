import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Switch } from "@/components/ui/switch";
import AppLayout from "@/layouts/app-layout";
import { type BreadcrumbItem } from "@/types";
import { Head } from "@inertiajs/react";
import { Shield, UserCheck, Settings, Edit } from "lucide-react";

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Roles", href: "/roles" },
];

interface Rol {
    id: number;
    nombre: string;
    descripcion: string | null;
    activo: boolean;
}

interface Permisos {
    [key: string]: string;
}

export default function RolesIndex({ roles, permisos }: { roles: Rol[], permisos: Permisos }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Roles" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Roles y Permisos</h1>
                        <p className="text-muted-foreground">Gestión de roles del sistema</p>
                    </div>
                    <Button className="gap-2">
                        <Shield className="h-4 w-4" />
                        Nuevo Rol
                    </Button>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {roles.map((rol) => (
                        <Card key={rol.id}>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Shield className="h-5 w-5 text-primary" />
                                        <CardTitle className="text-lg">{rol.nombre}</CardTitle>
                                    </div>
                                    <Badge variant={rol.activo ? "default" : "secondary"}>
                                        {rol.activo ? "Activo" : "Inactivo"}
                                    </Badge>
                                </div>
                                {rol.descripcion && (
                                    <CardDescription>{rol.descripcion}</CardDescription>
                                )}
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div className="text-sm font-medium">Permisos:</div>
                                    <div className="space-y-2">
                                        {Object.entries(permisos).slice(0, 3).map(([key, label]) => (
                                            <div key={key} className="flex items-center gap-2">
                                                <UserCheck className="h-3 w-3 text-green-600" />
                                                <span className="text-xs">{label}</span>
                                            </div>
                                        ))}
                                    </div>
                                    <Button variant="outline" size="sm" className="w-full gap-2">
                                        <Edit className="h-3 w-3" />
                                        Editar Permisos
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Permisos Disponibles</CardTitle>
                        <CardDescription>Lista de todos los permisos del sistema</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {Object.entries(permisos).map(([key, label]) => (
                                <div key={key} className="flex items-center justify-between p-2 border rounded">
                                    <div className="flex items-center gap-2">
                                        <Settings className="h-4 w-4 text-muted-foreground" />
                                        <span className="text-sm">{label}</span>
                                    </div>
                                    <Switch />
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
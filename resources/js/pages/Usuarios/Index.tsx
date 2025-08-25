
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
}
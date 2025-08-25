import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Plus, Edit, Trash2 } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Categorías', href: '/categorias' },
];

interface Categoria {
    id: number;
    nombre: string;
    descripcion: string | null;
    tipo: string;
    area: string;
    color: string;
    activo: boolean;
}

interface CategoriasProps {
    categorias: Categoria[];
}

export default function CategoriasIndex({ categorias }: CategoriasProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Categorías" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Categorías</h1>
                        <p className="text-muted-foreground">Gestiona las categorías del menú</p>
                    </div>
                    <Button className="gap-2">
                        <Plus className="h-4 w-4" />
                        Nueva Categoría
                    </Button>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {categorias.map((categoria) => (
                        <Card key={categoria.id} style={{ borderTop: `4px solid ${categoria.color}` }}>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div 
                                            className="h-4 w-4 rounded" 
                                            style={{ backgroundColor: categoria.color }}
                                        />
                                        <CardTitle className="text-lg">{categoria.nombre}</CardTitle>
                                    </div>
                                    <div className="flex gap-1">
                                        <Button variant="ghost" size="icon" className="h-8 w-8">
                                            <Edit className="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon" className="h-8 w-8 text-red-600">
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                                <CardDescription>{categoria.descripcion}</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="flex flex-col gap-2 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Tipo:</span>
                                        <span className="font-medium capitalize">{categoria.tipo}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Área:</span>
                                        <span className="font-medium capitalize">{categoria.area}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Estado:</span>
                                        <span className={`font-medium ${categoria.activo ? 'text-green-600' : 'text-red-600'}`}>
                                            {categoria.activo ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {categorias.length === 0 && (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <p className="text-muted-foreground mb-4">No hay categorías registradas</p>
                            <Button className="gap-2">
                                <Plus className="h-4 w-4" />
                                Crear primera categoría
                            </Button>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}
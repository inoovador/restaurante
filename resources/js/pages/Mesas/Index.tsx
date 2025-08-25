import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { Users, Utensils, Clock, Wrench } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mesas', href: '/mesas' },
];

interface Mesa {
    id: number;
    numero: string;
    capacidad: number;
    estado: 'disponible' | 'ocupada' | 'reservada' | 'mantenimiento';
    zona: string;
    activo: boolean;
}

interface MesasProps {
    mesas: Mesa[];
}

export default function MesasIndex({ mesas }: MesasProps) {
    const { put } = useForm();

    const handleEstadoChange = (mesaId: number, nuevoEstado: string) => {
        put(`/mesas/${mesaId}/estado`, {
            data: { estado: nuevoEstado },
            preserveScroll: true,
        });
    };

    const getEstadoColor = (estado: string) => {
        switch (estado) {
            case 'disponible': return 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/30 dark:text-green-400';
            case 'ocupada': return 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/30 dark:text-red-400';
            case 'reservada': return 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400';
            case 'mantenimiento': return 'bg-gray-100 text-gray-800 border-gray-300 dark:bg-gray-900/30 dark:text-gray-400';
            default: return '';
        }
    };

    const getEstadoIcon = (estado: string) => {
        switch (estado) {
            case 'disponible': return <Utensils className="h-8 w-8" />;
            case 'ocupada': return <Users className="h-8 w-8" />;
            case 'reservada': return <Clock className="h-8 w-8" />;
            case 'mantenimiento': return <Wrench className="h-8 w-8" />;
            default: return null;
        }
    };

    const zonas = [...new Set(mesas.map(m => m.zona))];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Mesas" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Gestión de Mesas</h1>
                        <p className="text-muted-foreground">Control del estado de las mesas del restaurante</p>
                    </div>
                    <div className="flex gap-4">
                        <div className="flex gap-2">
                            <Badge variant="outline" className="bg-green-100 dark:bg-green-900/30">
                                Disponibles: {mesas.filter(m => m.estado === 'disponible').length}
                            </Badge>
                            <Badge variant="outline" className="bg-red-100 dark:bg-red-900/30">
                                Ocupadas: {mesas.filter(m => m.estado === 'ocupada').length}
                            </Badge>
                            <Badge variant="outline" className="bg-yellow-100 dark:bg-yellow-900/30">
                                Reservadas: {mesas.filter(m => m.estado === 'reservada').length}
                            </Badge>
                        </div>
                    </div>
                </div>

                {zonas.map(zona => (
                    <div key={zona}>
                        <h2 className="text-xl font-semibold mb-3 capitalize">
                            {zona.replace('_', ' ')}
                        </h2>
                        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                            {mesas
                                .filter(m => m.zona === zona)
                                .map((mesa) => (
                                    <Card 
                                        key={mesa.id} 
                                        className={`cursor-pointer transition-all hover:shadow-lg ${getEstadoColor(mesa.estado)}`}
                                    >
                                        <CardHeader className="p-3 pb-2">
                                            <div className="flex items-center justify-between">
                                                <CardTitle className="text-lg">
                                                    Mesa {mesa.numero}
                                                </CardTitle>
                                                {getEstadoIcon(mesa.estado)}
                                            </div>
                                        </CardHeader>
                                        <CardContent className="p-3 pt-0">
                                            <div className="text-sm space-y-1">
                                                <p className="font-medium">
                                                    Capacidad: {mesa.capacidad} {mesa.capacidad === 1 ? 'persona' : 'personas'}
                                                </p>
                                                <p className="text-xs capitalize font-bold">
                                                    {mesa.estado}
                                                </p>
                                            </div>
                                            <div className="grid grid-cols-2 gap-1 mt-3">
                                                {mesa.estado !== 'disponible' && (
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        className="text-xs px-1"
                                                        onClick={() => handleEstadoChange(mesa.id, 'disponible')}
                                                    >
                                                        Liberar
                                                    </Button>
                                                )}
                                                {mesa.estado === 'disponible' && (
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        className="text-xs px-1"
                                                        onClick={() => handleEstadoChange(mesa.id, 'ocupada')}
                                                    >
                                                        Ocupar
                                                    </Button>
                                                )}
                                                {mesa.estado === 'disponible' && (
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        className="text-xs px-1"
                                                        onClick={() => handleEstadoChange(mesa.id, 'reservada')}
                                                    >
                                                        Reservar
                                                    </Button>
                                                )}
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))}
                        </div>
                    </div>
                ))}
            </div>
        </AppLayout>
    );
}
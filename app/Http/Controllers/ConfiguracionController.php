<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        // Obtener configuración del restaurante (podría venir de DB o archivo config)
        $configuracion = [
            'restaurante' => [
                'nombre' => 'FoodPoint',
                'ruc' => '20123456789',
                'direccion' => 'Av. Principal 123, Centro',
                'telefono' => '(01) 234-5678',
                'email' => 'info@foodpoint.com',
                'website' => 'www.foodpoint.com',
                'logo' => '/images/logo.jpeg'
            ],
            'impuestos' => [
                'igv' => 18,
                'servicio' => 10
            ],
            'moneda' => [
                'simbolo' => 'S/',
                'codigo' => 'PEN',
                'decimales' => 2
            ],
            'horario' => [
                'apertura' => '09:00',
                'cierre' => '23:00',
                'dias' => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']
            ],
            'notificaciones' => [
                'email' => true,
                'sms' => false,
                'whatsapp' => true
            ],
            'pos' => [
                'modo_offline' => false,
                'imprimir_boleta' => true,
                'sonido_alertas' => true
            ]
        ];

        // Estadísticas del sistema
        $stats = [
            'usuarios_activos' => DB::table('users')->count(),
            'productos_total' => DB::table('productos')->count(),
            'categorias_total' => DB::table('categorias')->count(),
            'ventas_hoy' => DB::table('ventas')->whereDate('created_at', today())->count(),
            'espacio_usado' => $this->getStorageUsage(),
            'version_sistema' => '2.0.1',
            'ultima_actualizacion' => '2024-12-28'
        ];

        return view('configuracion.index', compact('configuracion', 'stats'));
    }

    public function update(Request $request)
    {
        // Aquí se guardaría la configuración en la base de datos o archivo
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ruc' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email',
            'igv' => 'required|numeric|min:0|max:100',
            'servicio' => 'required|numeric|min:0|max:100',
        ]);

        // Actualizar configuración (ejemplo)
        // DB::table('configuracion')->updateOrInsert(
        //     ['clave' => 'restaurante'],
        //     ['valor' => json_encode($request->all())]
        // );

        return redirect()->route('configuracion.index')
            ->with('success', 'Configuración actualizada correctamente');
    }

    private function getStorageUsage()
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $usedSpace = $totalSpace - $freeSpace;
        $percentage = round(($usedSpace / $totalSpace) * 100, 2);
        
        return [
            'total' => $this->formatBytes($totalSpace),
            'usado' => $this->formatBytes($usedSpace),
            'libre' => $this->formatBytes($freeSpace),
            'porcentaje' => $percentage
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
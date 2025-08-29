<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    
    protected $fillable = [
        'cliente_id',
        'mesa_id',
        'usuario_id',
        'total',
        'estado'
    ];
    
    protected $casts = [
        'total' => 'decimal:2'
    ];
}

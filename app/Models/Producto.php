<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'categoria_id',
        'imagen',
        'stock',
        'activo'
    ];
    
    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
        'stock' => 'integer'
    ];
    
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}

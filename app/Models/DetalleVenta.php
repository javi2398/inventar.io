<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = "detalle_ventas";

    protected $fillable = [
        'id_producto',
        'id_venta',
        'cantidad',
        'precio_unitario'
    ];

    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }

    public function venta(){
        return $this->belongsTo(Venta::class, 'id_venta', 'id');
    }
}

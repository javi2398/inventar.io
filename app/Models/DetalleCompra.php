<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $table = "detalle_compras";

    protected $fillable = [
        'id_producto',
        'id_compra',
        'id_almacen',
        'cantidad_actual',
        'precio_unitario',
        'fecha_vencimiento',
        'estado',
    ];

    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    public function almacen(){
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }

    public function compra(){
        return $this->belongsTo(Compra::class, 'id_compra');
    }

    public function getSubtotalAttribute()
    {   
    return $this->cantidad * $this->precio_unitario;
    }
}

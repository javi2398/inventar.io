<?php

namespace App\Models;

use App\Models\Almacen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model
{
    use HasFactory;

    protected $table = "inventarios";

    protected $fillable = [
        'id_producto',
        'id_almacen',
        'precio_unitario',
        'cantidad_actual',
        'fecha_entrada',
        'fecha_salida',
        'fecha_vencimiento',
    ];


    public function getSubtotalAttribute()
    {
        return $this->cantidad_actual * $this->precio_unitario;
    }

    // Crear el modelo y migracion de almacen

    public function almacenes()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }

    public function producto(){
        return $this->belongsTo(Producto::class,'id_producto');
    }
}

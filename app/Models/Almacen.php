<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Almacen extends Model
{
    //
    use HasFactory;

    protected $table = "almacenes";

    protected $fillable = [
        'id_user',
        'nombre',
        'direccion',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'inventarios', 'id_almacen', 'id_producto')
            ->withPivot('id_almacen','cantidad_actual', 'precio_unitario', 'fecha_entrada', 'fecha_salida');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }


}

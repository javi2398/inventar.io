<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comprador extends Model
{
    protected $table = 'compradores';

    protected $fillable = [
        'nombre',
        'identificacion',
        'telefono',
        'email',
        'direccion',
        'tipo_comprador',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_comprador', 'id');
    }
}

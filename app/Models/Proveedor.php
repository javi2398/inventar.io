<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_proveedor', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;

    protected $table = "ventas";

    protected $fillable = [
        'id_user',
        'id_comprador',
        'fecha_venta',
    ];

    public function detalleVentas(){
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function comprador(){
        return $this->belongsTo(Comprador::class, 'id_comprador', 'id');
    }
}

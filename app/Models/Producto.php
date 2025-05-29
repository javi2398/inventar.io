<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = "productos";

    protected $fillable = [
        'id_categoria',
        'codigo',
        'nombre',
        'descripcion',
        'perecedero',
        'imagen',
    ];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_producto');
    }

    public function almacenes()
    {
        return $this->belongsToMany(Almacen::class, 'inventarios', 'id_producto', 'id_almacen')
            ->withPivot('cantidad_actual', 'fecha_entrada', 'fecha_salida'); 
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id');
    }

    public function detalleventas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id');
    }

    public function detallecompras()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id');
    }

    public function proveedores($userId)
    {
        return Proveedor::whereHas('compras.detalleCompras', function ($query) use ($userId) {
            $query->where('id_producto', $this->id)
                ->whereHas('compra', fn($q) => $q->where('id_user', $userId));
        })->get();
    }
}

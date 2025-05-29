<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'id_user',
        'nombre',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public $timestamps = true;

    // Relación con prendas
    public function prendas()
    {
        return $this->hasMany(Prenda::class, 'categoria_id');
    }
}
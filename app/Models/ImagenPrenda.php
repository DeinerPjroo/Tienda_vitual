<?php

// ============================================
// App\Models\ImagenPrenda.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenPrenda extends Model
{
    use HasFactory;

    protected $table = 'imagenes_prenda';

    protected $fillable = [
        'prenda_id',
        'url',
        'texto_alternativo',
        'posicion'
    ];

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null; // Esta tabla NO tiene updated_at

    public $timestamps = true;

    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }
}
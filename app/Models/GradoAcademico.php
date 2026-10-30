<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradoAcademico extends Model
{
    protected $table = 'academic.grado_academico';

    protected $fillable = [
        'codigo_nivel_academico',
        'nombre',

        'fecha_crea',
        'fecha_actualiza',
        'fecha_elimina',

        'user_crea',
        'user_actualiza',
        'user_elimina',
        'estado'
    ];

    protected $hidden = [
        'user_crea',
        'user_actualiza',
        'user_elimina',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    public function nivelAcademico(): BelongsTo
    {
        return $this->belongsTo(DetalleParametro::class, 'codigo_nivel_academico', 'codigo');
    }
}

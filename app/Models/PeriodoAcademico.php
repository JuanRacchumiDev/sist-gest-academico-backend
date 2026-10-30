<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodoAcademico extends Model
{
    protected $table = 'academic.periodo_academico';

    protected $fillable = [
        'codigo_estado_periodo_academico',
        'nombre',
        'year',
        'fecha_inicio',
        'fecha_final',

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

    public function estadoPeriodoAcademico(): BelongsTo
    {
        return $this->belongsTo(DetalleParametro::class, 'codigo_estado_periodo_academico', 'codigo');
    }
}

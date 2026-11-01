<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodoEvaluacion extends Model
{
    protected $table = 'academic.periodo_evaluacion';

    protected $fillable = [
        'id_periodo_academico',
        'codigo_tipo_periodo_academico',
        'nombre',
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

    public function periodoAcademico(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class, 'id_periodo_academico', 'id');
    }

    public function tipoPeriodoAcademico(): BelongsTo
    {
        return $this->belongsTo(DetalleParametro::class, 'codigo_tipo_periodo_academico', 'codigo');
    }
}

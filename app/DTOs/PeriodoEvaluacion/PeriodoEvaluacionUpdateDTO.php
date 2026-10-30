<?php

namespace App\DTOs\PeriodoEvaluacion;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class PeriodoEvaluacionUpdateDTO extends Data
{
    public function __construct(
        public ?int $id_periodo_academico = null,
        public ?int $codigo_tipo_periodo_academico = null,
        public ?string $nombre = null,
        public ?string $fecha_inicio = null,
        public ?string $fecha_final = null,
        public ?bool $estado = null,

        public ?string $fecha_crea = null,
        public ?string $fecha_actualiza = null,
        public ?string $fecha_elimina = null,
        public ?string $user_crea = null,
        public ?string $user_actualiza = null,
        public ?string $user_elimina = null
    ) {}

    public static function withDefaults(): array
    {
        return [
            'estado' => true
        ];
    }

    public static function rules(): array
    {
        return [
            'id_periodo_academico' => [
                'sometimes',
                'integer',
                'exists:academic.periodo_academico,id',
                'nullable'
            ],
            'codigo_tipo_periodo_academico' => [
                'sometimes',
                'integer',
                'exists:academic.detalle_parametro,codigo',
                'nullable'
            ],
            'nombre' => [
                'sometimes',
                'string',
                'max:50',
                'nullable'
            ],
            'fecha_inicio' => [
                'sometimes',
                'string',
                'max:10',
                'nullable'
            ],
            'fecha_final' => [
                'sometimes',
                'string',
                'max:10',
                'nullable'
            ],
            'fecha_crea' => [
                'sometimes',
                'string:10',
                'nullable'
            ],
            'fecha_actualiza' => [
                'sometimes',
                'string:10',
                'nullable'
            ],
            'fecha_elimina' => [
                'sometimes',
                'string:10',
                'nullable'
            ],
            'user_crea' => [
                'sometimes',
                'string',
                'max:12',
                'nullable'
            ],
            'user_actualiza' => [
                'sometimes',
                'string',
                'max:12',
                'nullable'
            ],
            'user_elimina' => [
                'sometimes',
                'string',
                'max:12',
                'nullable'
            ],
            'estado' => [
                'sometimes',
                'boolean',
                'nullable'
            ]
        ];
    }
}

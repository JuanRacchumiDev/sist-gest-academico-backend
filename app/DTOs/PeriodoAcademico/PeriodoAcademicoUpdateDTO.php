<?php

namespace App\DTOs\PeriodoAcademico;

use Spatie\LaravelData\Data;

class PeriodoAcademicoUpdateDTO extends Data
{
    public function __construct(
        public ?int $codigo_estado_periodo_academico = null,
        public ?string $nombre = null,
        public ?int $year = null,
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

    /**
     * Define los valores por defecto para los campos opcionales/booleanos
     */
    public static function withDefaults(): array
    {
        return [
            'estado' => true
        ];
    }

    public static function rules(): array
    {
        return [
            'codigo_estado_periodo_academico' => [
                'sometimes',
                'integer',
                'exists:academic.detalle_parametro,codigo',
                'nullable'
            ],
            'nombre' => [
                'sometimes',
                'string',
                'sometimes:100',
                'nullable'
            ],
            'year' => [
                'sometimes',
                'integer',
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

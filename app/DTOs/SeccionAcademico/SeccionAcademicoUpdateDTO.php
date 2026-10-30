<?php

namespace App\DTOs\SeccionAcademico;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class SeccionAcademicoUpdateDTO extends Data
{
    public function __construct(
        public ?int $id_grado_academico = null,
        public ?string $nombre = null,
        public ?bool $estado = null,

        public ?int $capacidad_minima = null,
        public ?int $capacidad_maxima = null,
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
            'id_grado_academico' => [
                'sometimes',
                'integer',
                'exists:academic.grado_academico,id',
                'nullable'
            ],
            'nombre' => [
                'sometimes',
                'string',
                'max:20',
                'nullable'
            ],
            'capacidad_minima' => [
                'sometimes',
                'integer',
                'nullable'
            ],
            'capacidad_maxima' => [
                'sometimes',
                'integer',
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
                'required',
                'boolean'
            ]
        ];
    }
}

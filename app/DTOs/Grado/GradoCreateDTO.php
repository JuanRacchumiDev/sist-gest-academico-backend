<?php

namespace App\DTOs\Grado;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class GradoCreateDTO extends Data
{
    public function __construct(
        public int $codigo_nivel_academico,
        public string $nombre,
        public bool $estado,

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
            'codigo_nivel_academico' => [
                'required',
                'integer',
                'exists:academic.detalle_parametro,codigo'
            ],
            'nombre' => [
                'required',
                'string',
                'max:50'
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

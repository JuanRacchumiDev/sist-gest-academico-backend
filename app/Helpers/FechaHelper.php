<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class FechaHelper
{
    /**
     * Obtiene la fecha actual en formato Y-m-d
     * 
     * @return string
     */
    public static function obtenerFechaActual(): string
    {
        return Carbon::now()->format("Y-m-d");
    }

    /**
     * Obtiene el año actual
     * 
     * @return int
     */
    public static function obtenerAnioActual(): int
    {
        return (int) Carbon::now()->format('Y');
    }
}

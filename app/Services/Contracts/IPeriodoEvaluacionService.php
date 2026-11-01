<?php

namespace App\Services\Contracts;

use App\DTOs\PeriodoEvaluacion\PeriodoEvaluacionCreateDTO;
use App\DTOs\PeriodoEvaluacion\PeriodoEvaluacionUpdateDTO;
use App\Models\PeriodoEvaluacion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IPeriodoEvaluacionService
{
    public function getAllPeriodos(?array $searchParams = null): Collection;
    public function getAllPeriodosWithFilters(array $filters, int $perPage): LengthAwarePaginator;
    public function getPeriodoById(int $id): ?PeriodoEvaluacion;
    public function getPeriodoByUniqueCriteria(?int $idPeriodoAcademico, ?string $codigoTipoPeriodo, string $nombre): ?PeriodoEvaluacion;
    public function createPeriodo(PeriodoEvaluacionCreateDTO $dto): PeriodoEvaluacion;
    public function updatePeriodo(int $id, PeriodoEvaluacionUpdateDTO $dto): ?PeriodoEvaluacion;
    public function deletePeriodo(int $id): bool;
}

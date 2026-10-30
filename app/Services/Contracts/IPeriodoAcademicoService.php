<?php

namespace App\Services\Contracts;

use App\DTOs\PeriodoAcademico\PeriodoAcademicoCreateDTO;
use App\DTOs\PeriodoAcademico\PeriodoAcademicoUpdateDTO;
use App\Models\PeriodoAcademico;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IPeriodoAcademicoService
{
    public function getAllPeriodos(?array $searchParams = null): Collection;
    public function getAllPeriodosWithFilters(array $filters, int $perPage): LengthAwarePaginator;
    public function getPeriodoById(int $id): ?PeriodoAcademico;
    public function createPeriodo(PeriodoAcademicoCreateDTO $dto): PeriodoAcademico;
    public function updatePeriodo(int $id, PeriodoAcademicoUpdateDTO $dto): ?PeriodoAcademico;
    public function deletePeriodo(int $id): bool;
}

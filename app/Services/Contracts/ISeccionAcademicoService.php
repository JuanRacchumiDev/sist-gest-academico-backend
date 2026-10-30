<?php

namespace App\Services\Contracts;

use App\DTOs\SeccionAcademico\SeccionAcademicoCreateDTO;
use App\DTOs\SeccionAcademico\SeccionAcademicoUpdateDTO;
use App\Models\SeccionAcademico;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ISeccionAcademicoService
{
    public function getAllSecciones(?array $searchParams = null): Collection;
    public function getAllSeccionesWithFilters(array $filters, int $perPage): LengthAwarePaginator;
    public function getSeccionById(int $id): ?SeccionAcademico;
    public function createSeccion(SeccionAcademicoCreateDTO $dto): SeccionAcademico;
    public function updateSeccion(int $id, SeccionAcademicoUpdateDTO $dto): ?SeccionAcademico;
    public function deleteSeccion(int $id): bool;
}

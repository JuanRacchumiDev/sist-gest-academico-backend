<?php

namespace App\Services\Contracts;

use App\DTOs\GradoAcademico\GradoAcademicoCreateDTO;
use App\DTOs\GradoAcademico\GradoAcademicoUpdateDTO;
use App\Models\GradoAcademico;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IGradoAcademicoService
{
    public function getAllGrados(?array $searchParams = null): Collection;
    public function getAllGradosWithFilters(array $filters, int $perPage): LengthAwarePaginator;
    public function getGradoById(int $id): ?GradoAcademico;
    public function createGrado(GradoAcademicoCreateDTO $dto): GradoAcademico;
    public function updateGrado(int $id, GradoAcademicoUpdateDTO $dto): ?GradoAcademico;
    public function deleteGrado(int $id): bool;
}

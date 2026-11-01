<?php

namespace App\Services\Contracts;

use App\DTOs\Grado\GradoCreateDTO;
use App\DTOs\Grado\GradoUpdateDTO;
use App\Models\GradoAcademico;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IGradoService
{
    public function getAllGrados(?array $searchParams = null): Collection;
    public function getAllGradosWithFilters(array $filters, int $perPage): LengthAwarePaginator;
    public function getGradoById(int $id): ?GradoAcademico;
    public function createGrado(GradoCreateDTO $dto): GradoAcademico;
    public function updateGrado(int $id, GradoUpdateDTO $dto): ?GradoAcademico;
    public function deleteGrado(int $id): bool;
}

<?php

namespace App\Repositories\Contracts;

use App\Models\PeriodoAcademico;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IPeriodoAcademicoRepository
{
    public function getAll(?array $searchParams = null): Collection;

    public function getAllFiltered(array $filters, int $perPage): LengthAwarePaginator;

    public function findById(int $id): ?PeriodoAcademico;

    public function create(array $data): PeriodoAcademico;

    public function update(int $id, array $data): ?PeriodoAcademico;

    public function delete(int $id): bool;
}

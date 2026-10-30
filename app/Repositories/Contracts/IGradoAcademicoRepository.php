<?php

namespace App\Repositories\Contracts;

use App\Models\GradoAcademico;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IGradoAcademicoRepository
{
    public function getAll(?array $searchParams = null): Collection;

    public function getAllFiltered(array $filters, int $perPage): LengthAwarePaginator;

    public function findById(int $id): ?GradoAcademico;

    public function create(array $data): GradoAcademico;

    public function update(int $id, array $data): ?GradoAcademico;

    public function delete(int $id): bool;
}

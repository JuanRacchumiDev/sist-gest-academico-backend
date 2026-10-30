<?php

namespace App\Repositories\Contracts;

use App\Models\SeccionAcademico;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ISeccionAcademicoRepository
{
    public function getAll(?array $searchParams = null): Collection;

    public function getAllFiltered(array $filters, int $perPage): LengthAwarePaginator;

    public function findById(int $id): ?SeccionAcademico;

    public function create(array $data): SeccionAcademico;

    public function update(int $id, array $data): ?SeccionAcademico;

    public function delete(int $id): bool;
}

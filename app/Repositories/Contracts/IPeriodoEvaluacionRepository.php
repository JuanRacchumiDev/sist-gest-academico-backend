<?php

namespace App\Repositories\Contracts;

use App\Models\PeriodoEvaluacion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IPeriodoEvaluacionRepository
{
    public function getAll(?array $searchParams = null): Collection;

    public function getAllFiltered(array $filters, int $perPage): LengthAwarePaginator;

    public function findById(int $id): ?PeriodoEvaluacion;

    public function findByUniqueCriteria(?int $idPeriodoAcademico, ?string $codigoTipoPeriodo, string $nombre): ?PeriodoEvaluacion;

    public function create(array $data): PeriodoEvaluacion;

    public function update(int $id, array $data): ?PeriodoEvaluacion;

    public function delete(int $id): bool;
}

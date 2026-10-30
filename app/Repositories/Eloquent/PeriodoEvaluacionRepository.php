<?php

namespace App\Repositories\Eloquent;

use App\Models\PeriodoEvaluacion;
use App\Repositories\Contracts\IPeriodoEvaluacionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Override;

class PeriodoEvaluacionRepository implements IPeriodoEvaluacionRepository
{
    public function getAll(?array $searchParams = null): Collection
    {
        $query = PeriodoEvaluacion::with([
            'periodoAcademico',
            'tipoPeriodoAcademico'
        ]);

        $query = $this->applyFilters($query, $searchParams ?? []);

        return $query->get();
    }

    public function getAllFiltered(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = PeriodoEvaluacion::with([
            'periodoAcademico',
            'tipoPeriodoAcademico'
        ]);

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('id', 'DESC')->paginate($perPage);
    }

    public function findById(int $id): ?PeriodoEvaluacion
    {
        return PeriodoEvaluacion::with([
            'periodoAcademico',
            'tipoPeriodoAcademico'
        ])->find($id);
    }

    public function create(array $data): PeriodoEvaluacion
    {
        // Crear el registro de periodo evaluación
        $periodo = PeriodoEvaluacion::create($data);

        return $periodo;
    }

    public function update(int $id, array $data): ?PeriodoEvaluacion
    {
        $periodo = $this->findById($id);

        if ($periodo) {
            $periodo->update($data);
            return $periodo;
        }

        return null;
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['fechaInicio'])) {
            $query->where('fecha_inicio', '>=', $filters['fechaInicio']);
        }

        if (!empty($filters['fechaFinal'])) {
            $query->where('fecha_final', '<=', $filters['fechaFinal']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $searchTerm = '%' . mb_strtolower($search, 'UTF-8') . '%';

            $query->where(function (Builder $q) use ($searchTerm) {
                $q->whereRaw('LOWER(nombre) LIKE ?', [$searchTerm]);
            });
        }

        return $query;
    }

    public function delete(int $id): bool
    {
        $periodo = $this->findById($id);

        if ($periodo) {
            return $periodo->delete();
        }

        return false;
    }
}

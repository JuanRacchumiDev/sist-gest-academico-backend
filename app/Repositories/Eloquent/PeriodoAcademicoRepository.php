<?php

namespace App\Repositories\Eloquent;

use App\Models\PeriodoAcademico;
use App\Repositories\Contracts\IPeriodoAcademicoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Override;

class PeriodoAcademicoRepository implements IPeriodoAcademicoRepository
{
    public function getAll(?array $searchParams = null): Collection
    {
        $query = PeriodoAcademico::with([
            'estadoPeriodoAcademico'
        ]);

        $query = $this->applyFilters($query, $searchParams ?? []);

        return $query->get();
    }

    public function getAllFiltered(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = PeriodoAcademico::with([
            'estadoPeriodoAcademico'
        ]);

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('id', 'DESC')->paginate($perPage);
    }

    public function findById(int $id): ?PeriodoAcademico
    {
        return PeriodoAcademico::with([
            'estadoPeriodoAcademico'
        ])->find($id);
    }

    public function create(array $data): PeriodoAcademico
    {
        // Crear el registro de periodo académico
        $periodo = PeriodoAcademico::create($data);

        return $periodo;
    }

    public function update(int $id, array $data): ?PeriodoAcademico
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

        // Filtrar por nombre exacto (útil para la validación de duplicados en el store)
        if (!empty($filters['nombre'])) {
            $nombreTerm = '%' . mb_strtolower($filters['nombre'], 'UTF-8') . '%';
            $query->whereRaw('LOWER(nombre) LIKE ?', [$nombreTerm]);
        }

        // Filtrar por estado si existe
        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
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

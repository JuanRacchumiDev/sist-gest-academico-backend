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

    public function findByUniqueCriteria(?int $idPeriodoAcademico, ?string $codigoTipoPeriodo, string $nombre): ?PeriodoEvaluacion
    {
        return PeriodoEvaluacion::with([
            'periodoAcademico',
            'tipoPeriodoAcademico'
        ])
            ->where('id_periodo_academico', $idPeriodoAcademico)
            ->where('codigo_tipo_periodo_academico', $codigoTipoPeriodo)
            ->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre, 'UTF-8')])
            ->first();
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

        if (!empty($filters['id_periodo_academico'])) {
            $query->where('id_periodo_academico', $filters['id_periodo_academico']);
        }

        if (!empty($filters['codigo_tipo_periodo_academico'])) {
            $query->where('codigo_tipo_periodo_academico', $filters['codigo_tipo_periodo_academico']);
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

<?php

namespace App\Repositories\Eloquent;

use App\Models\GradoAcademico;
use App\Repositories\Contracts\IGradoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Override;

class GradoRepository implements IGradoRepository
{
    public function getAll(?array $searchParams = null): Collection
    {
        $query = GradoAcademico::with([
            'nivelAcademico'
        ]);

        $query = $this->applyFilters($query, $searchParams ?? []);

        return $query->get();
    }

    public function getAllFiltered(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = GradoAcademico::with([
            'nivelAcademico'
        ]);

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('id', 'DESC')->paginate($perPage);
    }

    public function findById(int $id): ?GradoAcademico
    {
        return GradoAcademico::with([
            'nivelAcademico'
        ])->find($id);
    }

    public function create(array $data): GradoAcademico
    {
        // Crear el registro de periodo evaluación
        $grado = GradoAcademico::create($data);

        return $grado;
    }

    public function update(int $id, array $data): ?GradoAcademico
    {
        $grado = $this->findById($id);

        if ($grado) {
            $grado->update($data);
            return $grado;
        }

        return null;
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        // Filtrar por código de nivel académico si viene en los parámetros
        if (!empty($filters['codigo_nivel_academico'])) {
            $query->where('codigo_nivel_academico', $filters['codigo_nivel_academico']);
        }

        // Filtrar por nombre exacto (útil para la validación de duplicados en el store)
        if (!empty($filters['nombre'])) {
            $nombreTerm = '%' . mb_strtolower($filters['nombre'], 'UTF-8') . '%';
            $query->whereRaw('LOWER(nombre) LIKE ?', [$nombreTerm]);
        }

        // Búsqueda general por texto (usada por 'search' si aplica)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $searchTerm = '%' . mb_strtolower($search, 'UTF-8') . '%';

            $query->where(function (Builder $q) use ($searchTerm) {
                $q->whereRaw('LOWER(nombre) LIKE ?', [$searchTerm]);
            });
        }

        // Filtrar por estado si existe
        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        return $query;
    }

    public function delete(int $id): bool
    {
        $grado = $this->findById($id);

        if ($grado) {
            return $grado->delete();
        }

        return false;
    }
}

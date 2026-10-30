<?php

namespace App\Repositories\Eloquent;

use App\Models\SeccionAcademico;
use App\Repositories\Contracts\ISeccionAcademicoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Override;

class SeccionAcademicoRepository implements ISeccionAcademicoRepository
{
    public function getAll(?array $searchParams = null): Collection
    {
        $query = SeccionAcademico::with([
            'gradoAcademico'
        ]);

        $query = $this->applyFilters($query, $searchParams ?? []);

        return $query->get();
    }

    public function getAllFiltered(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = SeccionAcademico::with([
            'gradoAcademico'
        ]);

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('id', 'DESC')->paginate($perPage);
    }

    public function findById(int $id): ?SeccionAcademico
    {
        return SeccionAcademico::with([
            'gradoAcademico'
        ])->find($id);
    }

    public function create(array $data): SeccionAcademico
    {
        // Crear el registro de sección académico
        $seccion = SeccionAcademico::create($data);

        return $seccion;
    }

    public function update(int $id, array $data): ?SeccionAcademico
    {
        $seccion = $this->findById($id);

        if ($seccion) {
            $seccion->update($data);
            return $seccion;
        }

        return null;
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
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
        $seccion = $this->findById($id);

        if ($seccion) {
            return $seccion->delete();
        }

        return false;
    }
}

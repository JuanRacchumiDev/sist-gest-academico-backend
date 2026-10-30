<?php

namespace App\Repositories\Eloquent;

use App\Models\GradoAcademico;
use App\Repositories\Contracts\IGradoAcademicoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Override;

class GradoAcademicoRepository implements IGradoAcademicoRepository
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
        // Crear el registro de grado académico
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
        $grado = $this->findById($id);

        if ($grado) {
            return $grado->delete();
        }

        return false;
    }
}

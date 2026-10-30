<?php

namespace App\Services;

use App\DTOs\SeccionAcademico\SeccionAcademicoCreateDTO;
use App\DTOs\SeccionAcademico\SeccionAcademicoUpdateDTO;
use App\Models\SeccionAcademico;
use App\Repositories\Contracts\ISeccionAcademicoRepository;
use App\Services\Contracts\ISeccionAcademicoService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Override;

class SeccionAcademicoService implements ISeccionAcademicoService
{
    protected ISeccionAcademicoRepository $seccionRepository;

    public function __construct(ISeccionAcademicoRepository $seccionRepository)
    {
        $this->seccionRepository = $seccionRepository;
    }

    public function getAllSecciones(?array $searchParams = null): Collection
    {
        return $this->seccionRepository->getAll();
    }

    /**
     * Obtiene secciones académicas filtrados
     * @param array<int, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator<SeccionAcademico>
     */
    public function getAllSeccionesWithFilters(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->seccionRepository->getAllFiltered($filters, $perPage);
    }

    public function getSeccionById(int $id): ?SeccionAcademico
    {
        return $this->seccionRepository->findById($id);
    }

    public function createSeccion(SeccionAcademicoCreateDTO $seccionCreateDTO): SeccionAcademico
    {
        Log::info('Iniciando creación de sección académica', [
            'seccionCreateDTO' => $seccionCreateDTO->toArray()
        ]);

        return DB::transaction(function () use ($seccionCreateDTO) {
            $dataToCreate = $seccionCreateDTO->toArray();
            Log::info('Evaluando variable $dataToCreate', ['dataToCreate' => $dataToCreate]);

            // Filtrar nulos
            $data = array_filter($dataToCreate, fn($value) => !is_null($value));

            $seccion = $this->seccionRepository->create($data);

            return $seccion;
        });
    }

    public function updateSeccion(int $id, SeccionAcademicoUpdateDTO $seccionUpdateDTO): ?SeccionAcademico
    {
        $data = array_filter($seccionUpdateDTO->toArray(), fn($value) => !is_null($value));

        return $this->seccionRepository->update($id, $data);
    }

    public function deleteSeccion(int $id): bool
    {
        $seccion = $this->seccionRepository->findById($id);

        if (!$seccion) {
            throw new \Exception("La sección académica con ID {$id} no fue encontrada.", 404);
        }

        return $this->seccionRepository->delete($id);
    }
}

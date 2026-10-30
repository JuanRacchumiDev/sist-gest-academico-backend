<?php

namespace App\Services;

use App\DTOs\GradoAcademico\GradoAcademicoCreateDTO;
use App\DTOs\GradoAcademico\GradoAcademicoUpdateDTO;
use App\Models\GradoAcademico;
use App\Repositories\Contracts\IGradoAcademicoRepository;
use App\Services\Contracts\IGradoAcademicoService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Override;

class GradoAcademicoService implements IGradoAcademicoService
{
    protected IGradoAcademicoRepository $gradoRepository;

    public function __construct(IGradoAcademicoRepository $gradoRepository)
    {
        $this->gradoRepository = $gradoRepository;
    }

    public function getAllGrados(?array $searchParams = null): Collection
    {
        return $this->gradoRepository->getAll();
    }

    /**
     * Obtiene grados académicos filtrados
     * @param array<int, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator<GradoAcademico>
     */
    public function getAllGradosWithFilters(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->gradoRepository->getAllFiltered($filters, $perPage);
    }

    public function getGradoById(int $id): ?GradoAcademico
    {
        return $this->gradoRepository->findById($id);
    }

    public function createGrado(GradoAcademicoCreateDTO $gradoCreateDTO): GradoAcademico
    {
        Log::info('Iniciando creación de grado académico', [
            'gradoCreateDTO' => $gradoCreateDTO->toArray()
        ]);

        return DB::transaction(function () use ($gradoCreateDTO) {
            $dataToCreate = $gradoCreateDTO->toArray();
            Log::info('Evaluando variable $dataToCreate', ['dataToCreate' => $dataToCreate]);

            // Filtrar nulos
            $data = array_filter($dataToCreate, fn($value) => !is_null($value));

            $grado = $this->gradoRepository->create($data);

            return $grado;
        });
    }

    public function updateGrado(int $id, GradoAcademicoUpdateDTO $gradoUpdateDTO): ?GradoAcademico
    {
        $data = array_filter($gradoUpdateDTO->toArray(), fn($value) => !is_null($value));

        return $this->gradoRepository->update($id, $data);
    }

    public function deleteGrado(int $id): bool
    {
        $grado = $this->gradoRepository->findById($id);

        if (!$grado) {
            throw new \Exception("El grado académico con ID {$id} no fue encontrado.", 404);
        }

        return $this->gradoRepository->delete($id);
    }
}

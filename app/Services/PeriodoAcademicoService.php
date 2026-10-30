<?php

namespace App\Services;

use App\DTOs\PeriodoAcademico\PeriodoAcademicoCreateDTO;
use App\DTOs\PeriodoAcademico\PeriodoAcademicoUpdateDTO;
use App\Models\PeriodoAcademico;
use App\Repositories\Contracts\IPeriodoAcademicoRepository;
use App\Services\Contracts\IPeriodoAcademicoService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Override;

class PeriodoAcademicoService implements IPeriodoAcademicoService
{
    protected IPeriodoAcademicoRepository $periodoRepository;

    public function __construct(IPeriodoAcademicoRepository $periodoRepository)
    {
        $this->periodoRepository = $periodoRepository;
    }

    public function getAllPeriodos(?array $searchParams = null): Collection
    {
        return $this->periodoRepository->getAll($searchParams);
    }

    /**
     * Obtiene periodos académicos filtrados
     * @param array<int, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator<PeriodoAcademico>
     */
    public function getAllPeriodosWithFilters(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->periodoRepository->getAllFiltered($filters, $perPage);
    }

    public function getPeriodoById(int $id): ?PeriodoAcademico
    {
        return $this->periodoRepository->findById($id);
    }

    public function createPeriodo(PeriodoAcademicoCreateDTO $periodoCreateDTO): PeriodoAcademico
    {
        Log::info('Iniciando creación de periodo académico', [
            'periodoCreateDTO' => $periodoCreateDTO->toArray()
        ]);

        return DB::transaction(function () use ($periodoCreateDTO) {
            $dataToCreate = $periodoCreateDTO->toArray();
            Log::info('Evaluando variable $dataToCreate', ['dataToCreate' => $dataToCreate]);

            // Filtrar nulos
            $data = array_filter($dataToCreate, fn($value) => !is_null($value));

            $periodo = $this->periodoRepository->create($data);

            return $periodo;
        });
    }

    public function updatePeriodo(int $id, PeriodoAcademicoUpdateDTO $periodoUpdateDTO): ?PeriodoAcademico
    {
        $data = array_filter($periodoUpdateDTO->toArray(), fn($value) => !is_null($value));

        return $this->periodoRepository->update($id, $data);
    }

    public function deletePeriodo(int $id): bool
    {
        $periodo = $this->periodoRepository->findById($id);

        if (!$periodo) {
            throw new \Exception("El periodo académico con ID {$id} no fue encontrado.", 404);
        }

        return $this->periodoRepository->delete($id);
    }
}

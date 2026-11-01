<?php

namespace App\Services;

use App\DTOs\PeriodoEvaluacion\PeriodoEvaluacionCreateDTO;
use App\DTOs\PeriodoEvaluacion\PeriodoEvaluacionUpdateDTO;
use App\Models\PeriodoEvaluacion;
use App\Repositories\Contracts\IPeriodoEvaluacionRepository;
use App\Services\Contracts\IPeriodoEvaluacionService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Override;

class PeriodoEvaluacionService implements IPeriodoEvaluacionService
{
    protected IPeriodoEvaluacionRepository $periodoRepository;

    public function __construct(IPeriodoEvaluacionRepository $periodoRepository)
    {
        $this->periodoRepository = $periodoRepository;
    }

    public function getAllPeriodos(?array $searchParams = null): Collection
    {
        return $this->periodoRepository->getAll();
    }

    /**
     * Obtiene periodos académicos filtrados
     * @param array<int, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator<PeriodoEvaluacion>
     */
    public function getAllPeriodosWithFilters(array $filters, int $perPage): LengthAwarePaginator
    {
        return $this->periodoRepository->getAllFiltered($filters, $perPage);
    }

    public function getPeriodoById(int $id): ?PeriodoEvaluacion
    {
        return $this->periodoRepository->findById($id);
    }

    public function getPeriodoByUniqueCriteria(?int $idPeriodoAcademico, ?string $codigoTipoPeriodo, string $nombre): ?PeriodoEvaluacion
    {
        return $this->periodoRepository->findByUniqueCriteria($idPeriodoAcademico, $codigoTipoPeriodo, $nombre);
    }

    public function createPeriodo(PeriodoEvaluacionCreateDTO $periodoCreateDTO): PeriodoEvaluacion
    {
        Log::info('Iniciando creación de periodo evaluación', [
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

    public function updatePeriodo(int $id, PeriodoEvaluacionUpdateDTO $periodoUpdateDTO): ?PeriodoEvaluacion
    {
        $data = array_filter($periodoUpdateDTO->toArray(), fn($value) => !is_null($value));

        return $this->periodoRepository->update($id, $data);
    }

    public function deletePeriodo(int $id): bool
    {
        $periodo = $this->periodoRepository->findById($id);

        if (!$periodo) {
            throw new \Exception("El periodo evaluación con ID {$id} no fue encontrado.", 404);
        }

        return $this->periodoRepository->delete($id);
    }
}

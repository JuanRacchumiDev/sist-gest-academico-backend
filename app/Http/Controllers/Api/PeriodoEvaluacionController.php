<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTOs\PeriodoEvaluacion\PeriodoEvaluacionCreateDTO;
use App\DTOs\PeriodoEvaluacion\PeriodoEvaluacionUpdateDTO;
use App\Services\PeriodoEvaluacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helpers\FechaHelper;

class PeriodoEvaluacionController extends Controller
{
    protected PeriodoEvaluacionService $periodoService;

    public function __construct(PeriodoEvaluacionService $periodoService)
    {
        $this->periodoService = $periodoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getFiltered(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'id_periodo_academico',
                'codigo_tipo_periodo_academico',
                'nombre',
                'fecha_inicio',
                'fecha_final',
                'estado'
            ]);

            $filters = array_map(function ($value) {
                if ($value === 'true') return true;
                if ($value === 'false') return false;
                return $value;
            }, $filters);

            $periodos = $this->periodoService->getAllPeriodos($filters);

            if ($periodos->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'data' => [],
                    'message' => 'No se encontraron resultados'
                ], 200);
            }

            return response()->json([
                'result' => true,
                'data' => $periodos,
                'message' => 'Resultados encontrados correctamente'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error filtering periodos de evaluación: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener periodos de evaluación filtrados.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFilteredPaginate(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'id_periodo_academico',
                'codigo_tipo_periodo_academico',
                'fecha_inicio',
                'fecha_final',
            ]);

            if ($request->has('nombre')) {
                $filters['nombre'] = $request->input('nombre');
            }

            $perPage = (int)$request->input('limit', 10);

            $periodos = $this->periodoService->getAllPeriodosWithFilters($filters, $perPage);

            if ($periodos->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'data' => [],
                    'message' => 'No se encontraron resultados'
                ], 200);
            }

            return response()->json([
                'result' => true,
                'data' => $periodos,
                'message' => 'Resultados encontrados correctamente',
                'pagination' => [
                    'totalItems' => $periodos->total(),
                    'perPage' => $periodos->perPage(),
                    'currentPage' => $periodos->currentPage(),
                    'totalPages' => $periodos->lastPage(),
                    'nextPage' => $periodos->hasMorePages() ? $periodos->currentPage() + 1 : null,
                    'previousPage' => $periodos->currentPage() > 1 ? $periodos->currentPage() - 1 : null,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error filtering periodos de evaluación: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener periodos de evaluación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            $idPeriodoAcademico = $data['id_periodo_academico'] ?? null;
            $codigoTipoPeriodo = $data['codigo_tipo_periodo_academico'] ?? null;
            $nombre = $data['nombre'] ?? "";

            $periodoExistente = $this->periodoService->getPeriodoByUniqueCriteria($idPeriodoAcademico, $codigoTipoPeriodo, $nombre);

            if ($periodoExistente) {
                return response()->json([
                    'result' => true,
                    'data' => $periodoExistente,
                    'message' => 'El período de evaluación se encuentra registrado',
                    'code' => 'PREVIOUSLY_REGISTERED'
                ], 200);
            }

            $usuarioAutenticado = Auth::user();
            $username = $usuarioAutenticado ? ($usuarioAutenticado->name) : 'systemapi';
            $data['user_crea'] = $username;
            $data['fecha_crea'] = FechaHelper::obtenerFechaActual();

            if (!isset($data['estado'])) {
                $data['estado'] = true;
            }

            $periodoCreateDTO = PeriodoEvaluacionCreateDTO::from($data);

            $periodo = $this->periodoService->createPeriodo($periodoCreateDTO);

            return response()->json([
                'result' => true,
                'data' => $periodo,
                'message' => 'Período de evaluación creada exitosamente',
                'code' => 'CORRECT_RECORDED'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'code' => 'INVALID_RECORD'
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error creating periodo de evaluación: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al crear periodo de evaluación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $periodo = $this->periodoService->getPeriodoById($id);

            if (!$periodo) {
                return response()->json([
                    'result' => false,
                    'message' => 'Período de evaluación no encontrado',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'result' => true,
                'data' => $periodo,
                'message' => 'Periodo de evaluación encontrado correctamente'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching periodo de evaluación (id: {$id}): " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener el periodo de evaluación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $usuarioAutenticado = Auth::user();
            $username = $usuarioAutenticado ? ($usuarioAutenticado->name) : 'systemapi';
            $data['user_actualiza'] = $username;
            $data['fecha_actualiza'] = FechaHelper::obtenerFechaActual();

            $periodoUpdateDTO = PeriodoEvaluacionUpdateDTO::from([
                ...$data,
                'id' => $id
            ]);

            $periodo = $this->periodoService->updatePeriodo($id, $periodoUpdateDTO);

            if (!$periodo) {
                return response()->json([
                    'result' => false,
                    'data' => [],
                    'message' => 'Periodo de evaluación no encontrado o no se pudo actualizar'
                ], 404);
            }

            return response()->json([
                'result' => true,
                'data' => $periodo,
                'message' => 'Periodo de evaluación actualizado correctamente'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $message = 'Error al actualizar el periodo de evaluación:' . $e->getMessage();

            Log::error("Error updating periodo de evaluación: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => $message
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

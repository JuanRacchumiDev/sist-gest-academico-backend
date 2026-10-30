<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTOs\PeriodoAcademico\PeriodoAcademicoCreateDTO;
use App\DTOs\PeriodoAcademico\PeriodoAcademicoUpdateDTO;
use App\Services\PeriodoAcademicoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helpers\FechaHelper;

class PeriodoAcademicoController extends Controller
{
    protected PeriodoAcademicoService $periodoService;

    public function __construct(PeriodoAcademicoService $periodoService)
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
                'codigo_estado_periodo_academico',
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
            Log::error("Error filtering periodos académicos: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener periodos académicos filtrados.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFilteredPaginate(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'codigo_estado_periodo_academico',
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
            Log::error("Error filtering periodos académicos: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener periodos académicos.',
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

            $filters = [
                'codigo_estado_periodo_academico' => $data['codigo_estado_periodo_academico'] ?? null,
                'nombre' => $data['nombre'] ?? ""
            ];

            $periodoExistente = $this->periodoService->getAllPeriodos($filters)->first();

            if ($periodoExistente) {
                return response()->json([
                    'result' => true,
                    'data' => $periodoExistente,
                    'message' => 'El período académico se encuentra registrada',
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

            if (!isset($data['year'])) {
                $data['year'] = FechaHelper::obtenerAnioActual();
            }

            $periodoCreateDTO = PeriodoAcademicoCreateDTO::from($data);

            $periodo = $this->periodoService->createPeriodo($periodoCreateDTO);

            return response()->json([
                'result' => true,
                'data' => $periodo,
                'message' => 'Período académico creado exitosamente',
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
            Log::error("Error creating periodo académico: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al crear periodo académico: ' . $e->getMessage()
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
                    'message' => 'Período académico no encontrado',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'result' => true,
                'data' => $periodo,
                'message' => 'Periodo académico encontrado correctamente'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching periodo académico (id: {$id}): " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener el periodo académico: ' . $e->getMessage()
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

            $periodoUpdateDTO = PeriodoAcademicoUpdateDTO::from([
                ...$data,
                'id' => $id
            ]);

            $periodo = $this->periodoService->updatePeriodo($id, $periodoUpdateDTO);

            if (!$periodo) {
                return response()->json([
                    'result' => false,
                    'data' => [],
                    'message' => 'Periodo académico no encontrado o no se pudo actualizar'
                ], 404);
            }

            return response()->json([
                'result' => true,
                'data' => $periodo,
                'message' => 'Periodo académico actualizado correctamente'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $message = 'Error al actualizar el periodo académico:' . $e->getMessage();

            Log::error("Error updating periodo académico: " . $e->getMessage());

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

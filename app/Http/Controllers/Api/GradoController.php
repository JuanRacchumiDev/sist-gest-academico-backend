<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTOs\Grado\GradoCreateDTO;
use App\DTOs\Grado\GradoUpdateDTO;;

use App\Services\GradoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helpers\FechaHelper;

class GradoController extends Controller
{
    protected GradoService $gradoService;

    public function __construct(GradoService $gradoService)
    {
        $this->gradoService = $gradoService;
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
                'codigo_nivel_academico',
                'nombre',
                'estado'
            ]);

            $filters = array_map(function ($value) {
                if ($value === 'true') return true;
                if ($value === 'false') return false;
                return $value;
            }, $filters);

            $grados = $this->gradoService->getAllGrados($filters);

            if ($grados->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'data' => [],
                    'message' => 'No se encontraron resultados'
                ], 200);
            }

            return response()->json([
                'result' => true,
                'data' => $grados,
                'message' => 'Resultados encontrados correctamente'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error filtering grados académicos: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener grados académicos filtrados.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFilteredPaginate(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'codigo_nivel_academico',
            ]);

            if ($request->has('nombre')) {
                $filters['nombre'] = $request->input('nombre');
            }

            $perPage = (int)$request->input('limit', 10);

            $grados = $this->gradoService->getAllGradosWithFilters($filters, $perPage);

            if ($grados->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'data' => [],
                    'message' => 'No se encontraron resultados'
                ], 200);
            }

            return response()->json([
                'result' => true,
                'data' => $grados,
                'message' => 'Resultados encontrados correctamente',
                'pagination' => [
                    'totalItems' => $grados->total(),
                    'perPage' => $grados->perPage(),
                    'currentPage' => $grados->currentPage(),
                    'totalPages' => $grados->lastPage(),
                    'nextPage' => $grados->hasMorePages() ? $grados->currentPage() + 1 : null,
                    'previousPage' => $grados->currentPage() > 1 ? $grados->currentPage() - 1 : null,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error filtering grados académicos: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener grados académicos.',
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
                'codigo_nivel_academico' => $data['codigo_nivel_academico'] ?? null,
                'nombre' => $data['nombre'] ?? ""
            ];

            $gradoExistente = $this->gradoService->getAllGrados($filters)->first();

            if ($gradoExistente) {
                return response()->json([
                    'result' => true,
                    'data' => $gradoExistente,
                    'message' => 'El grado académico se encuentra registrado',
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

            $gradoCreateDTO = GradoCreateDTO::from($data);

            $grado = $this->gradoService->createGrado($gradoCreateDTO);

            return response()->json([
                'result' => true,
                'data' => $grado,
                'message' => 'Grado académico creado exitosamente',
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
            Log::error("Error creating grado académico: " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al crear grado académico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $grado = $this->gradoService->getGradoById($id);

            if (!$grado) {
                return response()->json([
                    'result' => false,
                    'message' => 'Grado académico no encontrado',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'result' => true,
                'data' => $grado,
                'message' => 'Grado académico encontrado correctamente'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching grado académico (id: {$id}): " . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Error al obtener el grado académico: ' . $e->getMessage()
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

            $gradoUpdateDTO = GradoUpdateDTO::from([
                ...$data,
                'id' => $id
            ]);

            $grado = $this->gradoService->updateGrado($id, $gradoUpdateDTO);

            if (!$grado) {
                return response()->json([
                    'result' => false,
                    'data' => [],
                    'message' => 'Grado académico no encontrado o no se pudo actualizar'
                ], 404);
            }

            return response()->json([
                'result' => true,
                'data' => $grado,
                'message' => 'Grado académico actualizado correctamente'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $message = 'Error al actualizar el grado académico:' . $e->getMessage();

            Log::error("Error updating grado académico: " . $e->getMessage());

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

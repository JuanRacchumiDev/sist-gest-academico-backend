<?php

namespace App\Repositories\Eloquent;

use App\Models\Certificado;
use App\Repositories\Contracts\ICertificadoRepository;
use App\Services\Contracts\IStorageService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Override;

class CertificadoRepository implements ICertificadoRepository
{
    protected IStorageService $storageService;

    public function __construct(IStorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function getAll(?array $searchParams = null): Collection
    {
        $query = $this->applyFilters($searchParams ?? []);

        return $query->get();
    }

    public function getAllFiltered(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->applyFilters($filters)->paginate($perPage);
    }

    public function findById(int $id): ?Certificado
    {
        return Certificado::with([
            'persona.grupos',
            'tipoCertificado',
            'sucursal',
            'plantilla.institucion',
            'programa.tipoPrograma',
            'programa.detalleModulos',
            'modulo'
        ])->find($id);
    }

    public function findByCodigo(string $codigo): ?Certificado
    {
        return Certificado::with([
            'persona.grupos',
            'tipoCertificado',
            'sucursal',
            'plantilla.institucion',
            'programa.tipoPrograma',
            'programa.detalleModulos',
            'modulo'
        ])
            ->where('codigo_verificacion', $codigo)
            ->first();
    }

    public function create(array $data): Certificado
    {
        return Certificado::create($data);
    }

    public function update(int $id, array $data): ?Certificado
    {
        $certificado = $this->findById($id);

        if (!$certificado) {
            return null;
        }

        $certificado->update($data);

        return $certificado;
    }

    public function delete(int $id): bool
    {
        $certificado = $this->findById($id);

        if (!$certificado) return false;

        return DB::transaction(function () use ($certificado) {
            // Obteniendo la ruta de los archivos a eliminar
            $pdfPath = trim("{$certificado->path_file}/{$certificado->filename}");
            $qrPath = $certificado->codigo_qr_path;

            $filesToDelete = array_filter([$pdfPath, $qrPath]);

            // Eliminar archivos físicos
            if (!empty($filesToDelete)) {
                try {
                    $this->storageService->delete($filesToDelete);
                } catch (\Exception $e) {
                    Log::warning("No se pudieron eliminar algunos archivos del storage para el certificado ID {$certificado->id}: " . $e->getMessage());
                }
            }

            return (bool) $certificado->delete();
        });
    }

    private function applyFilters(array $filters): Builder
    {
        Log::info('Validando variables de filtrado', ['filters' => $filters]);

        $query = Certificado::query()
            ->with([
                'persona.grupos',
                'tipoCertificado',
                'sucursal',
                'plantilla.institucion',
                'programa.tipoPrograma',
                'programa.detalleModulos',
                'modulo'
            ]);

        // Filtro por Búsqueda General (Código de verificación, Nombres o Documento de la Persona)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $searchTerm = '%' . mb_strtolower($search, 'UTF-8') . '%';

            $query->where(function (Builder $q) use ($searchTerm) {
                $q->whereRaw('LOWER(nombre_impresion) LIKE ?', [$searchTerm])
                    ->orWhereHas('persona', function (Builder $personaQuery) use ($searchTerm) {
                        $personaQuery->whereRaw('LOWER(numero_documento) LIKE ?', $searchTerm);
                    });
            });
        }

        // Filtro por sucursal
        if (!empty($filters['id_sucursal'])) {
            $query->where('id_sucursal', $filters['id_sucursal']);
        }

        // Filtro por institución
        if (!empty($filters['id_institucion'])) {
            $idInstitucion = $filters['id_institucion'];

            $query->whereHas('plantilla', function ($q) use ($idInstitucion) {
                $q->where('id_institucion', $idInstitucion);
            });
        }

        // Filtro por Programa
        if (!empty($filters['id_programa'])) {
            $query->where('id_programa', $filters['id_programa']);
        }

        // Ordenamiento descendente por fecha de registro
        return $query->orderBy('academic.certificado.fecha_crea', 'DESC');
    }
}

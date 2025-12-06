<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait HasDataTable
{
    /**
     * Aplica sort + per_page automáticamente para DataTableServer.
     * Detecta columnas ordenables automáticamente desde la BD.
     */
    public function scopeForDataTable(
        Builder $query,
        Request $request,
        ?array $allowedSorts = null,
        ?string $defaultSort = null,
        string $defaultDir = 'asc',
        int $defaultPerPage = 15
    ) {
        $model = $query->getModel();
        $table = $model->getTable();

        /*
        |--------------------------------------------------------------------------
        | 1. Determinar columnas ordenables
        |--------------------------------------------------------------------------
        |  Orden de prioridad:
        |   a) Parámetro $allowedSorts pasado al scope
        |   b) Constante DATATABLE_SORTABLE del modelo
        |   c) Propiedad datatableSortable
        |   d) (por defecto) TODAS las columnas del modelo
        */
        if ($allowedSorts === null) {

            $class = get_class($model);
            $const = $class . '::DATATABLE_SORTABLE';

            if (defined($const)) {
                // a) Modelo define constante
                $allowedSorts = constant($const);

            } elseif (property_exists($model, 'datatableSortable')) {
                // b) Modelo define propiedad
                $allowedSorts = $model->datatableSortable;

            } else {
                // c) Auto-detectar todas las columnas del modelo
                $columns = Schema::getColumnListing($table);

                // Siempre ignoramos columnas problemáticas
                $ignored = [
                    'password',
                    'remember_token',
                    'deleted_at',
                ];

                $allowedSorts = array_values(array_diff($columns, $ignored));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Determinar sort por defecto
        |--------------------------------------------------------------------------
        */
        if ($defaultSort === null) {
            $defaultSort = $allowedSorts[0] ?? $model->getKeyName();
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Paginación
        |--------------------------------------------------------------------------
        */
        $perPage = $request->integer('per_page', $defaultPerPage);

        /*
        |--------------------------------------------------------------------------
        | 4. Ordenamiento (sort_by / sort_dir)
        |--------------------------------------------------------------------------
        */
        $sortBy  = $request->get('sort_by');
        $sortDir = $request->get('sort_dir', $defaultDir);

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy($defaultSort, $defaultDir);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Paginator final compatible con Inertia
        |--------------------------------------------------------------------------
        */
        return $query->paginate($perPage)->withQueryString();
    }
}

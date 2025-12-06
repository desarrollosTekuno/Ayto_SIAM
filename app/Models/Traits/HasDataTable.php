<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait HasDataTable
{
    /**
     * Aplica búsqueda, sort y per_page para DataTableServer.
     *
     * Prioridades:
     *  - allowedSorts:
     *      a) parámetro $allowedSorts
     *      b) const DATATABLE_SORTABLE
     *      c) propiedad $datatableSortable
     *      d) TODAS las columnas de la tabla (menos algunas ignoradas)
     *
     *  - searchableColumns:
     *      a) parámetro $searchableColumns
     *      b) const DATATABLE_SEARCHABLE
     *      c) propiedad $datatableSearchable
     *      d) mismas columnas que allowedSorts
     */
    public function scopeForDataTable(
        Builder $query,
        Request $request,
        ?array $allowedSorts      = null,
        ?string $defaultSort      = null,
        string $defaultDir        = 'asc',
        int $defaultPerPage       = 15,
        ?array $searchableColumns = null
    ) {
        $model = $query->getModel();
        $table = $model->getTable();

        /*
        |--------------------------------------------------------------------------
        | 1. Determinar columnas ordenables (allowedSorts)
        |--------------------------------------------------------------------------
        */
        if ($allowedSorts === null) {
            $class = get_class($model);
            $const = $class . '::DATATABLE_SORTABLE';

            if (defined($const)) {
                $allowedSorts = (array) constant($const);

            } elseif (property_exists($model, 'datatableSortable')) {
                $allowedSorts = (array) $model->datatableSortable;

            } else {
                // Auto: todas las columnas de la tabla, excepto algunas sensibles
                $columns = Schema::getColumnListing($table);

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
        | 2. Determinar columnas buscables (searchableColumns)
        |--------------------------------------------------------------------------
        */
        if ($searchableColumns === null) {
            $classS = get_class($model);
            $constS = $classS . '::DATATABLE_SEARCHABLE';

            if (defined($constS)) {
                $searchableColumns = (array) constant($constS);

            } elseif (property_exists($model, 'datatableSearchable')) {
                $searchableColumns = (array) $model->datatableSearchable;

            } else {
                // Por defecto: mismas columnas que se pueden ordenar
                $searchableColumns = $allowedSorts;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Búsqueda global (search)
        |--------------------------------------------------------------------------
        */
        $search = trim((string) $request->get('search', ''));

        if ($search !== '' && !empty($searchableColumns)) {
            $query->where(function (Builder $q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Sort por defecto / desde la tabla
        |--------------------------------------------------------------------------
        */
        if ($defaultSort === null) {
            $defaultSort = $allowedSorts[0] ?? $model->getKeyName();
        }

        $perPage = $request->integer('per_page', $defaultPerPage);

        $sortBy  = $request->get('sort_by');
        $sortDir = strtolower($request->get('sort_dir', $defaultDir));
        if (! in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = $defaultDir;
        }

        if ($sortBy && in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy($defaultSort, $sortDir);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Paginator final
        |--------------------------------------------------------------------------
        */
        return $query->paginate($perPage)->withQueryString();
    }
}

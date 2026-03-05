<?php

namespace App\Traits;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

/**
 * Trait LivewirePaginationTrait
 *
 * Provides reusable pagination and sorting for Livewire components.
 * Compatible with Livewire 3.
 */
trait LivewirePaginationTrait
{
    /**
     * Handle paginated query with optional sorting.
     *
     * @param $query
     * @return mixed
     */
    public function handlePaginatedQuery($query): mixed
    {
        // Ensure limit is valid
        $limit = property_exists($this, 'limit') && (int)$this->limit > 0 ? (int)$this->limit : 10;

        // Get current page using Livewire's WithPagination method if available
        $page = method_exists($this, 'getPage') ? $this->getPage() : 1;

        // Clone the query before applying sorting
        $queryBackup = clone $query;
        try {
            // Apply sorting if sort & order properties exist
            if (property_exists($this, 'sort') && property_exists($this, 'order')) {
                $sort = $this->sort;
                $order = strtolower($this->order ?? 'asc');
                $order = in_array($order, ['asc', 'desc']) ? $order : 'asc';

                if (!empty($sort)) {
                    $query->orderBy($sort, $order);
                }
            }

            $result = $this->doPaginate($query, $limit, $page);
        } catch (QueryException $e) {
            // Catch unknown column errors (SQLSTATE 42S22)
            if ($e->getCode() === '42S22') {
                Log::error(
                    message: "Invalid sort column [{$this->sort}] on table [{$query->getModel()->getTable()}], fallback without sorting.",
                );

                // Retry using the clone (fresh query, no sorting applied yet)
                $query = $queryBackup;
                $result = $this->doPaginate($query, $limit, $page);
            } else {
                throw $e;
            }
        }

        // If requested page is empty but data exists, fallback to last page
        if ($result->isEmpty() && $result->total() > 0 && $result->currentPage() > 1) {
            $lastPage = $result->lastPage();

            if (method_exists($this, 'setPage')) {
                $this->setPage($lastPage);
            }

            $result = $this->doPaginate($query, $limit, $lastPage);
        }

        return $result;
    }

    /**
     * Toggle sorting field and direction.
     *
     * @param string $field
     * @return void
     */
    public function sortBy(string $field): void
    {
        if (property_exists($this, 'sort') && property_exists($this, 'order')) {
            $this->order = ($this->sort === $field && $this->order === 'asc') ? 'desc' : 'asc';
            $this->sort = $field;
        }
    }

    /**
     * Private helper to paginate consistently.
     *
     * @param $query
     * @param int $limit
     * @param int $page
     * @return mixed
     */
    private function doPaginate($query, int $limit, int $page): mixed
    {
        // paginate($perPage, $columns, $pageName, $page)
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @return void
     */
    public function clearSearch(): void
    {
        if (property_exists($this, 'search')) {
            $this->search = '';
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }
        }
    }
}

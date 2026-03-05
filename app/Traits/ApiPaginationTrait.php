<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait ApiPaginationTrait
{
    protected int $page = 1;
    protected int $limit = 5;
    protected string $order = 'ASC';

    /**
     * this function is used to set pagination manually
     *
     * @param int $page
     * @param int $limit
     * @param int $total
     * @param mixed $data
     * @return array
     */
    protected function manualPaginateWrapper(int $page, int $limit, int $total, mixed $data = []): array
    {
        return [
            "_metadata" => [
                "page" => $page,
                "per_page" => $limit,
                "total" => $total
            ],
            "records" => $data
        ];
    }

    /**
     * this function is used to set pagination automatically using laravel paginate eloquent
     *
     * @param mixed $data
     * @return array
     */
    protected static function autoPaginateWrapper(mixed $data): array
    {
        return [
            "_metadata" => [
                "page" => (int)$data->currentPage(),
                "per_page" => (int)$data->perPage(),
                "total" => (int)$data->total(),
            ],
            "records" => $data->items()
        ];
    }

    /**
     * Bungkus response paginated data dengan format standar API v2.
     *
     * Paginator itu hasil dari query yang pakai ->paginate().
     * Transformed data biasanya hasil dari ->map() atau ->transform().
     * Jika data kosong setelah transformasi, akan langsung menggunakan data dari paginator->items().
     * Jika tidak ada transformasi, langsung kirim data dari paginator tanpa perubahan.
     *
     * Contoh penggunaan:
     *
     * 1. **Jika kamu menggunakan transformasi data**:
     *
     * $paginator = SpRequestLop::with(...)->paginate(); // Contoh query paginate()
     * $data = $paginator->getCollection()->transform(function ($item) {
     *     return [
     *         'id' => $item->id,
     *         'code' => $item->code,
     *         'name' => $item->name,
     *         // Lainnya...
     *     ];
     * });
     * return autoPaginateWrapperV2($paginator, $data);
     *
     * 2. **Jika tidak perlu transformasi, langsung ambil dari paginator**:
     *
     * $paginator = SpRequestLop::paginate(); // Query paginate() langsung
     * return autoPaginateWrapperV2($paginator);
     *
     * 3. **Jika transformasi menghasilkan data kosong**:
     *
     * $paginator = SpRequestLop::with(...)->paginate();
     * $data = $paginator->getCollection()->transform(function ($item) {
     *     return null;  // Data kosong
     * });
     * return autoPaginateWrapperV2($paginator, $data); // Ambil dari paginator->items()
     *
     * @param LengthAwarePaginator $paginator Hasil query paginate()
     * @param Collection|array|null $transformedData Data hasil format ulang, defaultnya null jika tidak diberikan
     * @return array
     */
    protected static function autoPaginateWrapperV2(
        LengthAwarePaginator $paginator,
        Collection|array     $transformedData = null // Default ke null jika tidak ada data transformasi
    ): array
    {
        // Jika data transformedData null, ambil dari paginator->items()
        if (is_null($transformedData)) {
            $transformedData = $paginator->items();
        }

        return [
            "wrapper-v2" => true,
            "headers" => [
                "Total-Count" => $paginator->total(),
                "Per-Page" => $paginator->perPage(),
                "Current-Page" => $paginator->currentPage(),
                "Total-Pages" => $paginator->lastPage(),
            ],
            "records" => $transformedData
        ];
    }

}

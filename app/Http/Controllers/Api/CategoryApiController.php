<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant;
use App\Traits\ApiResponserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    use ApiResponserTrait;

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->restaurant;

        // Ambil kategori yang dimiliki restoran ini
        // Urutkan berdasarkan order_column jika ada, atau nama
        $categories = Category::where('restaurant_id', $restaurant->id)
            ->orderBy('order_column')
            ->pluck('name')
            ->toArray();

        // Return array string kategori, contoh: ["makanan", "minuman"]
        return $this->successResponse($categories);
    }
}

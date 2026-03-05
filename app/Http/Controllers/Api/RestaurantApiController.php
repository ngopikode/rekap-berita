<?php

namespace App\Http\Controllers\Api;

use App\Helpers\TenantUrl;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Traits\ApiResponserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantApiController extends Controller
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

        $data = [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'logo' => $restaurant->logo ? TenantUrl::asset($restaurant->logo) : null,
            'theme_color' => $restaurant->theme_color,
            'whatsapp_number' => $restaurant->whatsapp_number,
            'address' => $restaurant->address,
            'hero' => [
                'promo_text' => $restaurant->hero_promo_text,
                'status_text' => $restaurant->hero_status_text,
                'headline' => $restaurant->hero_headline,
                'tagline' => $restaurant->hero_tagline,
                'instagram_url' => $restaurant->hero_instagram_url,
            ],
            'navbar' => [
                'brand_text' => $restaurant->navbar_brand_text,
                'title' => $restaurant->navbar_title,
                'subtitle' => $restaurant->navbar_subtitle,
            ],
            'seo' => [
                'title' => $restaurant->seo_title,
                'description' => $restaurant->seo_description,
                'keywords' => $restaurant->seo_keywords,
                'og_title' => $restaurant->og_title,
                'og_description' => $restaurant->og_description,
                'og_image' => $restaurant->og_image ? TenantUrl::asset($restaurant->og_image) : null
            ]
        ];

        return $this->successResponse($data);
    }
}

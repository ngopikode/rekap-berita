<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Restaurant;
use App\Services\Product\ProductService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;

class MenuController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        protected ProductService $productService
    )
    {
    }

    /**
     * Show a product preview for social media bots.
     *
     * @param Request $request
     * @param string $subdomain
     * @param string $productId
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Redirector|\Illuminate\View\View
     */
    public function showProductPreview(Request $request, string $subdomain, string $productId)
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->restaurant;
        $product = $this->productService->getProduct($restaurant, $productId);
        $fullReactUrl = $this->getReactAppUrl($request, $subdomain);

        if ($this->isSocialMediaBot($request)) {
            return view('product_preview', [
                'restaurant' => $restaurant,
                'product' => $product,
                'image_url' => $this->productService->getProductImageUrl($product),
                'react_app_url' => $fullReactUrl,
            ]);
        }

        return redirect("$fullReactUrl#$productId");
    }

    /**
     * Menampilkan halaman preview story (HTML)
     */
    public function shareAsStory(Request $request, $subdomain, $productId)
    {
        $restaurant = $request->restaurant;
        $product = $this->productService->getProduct($restaurant, $productId);

        $fullReactUrl = $this->getReactAppUrl($request, $subdomain);
        $productUrl = "$fullReactUrl/menu/$productId";

        return view('story_preview', [
            'restaurant' => $restaurant,
            'product' => $product,
            'image_url' => $this->productService->getProductImageUrl($product),
            'product_url' => $productUrl,
            'share_text' => $this->generateShareText($product, $restaurant),
            'share_title' => "$product->name - $restaurant->name"
        ]);
    }

    /**
     * Men-generate gambar story (JPEG)
     */
    public function generateStoryImage(Request $request, $subdomain, $productId)
    {
        set_time_limit(60);

        $restaurant = $request->restaurant;
        $product = $this->productService->getProduct($restaurant, $productId);

        // --- CACHING STRATEGY ---
        $cacheFileName = "stories/$subdomain/{$product->id}_{$product->updated_at->timestamp}.jpg";
        $downloadName = Str::slug($product->name) . '-story.jpg';

        if (Storage::disk('public')->exists($cacheFileName)) {
            return $this->downloadStoryImage($cacheFileName, $downloadName);
        }

        $this->clearOldStoryCache($subdomain, $product->id);

        $productImagePath = Storage::disk('public')->path($product->image);

        if (!file_exists($productImagePath)) {
            abort(404, 'Product image not found');
        }

        $this->createStoryImage($product, $restaurant, $productImagePath, $cacheFileName);

        return $this->downloadStoryImage($cacheFileName, $downloadName);
    }

    /**
     * Construct the React App Base URL.
     */
    private function getReactAppUrl(Request $request, string $subdomain): string
    {
        $reactAppBaseUrl = config('app.frontend_url_base');
        $protocol = $request->isSecure() ? 'https' : 'http';
        return "$protocol://$subdomain.$reactAppBaseUrl";
    }

    /**
     * Check if the request comes from a social media bot.
     */
    private function isSocialMediaBot(Request $request): bool
    {
        $userAgent = strtolower($request->header('User-Agent'));
        $bots = ['whatsapp', 'facebookexternalhit', 'facebot', 'twitterbot', 'linkedinbot'];

        foreach ($bots as $bot) {
            if (str_contains($userAgent, $bot)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate the sharing text for the story.
     */
    private function generateShareText(Product $product, Restaurant $restaurant): string
    {
        $generalHooks = [
            '✨ *Barangkali lagi kepikiran ini...*',
            '🌟 *Salah satu yang paling sering dicari nih,*',
            '📍 *Jangan sampai kelewat yang satu ini ya,*',
            '🍃 *Pilihan pas buat nemenin hari kamu,*',
            '🤍 *Rekomendasi spesial buat kamu,*',
            '💡 *Cek deh, siapa tahu kamu suka,*'
        ];

        $randomHook = $generalHooks[array_rand($generalHooks)];
        $priceFormatted = 'Rp ' . number_format($product->price, 0, ',', '.');

        $shareText = "$randomHook\n\n";
        $shareText .= "*$product->name* @ *$restaurant->name*\n";

        if ($product->description) {
            $shareText .= "_\"$product->description\"_\n\n";
        } else {
            $shareText .= "\n";
        }

        $shareText .= "Harganya cuma *$priceFormatted* aja lho. ✨\n\n";
        $shareText .= "Cek selengkapnya atau langsung order di sini ya:\n";

        return $shareText;
    }

    /**
     * Clear old cached story images for the product.
     */
    private function clearOldStoryCache(int $subdomain, int $productId): void
    {
        $directory = "stories/$subdomain";
        if (!Storage::disk('public')->exists($directory)) {
            return;
        }

        $oldFiles = Storage::disk('public')->files($directory);
        foreach ($oldFiles as $file) {
            if (Str::startsWith(basename($file), "{$productId}_")) {
                Storage::disk('public')->delete($file);
            }
        }
    }

    /**
     * Return a download response for the story image.
     */
    private function downloadStoryImage(string $filePath, string $downloadName)
    {
        return response()->download(Storage::disk('public')->path($filePath), $downloadName, [
            'Content-Type' => 'image/jpeg'
        ]);
    }

    /**
     * Create and save the story image.
     */
    private function createStoryImage(Product $product, Restaurant $restaurant, string $sourceImagePath, string $destCachePath): void
    {
        // Canvas HD (720x1280)
        $canvasWidth = 720;
        $canvasHeight = 1280;

        $img = Image::create($canvasWidth, $canvasHeight)->fill('#ffffff');

        // --- BACKGROUND (Fake Blur) ---
        $backgroundImage = Image::read($sourceImagePath);
        $backgroundImage->cover(36, 64);
        $backgroundImage->resize($canvasWidth, $canvasHeight);

        $overlay = Image::create($canvasWidth, $canvasHeight)->fill('rgba(0, 0, 0, 0.35)');
        $backgroundImage->place($overlay);

        $img->place($backgroundImage);

        // --- MAIN IMAGE ---
        $mainImage = Image::read($sourceImagePath);
        $mainImage->scale(width: 550);
        $img->place($mainImage, 'center');

        // --- FONT HANDLING ---
        $fontBold = $this->getFontPath('Poppins-Bold.ttf');
        $fontRegular = $this->getFontPath('Poppins-Regular.ttf');
        $fontLight = $this->getFontPath('Poppins-Light.ttf');

        // --- TEXT ---

        // Nama Produk
        if ($fontBold) {
            $img->text($product->name, $canvasWidth / 2, 900, function (FontFactory $font) use ($fontBold) {
                $font->filename($fontBold);
                $font->size(50);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('bottom');
            });
        }

        // Harga
        if ($fontRegular) {
            $img->text('Rp ' . number_format($product->price, 0, ',', '.'), $canvasWidth / 2, 980, function (FontFactory $font) use ($fontRegular) {
                $font->filename($fontRegular);
                $font->size(40);
                $font->color('#ffdd00');
                $font->align('center');
                $font->valign('bottom');
            });
        }

        // Call to Action (Tanpa QR)
        if ($fontRegular) {
            $img->text('Cek menu lengkap di link bio!', $canvasWidth / 2, 1150, function (FontFactory $font) use ($fontRegular) {
                $font->filename($fontRegular);
                $font->size(24);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
        }

        // Nama Restoran
        if ($fontLight) {
            $img->text($restaurant->name, $canvasWidth / 2, 1230, function (FontFactory $font) use ($fontLight) {
                $font->filename($fontLight);
                $font->size(24);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('bottom');
            });
        }

        // Simpan ke Cache
        $directory = dirname($destCachePath);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $img->save(Storage::disk('public')->path($destCachePath), 80);
    }

    /**
     * Get the file path for a font if it exists.
     */
    private function getFontPath(string $fontName): ?string
    {
        $path = public_path("fonts/$fontName");
        return file_exists($path) ? $path : null;
    }
}

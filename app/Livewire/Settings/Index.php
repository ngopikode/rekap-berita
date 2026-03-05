<?php

namespace App\Livewire\Settings;

use App\Models\Restaurant;
use App\Traits\CompressesImages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads, CompressesImages;

    // General Info
    #[Rule('required|string|max:255')]
    public $restaurantName;

    #[Rule('required|string|max:50|alpha_dash')]
    public $subdomain;

    #[Rule('nullable|string|max:20')]
    public $whatsappNumber;

    #[Rule('nullable|string|max:500')]
    public $address;

    #[Rule('nullable|string|max:7')]
    public $themeColor;

    #[Rule('nullable|image|max:2048')]
    public $logo;

    public $existingLogo;
    public $isActive = true;

    // Hero Section
    #[Rule('nullable|string|max:255')]
    public $heroPromoText;

    #[Rule('nullable|string|max:255')]
    public $heroStatusText;

    #[Rule('nullable|string|max:255')]
    public $heroHeadline;

    #[Rule('nullable|string|max:255')]
    public $heroTagline;

    #[Rule('nullable|url|max:255')]
    public $heroInstagramUrl;

    // Navbar
    #[Rule('nullable|string|max:255')]
    public $navbarBrandText;

    #[Rule('nullable|string|max:255')]
    public $navbarTitle;

    #[Rule('nullable|string|max:255')]
    public $navbarSubtitle;

    // SEO
    #[Rule('nullable|string|max:255')]
    public $seoTitle;

    #[Rule('nullable|string|max:500')]
    public $seoDescription;

    #[Rule('nullable|string|max:255')]
    public $seoKeywords;

    #[Rule('nullable|string|max:255')]
    public $ogTitle;

    #[Rule('nullable|string|max:500')]
    public $ogDescription;

    #[Rule('nullable|image|max:2048')]
    public $ogImage;

    public $existingOgImage;

    // State
    public $activeTab = 'general';
    public $hasRestaurant = false;
    public $saved = false;

    public function mount()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if ($restaurant) {
            $this->hasRestaurant = true;
            $this->restaurantName = $restaurant->name;
            $this->subdomain = $restaurant->subdomain;
            $this->whatsappNumber = $restaurant->whatsapp_number;
            $this->address = $restaurant->address;
            $this->themeColor = $restaurant->theme_color ?? '#F50057';
            $this->existingLogo = $restaurant->logo;
            $this->isActive = $restaurant->is_active;

            // Hero
            $this->heroPromoText = $restaurant->hero_promo_text;
            $this->heroStatusText = $restaurant->hero_status_text;
            $this->heroHeadline = $restaurant->hero_headline;
            $this->heroTagline = $restaurant->hero_tagline;
            $this->heroInstagramUrl = $restaurant->hero_instagram_url;

            // Navbar
            $this->navbarBrandText = $restaurant->navbar_brand_text;
            $this->navbarTitle = $restaurant->navbar_title;
            $this->navbarSubtitle = $restaurant->navbar_subtitle;

            // SEO
            $this->seoTitle = $restaurant->seo_title;
            $this->seoDescription = $restaurant->seo_description;
            $this->seoKeywords = $restaurant->seo_keywords;
            $this->ogTitle = $restaurant->og_title;
            $this->ogDescription = $restaurant->og_description;
            $this->existingOgImage = $restaurant->og_image;
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $restaurant = $user->restaurant;

        $logoPath = $this->existingLogo;
        if ($this->logo) {
            if ($this->existingLogo) {
                Storage::disk('public')->delete($this->existingLogo);
            }
            $logoPath = $this->compressAndStore($this->logo, 'logos/' . $user->id);
        }

        $ogImagePath = $this->existingOgImage;
        if ($this->ogImage) {
            if ($this->existingOgImage) {
                Storage::disk('public')->delete($this->existingOgImage);
            }
            $ogImagePath = $this->compressAndStore($this->ogImage, 'og/' . $user->id);
        }

        $data = [
            'name' => $this->restaurantName,
            'subdomain' => $this->subdomain,
            'whatsapp_number' => $this->whatsappNumber,
            'address' => $this->address,
            'theme_color' => $this->themeColor,
            'logo' => $logoPath,
            'is_active' => $this->isActive,
            // Hero
            'hero_promo_text' => $this->heroPromoText,
            'hero_status_text' => $this->heroStatusText,
            'hero_headline' => $this->heroHeadline,
            'hero_tagline' => $this->heroTagline,
            'hero_instagram_url' => $this->heroInstagramUrl,
            // Navbar
            'navbar_brand_text' => $this->navbarBrandText,
            'navbar_title' => $this->navbarTitle,
            'navbar_subtitle' => $this->navbarSubtitle,
            // SEO
            'seo_title' => $this->seoTitle,
            'seo_description' => $this->seoDescription,
            'seo_keywords' => $this->seoKeywords,
            'og_title' => $this->ogTitle,
            'og_description' => $this->ogDescription,
            'og_image' => $ogImagePath,
        ];

        if ($restaurant) {
            $restaurant->update($data);
        } else {
            $data['user_id'] = $user->id;
            Restaurant::create($data);
            $this->hasRestaurant = true;
        }

        $this->saved = true;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.settings.index');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'subdomain',
        'logo',
        'theme_color',
        'whatsapp_number',
        'address',
        'is_active',
        // Hero Section
        'hero_promo_text',
        'hero_status_text',
        'hero_headline',
        'hero_tagline',
        'hero_instagram_url',
        // Navbar
        'navbar_brand_text',
        'navbar_title',
        'navbar_subtitle',
        // SEO Fields
        'seo_title',
        'seo_description',
        'seo_keywords',
        'og_title',
        'og_description',
        'og_image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

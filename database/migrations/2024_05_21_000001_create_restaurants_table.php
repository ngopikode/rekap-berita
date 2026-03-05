<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->string('logo')->nullable();
            $table->string('theme_color', 7)->default('#f59e0b');
            $table->string('whatsapp_number', 20)->nullable();
            $table->text('address')->nullable();

            // SEO Fields
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            // Hero Section
            $table->string('hero_promo_text')->default('Promo');
            $table->string('hero_status_text')->default('Buka Sekarang');
            $table->string('hero_headline')->default('Enjoy & Dine');
            $table->string('hero_tagline')->default('Nikmati menu spesial kami.');
            $table->string('hero_instagram_url')->nullable();

            // Navbar
            $table->string('navbar_brand_text')->default('Ez');
            $table->string('navbar_title')->nullable();
            $table->string('navbar_subtitle')->default('Menu Digital');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};

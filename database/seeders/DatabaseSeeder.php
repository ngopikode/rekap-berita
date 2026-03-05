<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel untuk menghindari duplikasi saat seeding ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Restaurant::truncate();
        Category::truncate();
        Product::truncate();
        Option::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Buat User (Pemilik Restoran)
        $user = User::create([
            'name' => 'Pemilik Rokus',
            'email' => 'owner@samarotikukus.com',
            'password' => Hash::make('password'),
        ]);

        // 3. Buat Restoran (Tenant)
        $restaurant = Restaurant::create([
            'user_id' => $user->id,
            'name' => 'Sama Roti Kukus',
            'subdomain' => 'samarotikukus',
            // Path disesuaikan dengan struktur storage/app/public/logos/{restaurant_id}/...
            'logo' => 'logos/1/logo.png',
            'theme_color' => '#d4b982',
            'whatsapp_number' => '6282283668001',
            'address' => "Kompleks @allnewtsjcafe, Bangkot Kab Kampar",
            'is_active' => true,

            // Hero Section
            'hero_promo_text' => 'Promo',
            'hero_status_text' => 'Open until 11.00PM',
            'hero_headline' => 'Seruput & Gigit',
            'hero_tagline' => '"StayTune the Healthy People"',
            'hero_instagram_url' => 'https://www.instagram.com/sama.co.id',

            // Navbar
            'navbar_brand_text' => 'Sama',
            'navbar_title' => 'Roti Kukus',
            'navbar_subtitle' => "Kampar • Est. 10'19th",

            // SEO
            'seo_title' => 'Sama Roti Kukus - Seruput & Gigit | Kuliner Hits Kampar',
            'seo_description' => 'Nikmati sensasi roti kukus lumer dan minuman kekinian paling hits di Kampar sejak 2019. Lokasi: Kompleks @allnewtsjcafe. Pesan online sekarang!',
            'seo_keywords' => 'Sama Roti Kukus, Rokus Kampar, Kuliner Kampar, Roti Kukus Lumer, Jajanan Kampar',
            'og_title' => 'Sama Roti Kukus - Seruput & Gigit',
            'og_description' => 'Camilan lumer paling hits di Kampar. Harga mulai 7k! Beli 4 Gratis 1.',
            // Path disesuaikan dengan struktur storage/app/public/og_images/{restaurant_id}/...
            'og_image' => 'og_images/1/og-main.jpg',
        ]);

        // 4. Buat Kategori untuk restoran ini
        $catRoti = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Roti',
            'order_column' => 1,
        ]);

        $catMinuman = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Minuman',
            'order_column' => 2,
        ]);

        // 5. Data Menu Lengkap dengan path gambar yang diperbarui
        // Path gambar sekarang menunjuk ke 'products/{restaurant_id}/...'
        $menuData = [
            [
                'id' => 1, 'cat' => 'roti', 'name' => 'Rokus Original', 'price' => 7000, 'desc' => 'Pilih 1 varian rasa klasik favoritmu.',
                'type' => 'single', 'options' => ['Cokelat Original', 'Tiramisu', 'Keju', 'Sarikaya', 'Choco Cruncy'],
                'img' => 'products/1/rokus-ori.png'
            ],
            [
                'id' => 2, 'cat' => 'roti', 'name' => 'Rokus Mix', 'price' => 8000, 'desc' => 'Perpaduan 2 rasa lumer dalam satu gigitan.',
                'type' => 'single', 'options' => ['Cokelat + Keju', 'Cokelat + Oreo', 'Cokelat + Kacang', 'Cokelat + Tiramisu', 'Sarikaya + Keju', 'Tiramisu + Keju', 'Tiramisu + Oreo', 'Choco Cruncy + Oreo'],
                'img' => 'products/1/rokus-mix.png'
            ],
            [
                'id' => 3, 'cat' => 'roti', 'name' => 'Rokus Combo', 'price' => 10000, 'desc' => 'Eksplorasi rasa dengan maksimal 3 topping.',
                'type' => 'multi', 'options' => ['Cokelat', 'Tiramisu', 'Keju', 'Sarikaya', 'Oreo', 'Kacang', 'Choco Cruncy'],
                'img' => 'products/1/rokus-combo.png'
            ],
            [
                'id' => 4, 'cat' => 'minuman', 'name' => 'Kopi Susu Aren', 'price' => 11000, 'desc' => 'Signature coffee dengan gula aren asli.',
                'type' => 'single', 'options' => [], 'img' => 'products/1/kopi-susu-aren.jpeg'
            ],
            [
                'id' => 5, 'cat' => 'minuman', 'name' => 'Blue Ocean', 'price' => 11000, 'desc' => 'Kesegaran soda biru lemon yang unik.',
                'type' => 'single', 'options' => [], 'img' => 'products/1/blue-ocean.jpeg'
            ],
            [
                'id' => 6, 'cat' => 'minuman', 'name' => 'Milky Mango', 'price' => 11000, 'desc' => 'Creamy milk dengan rasa mangga manis.',
                'type' => 'single', 'options' => [], 'img' => 'products/1/milky-mango.jpeg'
            ],
            [
                'id' => 7, 'cat' => 'minuman', 'name' => 'Passion Soda', 'price' => 11000, 'desc' => 'Soda markisa yang menyegarkan dahaga.',
                'type' => 'single', 'options' => [], 'img' => 'products/1/passion-soda.jpeg'
            ],
            [
                'id' => 8, 'cat' => 'minuman', 'name' => 'Choco Milky', 'price' => 11000, 'desc' => 'Susu cokelat creamy yang nyoklat banget.',
                'type' => 'single', 'options' => [], 'img' => 'products/1/choco-milky.jpeg'
            ],
            [
                'id' => 9, 'cat' => 'minuman', 'name' => 'Yakult Mango', 'price' => 11000, 'desc' => 'Perpaduan Yakult dan mangga yang segar.',
                'type' => 'single', 'options' => [], 'img' => 'products/1/yakult-mango.jpeg'
            ],
            [
                'id' => 10, 'cat' => 'minuman', 'name' => 'Yakult Peach', 'price' => 11000, 'desc' => 'Kesegaran Yakult dengan aroma peach.',
                'type' => 'single', 'options' => [], 'img' => 'products/1/yakult-peach.jpeg'
            ],
            [
                'id' => 11, 'cat' => 'minuman', 'name' => 'Cendol Aren', 'price' => 10000, 'desc' => 'Cita rasa tradisional cendol gula aren.',
                'type' => 'single', 'options' => [], 'img' => 'products/1/cendol-aren.png'
            ]
        ];

        // 6. Loop dan masukkan setiap produk beserta opsinya
        $orderColumn = 1;
        foreach ($menuData as $item) {
            $product = Product::create([
                'restaurant_id' => $restaurant->id,
                'category_id' => ($item['cat'] === 'roti') ? $catRoti->id : $catMinuman->id,
                'name' => $item['name'],
                'description' => $item['desc'],
                'price' => $item['price'],
                'image' => $item['img'],
                'type' => $item['type'],
                'order_column' => $orderColumn++,
                'is_available' => true,
            ]);

            // Jika ada opsi, masukkan ke tabel options
            if (!empty($item['options'])) {
                foreach ($item['options'] as $optionName) {
                    Option::create([
                        'product_id' => $product->id,
                        'name' => $optionName,
                    ]);
                }
            }
        }
    }
}

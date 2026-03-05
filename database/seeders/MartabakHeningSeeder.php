<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MartabakHeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat User Baru (Pemilik Martabak Hening)
        // Kita gunakan email unik agar tidak bentrok dengan user lama
        $user = User::create([
            'name' => 'Owner Martabak Hening',
            'email' => 'owner@martabakhening.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Buat Restoran (Tenant Baru)
        $restaurant = Restaurant::create([
            'user_id' => $user->id,
            'name' => 'Martabak Hening',
            'subdomain' => 'martabakhening',
            // Gunakan placeholder path, nanti file gambarnya diupload ke folder ID restoran yang baru terbentuk
            'logo' => 'logos/martabak-hening/logo.png',
            'theme_color' => '#f59e0b', // Warna oranye keemasan khas martabak
            'whatsapp_number' => '6285172441544', // Nomor kamu (format 62)
            'address' => "Jl. Kemenangan No. 88, (Depan Indomaret Point)",
            'is_active' => true,

            // Hero Section
            'hero_promo_text' => 'Promo Spesial',
            'hero_status_text' => 'Buka 17.00 - 23.00',
            'hero_headline' => 'Manis & Gurih',
            'hero_tagline' => 'Nikmati kelembutan di setiap gigitan.',
            'hero_instagram_url' => 'https://www.instagram.com/martabakhening',

            // Navbar
            'navbar_brand_text' => 'Martabak',
            'navbar_title' => 'Hening',
            'navbar_subtitle' => 'Sejak 2020',

            // SEO
            'seo_title' => 'Martabak Hening - Manis & Telur Premium',
            'seo_description' => 'Martabak manis dengan topping melimpah dan martabak telur renyah. Bahan premium, rasa juara.',
            'seo_keywords' => 'Martabak Hening, Martabak Manis, Terang Bulan, Martabak Telur, Kuliner Malam',
            'og_title' => 'Martabak Hening - Tebal & Lembut',
            'og_description' => 'Lapar malam? Martabak Hening solusinya. Order via WhatsApp sekarang!',
            'og_image' => 'og_images/martabak-hening/og-main.jpg',
        ]);

        // 3. Buat Kategori untuk Martabak Hening
        $catManis = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Martabak Manis',
            'order_column' => 1,
        ]);

        $catTelur = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Martabak Telur',
            'order_column' => 2,
        ]);

        $catMinum = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Minuman Segar',
            'order_column' => 3,
        ]);

        // 4. Data Menu Martabak
        $menuData = [

            // ===============================
            // ===== MARTABAK MANIS (30) =====
            // ===============================

            ['cat_id' => $catManis->id, 'name' => 'Manis Original Cokelat Kacang', 'price' => 25000, 'desc' => 'Adonan lembut dengan cokelat meses dan kacang sangrai.', 'type' => 'single', 'options' => ['Biasa', 'Pakai Wijen'], 'img' => 'products/martabak-hening/manis-01.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Keju Susu', 'price' => 28000, 'desc' => 'Keju cheddar parut dengan susu kental manis.', 'type' => 'single', 'options' => ['Extra Keju'], 'img' => 'products/martabak-hening/manis-02.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Cokelat Wijen', 'price' => 26000, 'desc' => 'Meses cokelat dengan taburan wijen gurih.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-03.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Keju Cokelat', 'price' => 30000, 'desc' => 'Perpaduan keju dan cokelat lumer.', 'type' => 'single', 'options' => ['Tipis', 'Tebal'], 'img' => 'products/martabak-hening/manis-04.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Pandan Keju', 'price' => 32000, 'desc' => 'Adonan pandan dengan topping keju melimpah.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-05.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Red Velvet Oreo', 'price' => 35000, 'desc' => 'Base red velvet dengan cream cheese oreo.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-06.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Blackforest', 'price' => 35000, 'desc' => 'Cokelat premium dengan topping meses dark.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-07.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Tiramisu', 'price' => 36000, 'desc' => 'Rasa tiramisu lembut dengan taburan cokelat.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-08.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Matcha Almond', 'price' => 37000, 'desc' => 'Matcha premium dengan almond slice.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-09.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Nutella Keju', 'price' => 38000, 'desc' => 'Olesan nutella asli dengan keju parut.', 'type' => 'single', 'options' => ['Extra Nutella'], 'img' => 'products/martabak-hening/manis-10.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Milo Keju', 'price' => 33000, 'desc' => 'Taburan milo tebal dengan keju.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-11.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Strawberry Keju', 'price' => 32000, 'desc' => 'Selai strawberry dengan topping keju.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-12.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Blueberry Cream', 'price' => 33000, 'desc' => 'Blueberry dengan cream manis lembut.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-13.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Durian Keju', 'price' => 40000, 'desc' => 'Durian asli dengan keju premium.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-14.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Kismis Kacang', 'price' => 28000, 'desc' => 'Kismis manis dengan kacang sangrai.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-15.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Toblerone', 'price' => 39000, 'desc' => 'Cokelat toblerone premium.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-16.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Silverqueen', 'price' => 39000, 'desc' => 'Cokelat silverqueen legit.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-17.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Kitkat Keju', 'price' => 36000, 'desc' => 'Kitkat crunch dengan keju parut.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-18.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Oreo Cream', 'price' => 34000, 'desc' => 'Oreo crumble dengan krim manis.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-19.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Lotus Biscoff', 'price' => 40000, 'desc' => 'Biscoff spread dengan crumble.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-20.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Skippy Peanut', 'price' => 35000, 'desc' => 'Selai kacang skippy creamy.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-21.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Pisang Cokelat', 'price' => 30000, 'desc' => 'Pisang segar dengan cokelat lumer.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-22.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Pisang Keju', 'price' => 31000, 'desc' => 'Pisang manis dan keju gurih.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-23.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Double Keju', 'price' => 36000, 'desc' => 'Keju melimpah dua lapis.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-24.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Triple Cokelat', 'price' => 38000, 'desc' => '3 jenis cokelat premium.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-25.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Jagung Keju', 'price' => 30000, 'desc' => 'Jagung manis dengan keju parut.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-26.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Cappuccino', 'price' => 33000, 'desc' => 'Rasa kopi cappuccino legit.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-27.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Ovomaltine', 'price' => 38000, 'desc' => 'Ovomaltine crunchy premium.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-28.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Cheese Cream', 'price' => 35000, 'desc' => 'Krim keju lembut manis.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-29.jpg'],
            ['cat_id' => $catManis->id, 'name' => 'Manis Komplit Spesial', 'price' => 42000, 'desc' => 'Cokelat, keju, kacang, wijen lengkap.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/manis-30.jpg'],


            // ===============================
            // ===== MARTABAK TELUR (30) =====
            // ===============================

            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Ayam', 'price' => 22000, 'desc' => 'Isian ayam cincang gurih.', 'type' => 'single', 'options' => ['2 Telur', '3 Telur', '4 Telur'], 'img' => 'products/martabak-hening/telur-01.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Sapi', 'price' => 28000, 'desc' => 'Daging sapi berbumbu kari.', 'type' => 'single', 'options' => ['2 Telur', '3 Telur'], 'img' => 'products/martabak-hening/telur-02.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Bebek', 'price' => 30000, 'desc' => 'Telur bebek lebih gurih.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-03.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Mozzarella', 'price' => 35000, 'desc' => 'Telur sapi dengan mozzarella leleh.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-04.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Kornet', 'price' => 27000, 'desc' => 'Isian kornet sapi.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-05.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Sosis', 'price' => 26000, 'desc' => 'Tambahan sosis potong.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-06.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Tuna', 'price' => 29000, 'desc' => 'Isian tuna pedas.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-07.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Rendang', 'price' => 35000, 'desc' => 'Daging rendang khas.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-08.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur BBQ', 'price' => 33000, 'desc' => 'Rasa saus BBQ.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-09.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Blackpepper', 'price' => 33000, 'desc' => 'Daging lada hitam.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-10.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Smoked Beef', 'price' => 34000, 'desc' => 'Smoked beef premium.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-11.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Extra Daging', 'price' => 38000, 'desc' => 'Daging dua kali lipat.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-12.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Pedas', 'price' => 28000, 'desc' => 'Level pedas pilihan.', 'type' => 'single', 'options' => ['Level 1', 'Level 2', 'Level 3'], 'img' => 'products/martabak-hening/telur-13.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Cabe Ijo', 'price' => 30000, 'desc' => 'Sambal cabe ijo khas.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-14.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Kari', 'price' => 30000, 'desc' => 'Bumbu kari khas.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-15.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Mix', 'price' => 36000, 'desc' => 'Campuran ayam dan sapi.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-16.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Jumbo', 'price' => 40000, 'desc' => 'Ukuran besar untuk sharing.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-17.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Mini', 'price' => 18000, 'desc' => 'Porsi kecil.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-18.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Special 4 Telur', 'price' => 42000, 'desc' => 'Isi 4 telur full daging.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-19.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Super Daging', 'price' => 45000, 'desc' => 'Extra full daging premium.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-20.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Ayam Keju', 'price' => 30000, 'desc' => 'Ayam dan keju.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-21.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Sapi Keju', 'price' => 34000, 'desc' => 'Sapi dan keju leleh.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-22.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Ayam Jumbo', 'price' => 35000, 'desc' => 'Ayam ukuran jumbo.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-23.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Sapi Jumbo', 'price' => 40000, 'desc' => 'Sapi ukuran jumbo.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-24.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Ayam Spesial', 'price' => 30000, 'desc' => 'Ayam lebih banyak.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-25.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Sapi Spesial', 'price' => 35000, 'desc' => 'Sapi lebih banyak.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-26.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Sambal Matah', 'price' => 32000, 'desc' => 'Dengan sambal matah.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-27.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Daging Asap', 'price' => 35000, 'desc' => 'Isian daging asap.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-28.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Ayam Crispy', 'price' => 32000, 'desc' => 'Ayam crispy potong.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-29.jpg'],
            ['cat_id' => $catTelur->id, 'name' => 'Martabak Telur Komplit', 'price' => 48000, 'desc' => 'Isi lengkap premium.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/telur-30.jpg'],


            // ===============================
            // ===== MINUMAN SEGAR (30) ======
            // ===============================

            ['cat_id' => $catMinum->id, 'name' => 'Teh Tarik Hangat', 'price' => 8000, 'desc' => 'Teh susu khas berbusa.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-01.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Teh Tarik Dingin', 'price' => 10000, 'desc' => 'Versi es menyegarkan.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-02.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Teh Manis', 'price' => 6000, 'desc' => 'Teh manis segar.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-03.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Jeruk', 'price' => 10000, 'desc' => 'Perasan jeruk asli.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-04.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Jeruk Hangat', 'price' => 9000, 'desc' => 'Jeruk hangat menyegarkan.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-05.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Lemon Tea', 'price' => 12000, 'desc' => 'Teh lemon segar.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-06.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Thai Tea', 'price' => 15000, 'desc' => 'Thai tea creamy.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-07.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Green Tea Latte', 'price' => 15000, 'desc' => 'Matcha latte segar.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-08.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Kopi Hitam', 'price' => 10000, 'desc' => 'Kopi hitam dingin.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-09.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Kopi Susu', 'price' => 15000, 'desc' => 'Kopi susu creamy.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-10.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Kopi Susu Gula Aren', 'price' => 18000, 'desc' => 'Kopi dengan gula aren.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-11.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Cappuccino Dingin', 'price' => 17000, 'desc' => 'Cappuccino ice.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-12.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Chocolate Latte', 'price' => 15000, 'desc' => 'Cokelat creamy.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-13.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Milo Dingin', 'price' => 14000, 'desc' => 'Milo segar.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-14.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Susu Cokelat', 'price' => 12000, 'desc' => 'Susu cokelat manis.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-15.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Susu Putih Hangat', 'price' => 10000, 'desc' => 'Susu hangat.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-16.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Sirup Cocopandan', 'price' => 9000, 'desc' => 'Sirup segar.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-17.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Sirup Melon', 'price' => 9000, 'desc' => 'Sirup melon dingin.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-18.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Campur', 'price' => 15000, 'desc' => 'Campuran buah dan sirup.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-19.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Teler', 'price' => 18000, 'desc' => 'Alpukat, kelapa, nangka.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-20.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Es Buah', 'price' => 15000, 'desc' => 'Buah segar campur.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-21.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Air Mineral', 'price' => 5000, 'desc' => 'Air mineral botol.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-22.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Soda Gembira', 'price' => 15000, 'desc' => 'Soda dengan susu.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-23.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Sparkling Lemon', 'price' => 15000, 'desc' => 'Soda lemon segar.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-24.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Teh Rosella', 'price' => 12000, 'desc' => 'Teh rosella sehat.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-25.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Wedang Jahe', 'price' => 12000, 'desc' => 'Jahe hangat.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-26.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Bandrek Susu', 'price' => 15000, 'desc' => 'Bandrek hangat susu.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-27.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Jus Alpukat', 'price' => 18000, 'desc' => 'Alpukat creamy.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-28.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Jus Mangga', 'price' => 17000, 'desc' => 'Mangga segar.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-29.jpg'],
            ['cat_id' => $catMinum->id, 'name' => 'Jus Strawberry', 'price' => 17000, 'desc' => 'Strawberry fresh.', 'type' => 'single', 'options' => [], 'img' => 'products/martabak-hening/minum-30.jpg'],

        ];

        // 5. Loop Insert Produk
        $orderColumn = 1;
        foreach ($menuData as $item) {
            $product = Product::create([
                'restaurant_id' => $restaurant->id,
                'category_id' => $item['cat_id'],
                'name' => $item['name'],
                'description' => $item['desc'],
                'price' => $item['price'],
                'image' => $item['img'],
                'type' => $item['type'],
                'order_column' => $orderColumn++,
                'is_available' => true,
            ]);

            // Insert Opsi jika ada
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

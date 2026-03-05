<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin Default
        $user = User::factory()->create([
            'name' => 'Admin Rekap',
            'email' => 'yuliadesuryanii@gmail.com',
            'password' => bcrypt('adekManis'), // Password: password
        ]);

//        // 2. Buat Dummy Publishers (dikaitkan dengan user admin)
//        $publisherNames = ['DETIK', 'KOMPAS', 'CNN INDONESIA', 'DERAKPOST', 'RIAUPOS', 'HALLORIAU'];
//
//        foreach ($publisherNames as $name) {
//            $publisher = Publisher::create([
//                'name' => $name,
//                'user_id' => $user->id, // Kaitkan dengan user
//            ]);
//
//            // 3. Buat Dummy Articles untuk bulan ini
//            for ($i = 0; $i < rand(5, 15); $i++) {
//                // Generate URL unik dummy
//                $url = 'https://example.com/' . strtolower($name) . '/berita-' . $i . '-' . uniqid();
//
//                Article::create([
//                    'publisher_id' => $publisher->id,
//                    'url' => $url,
//                    'published_at' => now()->startOfMonth()->addDays(rand(0, now()->day - 1)),
//                ]);
//            }
//        }
    }
}

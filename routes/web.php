<?php

use App\Http\Controllers\MenuController;
use App\Livewire\Dashboard;
use App\Livewire\Menu\MenuIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome')->name('welcome');

// --- Rute untuk Dashboard Admin dan Rich Preview ---
// Ini akan menangani domain seperti: subdomain.ngopikode.my.id
Route::domain('{subdomain}.' . config('app.frontend_url_base'))
    ->middleware('validateSubdomain')
    ->group(function () {
        // --- Rute untuk Rich Preview ---
        // Ini akan menangani URL seperti: /menu/1
        Route::get('/menu/{productId}', [MenuController::class, 'showProductPreview'])->name('product.preview');

        // Halaman Preview Story (HTML)
        Route::get('/menu/{productId}/story', [MenuController::class, 'shareAsStory'])->name('product.story');

        // Generate Gambar Story (JPEG)
        Route::get('/menu/{productId}/story/image', [MenuController::class, 'generateStoryImage'])->name('product.story.image');
    });

// --- Rute untuk Dashboard Admin ---
Route::prefix('dashboard')->group(function () {

    Route::middleware('auth:web')
        ->group(function () {
            // Dashboard
            Route::get('/', Dashboard::class)->name('dashboard');

            // Menu Management
            Route::get('menu', MenuIndex::class)->name('menu.index');

            // Order Management
            Route::get('/orders', \App\Livewire\Orders\Index::class)->name('orders.index');

            // Settings
            Route::get('/settings', \App\Livewire\Settings\Index::class)->name('settings.index');

            // Profile
            Route::view('profile', 'profile')->name('profile');

            // Include auth routes within the subdomain group
        });

    require __DIR__ . '/auth.php';
});

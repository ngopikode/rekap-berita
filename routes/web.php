<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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


Route::middleware(['auth'])->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    // Menggunakan view dashboard.blade.php yang memanggil komponen livewire
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/publishers', 'publisher-management')->name('publishers.index');
    Route::view('/profile', 'profile')->name('profile');
});

require __DIR__ . '/auth.php';

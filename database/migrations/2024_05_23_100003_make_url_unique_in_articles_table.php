<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Tambahkan kolom hash untuk index unik
            $table->string('url_hash', 32)->after('url')->nullable();
        });

        // Jika ada data lama (untuk kasus non-fresh), isi hash-nya
        // DB::statement("UPDATE articles SET url_hash = MD5(url)");

        Schema::table('articles', function (Blueprint $table) {
            // Pastikan tidak null dan buat unique
            // Kita biarkan nullable dulu di atas biar tidak error saat add column
            // Tapi karena ini migrate:fresh, tabel kosong, jadi aman.
            $table->unique('url_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropUnique(['url_hash']);
            $table->dropColumn('url_hash');
        });
    }
};

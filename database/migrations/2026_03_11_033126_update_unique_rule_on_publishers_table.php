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
        Schema::table('publishers', function (Blueprint $table) {
            // 1. Hapus aturan unik yang lama (yang bikin error)
            // Catatan: 'publishers_name_unique' adalah nama index dari error log-mu
            $table->dropUnique('publishers_name_unique');

            // 2. Buat aturan unik baru (Kombinasi user_id dan name)
            $table->unique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publishers', function (Blueprint $table) {
            // Kembalikan ke asal jika di-rollback
            $table->dropUnique(['user_id', 'name']);
            $table->unique('name');
        });
    }
};

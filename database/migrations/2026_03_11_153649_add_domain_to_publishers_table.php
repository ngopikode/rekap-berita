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
        // TAHAP 1: Tambahkan kolom (Cek dulu takutnya migrasi tadi macet di tengah jalan dan kolomnya sudah terbuat)
        Schema::table('publishers', function (Blueprint $table) {
            if (!Schema::hasColumn('publishers', 'domain')) {
                $table->string('domain')->nullable()->after('name');
            }
        });

        // TAHAP 2: Buat aturan unik BARU DULU, biar Foreign Key (user_id) pindah pegangan ke sini
        Schema::table('publishers', function (Blueprint $table) {
            // Kita beri nama index spesifik agar rapi
            $table->unique(['user_id', 'domain'], 'publishers_user_id_domain_unique');
        });

        // TAHAP 3: Setelah MySQL tenang punya pegangan baru, baru kita hapus yang lama!
        Schema::table('publishers', function (Blueprint $table) {
            $table->dropUnique('publishers_user_id_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publishers', function (Blueprint $table) {
            // Kembalikan seperti semula jika di-rollback
            $table->unique(['user_id', 'name'], 'publishers_user_id_name_unique');
        });

        Schema::table('publishers', function (Blueprint $table) {
            $table->dropUnique('publishers_user_id_domain_unique');
            $table->dropColumn('domain');
        });
    }
};

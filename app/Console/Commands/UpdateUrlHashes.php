<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class UpdateUrlHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:url-hashes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbarui url_hash lama menjadi format baru (md5(url + user_id))';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembaruan url_hash...');

        // Ambil semua artikel beserta data publisher-nya
        $articles = Article::with('publisher')->get();

        // Buat loading bar di terminal
        $bar = $this->output->createProgressBar(count($articles));

        $updatedCount = 0;
        $deletedCount = 0;

        foreach ($articles as $article) {
            // Jika artikel tidak punya publisher (data rusak), lewati
            if (!$article->publisher) {
                $bar->advance();
                continue;
            }

            $userId = $article->publisher->user_id;
            $newHash = md5($article->url . '_' . $userId); // Resep Hash Baru

            // Jika hash di database belum pakai format baru
            if ($article->url_hash !== $newHash) {
                try {
                    $article->update(['url_hash' => $newHash]);
                    $updatedCount++;
                } catch (\Exception $e) {
                    // Jika error (karena user tersebut sudah punya URL ini), hapus saja duplikatnya
                    $article->delete();
                    $deletedCount++;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Selesai! Berhasil mengupdate {$updatedCount} artikel.");
        if ($deletedCount > 0) {
            $this->warn("Menghapus {$deletedCount} artikel duplikat dari user yang sama.");
        }
    }
}

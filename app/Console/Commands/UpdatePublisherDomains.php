<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Publisher;
use Illuminate\Console\Command;

class UpdatePublisherDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:publisher-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengisi kolom domain yang kosong pada tabel publishers dan menggabungkan duplikat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembaruan domain untuk publisher lama...');

        // Ambil semua publisher yang domain-nya masih kosong atau null
        $publishers = Publisher::whereNull('domain')->orWhere('domain', '')->get();

        if ($publishers->isEmpty()) {
            $this->info('Semua publisher sudah memiliki domain. Tidak ada yang perlu diupdate!');
            return;
        }

        // Buat progress bar biar kelihatan keren di terminal
        $bar = $this->output->createProgressBar(count($publishers));
        $bar->start();

        $updatedCount = 0;
        $mergedCount = 0;

        foreach ($publishers as $pub) {
            // Ambil satu artikel pertama milik publisher ini sebagai acuan domain
            $article = Article::where('publisher_id', $pub->id)->first();

            $domain = null;

            if ($article) {
                // Ekstrak domain murni dari URL artikelnya
                $host = parse_url($article->url, PHP_URL_HOST);
                if ($host) {
                    $domain = str_replace('www.', '', strtolower($host));
                }
            }

            // Jika dia anehnya tidak punya artikel sama sekali, buatkan domain fallback dari namanya agar tidak error unique
            if (!$domain) {
                $domain = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pub->name)) . '.unknown';
            }

            // CEK DUPLIKAT: Apakah user ini sudah punya publisher lain dengan domain yang sama?
            $existingPublisher = Publisher::where('user_id', $pub->user_id)
                ->where('domain', $domain)
                ->where('id', '!=', $pub->id)
                ->first();

            if ($existingPublisher) {
                // JIKA ADA DUPLIKAT: Pindahkan semua artikel ke publisher utama, lalu hapus publisher ini
                Article::where('publisher_id', $pub->id)->update(['publisher_id' => $existingPublisher->id]);
                $pub->delete();
                $mergedCount++;
            } else {
                // JIKA AMAN: Update domain-nya
                $pub->update(['domain' => $domain]);
                $updatedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2); // Kasih jarak 2 baris setelah progress bar selesai

        // Tampilkan hasil laporannya
        $this->info("Selesai! Berhasil mengupdate {$updatedCount} domain media.");
        if ($mergedCount > 0) {
            $this->warn("Telah menggabungkan dan membersihkan {$mergedCount} media yang duplikat (domain sama).");
        }
    }
}

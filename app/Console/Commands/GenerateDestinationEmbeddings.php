<?php

namespace App\Console\Commands;

use App\Models\Destinasi;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;

class GenerateDestinationEmbeddings extends Command
{
    protected $signature   = 'wayway:embed {--force : Re-embed semua destinasi meski sudah ada embedding}';
    protected $description = 'Generate atau update embedding vector untuk semua destinasi aktif';

    public function __construct(private EmbeddingService $embeddingService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = Destinasi::where('status', 'active')
            ->with('kategori');

        if (! $this->option('force')) {
            $query->whereNull('embedded_at');
        }

        $destinasi = $query->get();

        if ($destinasi->isEmpty()) {
            $this->info('Semua destinasi sudah memiliki embedding. Gunakan --force untuk re-embed.');
            return self::SUCCESS;
        }

        $this->info("Memproses {$destinasi->count()} destinasi...");
        $bar = $this->output->createProgressBar($destinasi->count());
        $bar->start();

        $sukses = 0;
        $gagal  = 0;

        foreach ($destinasi as $dest) {
            try {
                $this->embeddingService->embedDestinasi($dest);
                $sukses++;
            } catch (\Exception $e) {
                $gagal++;
                $this->newLine();
                $this->error("Gagal embed '{$dest->nama_destinasi}': {$e->getMessage()}");
            }

            $bar->advance();
            // Jeda kecil agar tidak kena rate limit OpenAI
            usleep(200_000); // 200ms
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Selesai! Sukses: {$sukses} | Gagal: {$gagal}");

        return self::SUCCESS;
    }
}
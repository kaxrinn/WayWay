<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaketPromosi;
use Illuminate\Support\Facades\DB;

class PaketPromosiSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK checks (IMPORTANT)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PaketPromosi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Basic Package - FREE
        PaketPromosi::create([
            'nama_paket' => 'Basic',
            'deskripsi' => 'Paket gratis untuk memulai - Otomatis aktif saat registrasi',
            'harga' => 0,
            'durasi_hari' => 0,
            'fitur' => 'Terdaftar di WayWay,Muncul di pencarian,Halaman detail destinasi,Edit melalui request admin',
            'status' => 'active',
            'max_destinasi' => 1,
            'max_foto' => 3,
            'max_video' => 0,
            'priority_level' => 1,
            'can_edit_foto' => false,
            'is_featured_allowed' => false,
        ]);

        // Standard Package
        PaketPromosi::create([
            'nama_paket' => 'Standard',
            'deskripsi' => 'Upgrade fleksibel untuk pemilik wisata kecil-menengah',
            'harga' => 49000,
            'durasi_hari' => 30,
            'fitur' => 'Edit & hapus foto sendiri,Update info kapan saja,Posisi lebih tinggi di pencarian,Bebas update destinasi',
            'status' => 'active',
            'max_destinasi' => 3,
            'max_foto' => 8,
            'max_video' => 0,
            'priority_level' => 2,
            'can_edit_foto' => true,
            'is_featured_allowed' => false,
        ]);

        // Premium Package
        PaketPromosi::create([
            'nama_paket' => 'Premium',
            'deskripsi' => 'Promosi & exposure maksimal untuk wisata unggulan',
            'harga' => 149000,
            'durasi_hari' => 30,
            'fitur' => 'Featured di homepage,Banner promosi khusus,Prioritas AI Recommendation,Prioritas AI Itinerary,Statistik performa lengkap,Label Rekomendasi WayWay',
            'status' => 'active',
            'max_destinasi' => 10,
            'max_foto' => 20,
            'max_video' => 20,
            'priority_level' => 3,
            'can_edit_foto' => true,
            'is_featured_allowed' => true,
        ]);

        $this->command->info('✅ Paket Promosi berhasil di-seed!');
    }
}

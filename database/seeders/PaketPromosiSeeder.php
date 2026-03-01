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
            'deskripsi' => 'Free package to get started - Automatically activated upon registration',
            'harga' => 0,
            'durasi_hari' => 0,
            'fitur' => 'Listed on WayWay,Appears in search results,Destination detail page,Edit via admin request',
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
            'deskripsi' => 'Flexible upgrade for small to medium tourism owners',
            'harga' => 49000,
            'durasi_hari' => 30,
            'fitur' => 'Edit & delete photos directly,Update info anytime,Higher position in search results,Unlimited destination updates',
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
            'deskripsi' => 'Maximum promotion & exposure for top destinations',
            'harga' => 149000,
            'durasi_hari' => 30,
            'fitur' => 'Featured on homepage,Exclusive promotion banner,AI Recommendation priority,AI Itinerary priority,Full performance statistics,WayWay Recommended label',
            'status' => 'active',
            'max_destinasi' => 10,
            'max_foto' => 20,
            'max_video' => 20,
            'priority_level' => 3,
            'can_edit_foto' => true,
            'is_featured_allowed' => true,
        ]);

        $this->command->info('✅ Paket Promosi seeded successfully!');
    }
}
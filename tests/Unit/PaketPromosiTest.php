<?php

namespace Tests\Unit;

use App\Models\PaketPromosi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaketPromosiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function basic_package_is_free()
    {
        $basic = PaketPromosi::create([
            'nama_paket'          => 'Basic',
            'harga'               => 0,
            'durasi_hari'         => 0,
            'max_destinasi'       => 1,
            'max_foto'            => 3,
            'max_video'           => 0,
            'priority_level'      => 1,
            'can_edit_foto'       => false,
            'is_featured_allowed' => false,
            'status'              => 'active',
        ]);

        $this->assertEquals(0, $basic->harga);
        $this->assertFalse((bool) $basic->can_edit_foto);
        $this->assertFalse((bool) $basic->is_featured_allowed);
    }

    /** @test */
    public function standard_package_has_correct_limits()
    {
        $standard = PaketPromosi::create([
            'nama_paket'          => 'Standard',
            'harga'               => 49000,
            'durasi_hari'         => 30,
            'max_destinasi'       => 3,
            'max_foto'            => 8,
            'max_video'           => 0,
            'priority_level'      => 2,
            'can_edit_foto'       => true,
            'is_featured_allowed' => false,
            'status'              => 'active',
        ]);

        $this->assertEquals(49000, $standard->harga);
        $this->assertEquals(3, $standard->max_destinasi);
        $this->assertEquals(8, $standard->max_foto);
        $this->assertTrue((bool) $standard->can_edit_foto);
    }

    /** @test */
    public function premium_package_allows_featured()
    {
        $premium = PaketPromosi::create([
            'nama_paket'          => 'Premium',
            'harga'               => 149000,
            'durasi_hari'         => 30,
            'max_destinasi'       => 10,
            'max_foto'            => 20,
            'max_video'           => 20,
            'priority_level'      => 3,
            'can_edit_foto'       => true,
            'is_featured_allowed' => true,
            'status'              => 'active',
        ]);

        $this->assertTrue((bool) $premium->is_featured_allowed);
        $this->assertEquals(3, $premium->priority_level);
        $this->assertEquals(20, $premium->max_video);
    }

    /** @test */
    public function user_get_paket_limits_returns_correct_values()
    {
        $basic = PaketPromosi::create([
            'nama_paket' => 'Basic', 'harga' => 0, 'durasi_hari' => 0,
            'max_destinasi' => 1, 'max_foto' => 3, 'max_video' => 0,
            'priority_level' => 1, 'can_edit_foto' => false,
            'is_featured_allowed' => false, 'status' => 'active',
        ]);

        $user   = User::factory()->create(['current_paket_id' => $basic->id]);
        $limits = $user->getPaketLimits();

        $this->assertEquals(1, $limits['max_destinasi']);
        $this->assertEquals(3, $limits['max_foto']);
        $this->assertFalse($limits['can_edit_foto']);
    }

    /** @test */
    public function transaksi_promosi_order_id_format_is_correct()
    {
        $userId    = 42;
        $timestamp = 1234567890;
        $orderId   = "TRX-{$userId}-{$timestamp}";

        $this->assertMatchesRegularExpression('/^TRX-\d+-\d+$/', $orderId);
    }
}

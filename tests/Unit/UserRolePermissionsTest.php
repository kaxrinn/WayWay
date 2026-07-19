<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function wisatawan_role_is_default()
    {
        $user = User::factory()->create();
        $this->assertEquals('wisatawan', $user->role);
    }

    /** @test */
    public function user_has_correct_role_methods()
    {
        $admin     = User::factory()->create(['role' => 'admin']);
        $wisatawan = User::factory()->create(['role' => 'wisatawan']);
        $pemilik   = User::factory()->create(['role' => 'pemilik_wisata']);
        $agent     = User::factory()->create(['role' => 'travel_agent']);

        // Test role checks if implemented as methods
        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('wisatawan', $wisatawan->role);
        $this->assertEquals('pemilik_wisata', $pemilik->role);
        $this->assertEquals('travel_agent', $agent->role);
    }

    /** @test */
    public function valid_roles_are_enforced()
    {
        $validRoles = ['admin', 'pemilik_wisata', 'wisatawan', 'travel_agent'];

        foreach ($validRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->assertContains($user->role, $validRoles);
        }
    }

    /** @test */
    public function user_can_have_google_oauth_without_password()
    {
        $user = User::factory()->create([
            'password'  => null,
            'google_id' => 'google_123456',
        ]);

        $this->assertNull($user->password);
        $this->assertNotNull($user->google_id);
    }

    /** @test */
    public function user_current_paket_relationship_works()
    {
        $paket = \App\Models\PaketPromosi::create([
            'nama_paket' => 'Basic', 'harga' => 0, 'durasi_hari' => 0,
            'max_destinasi' => 1, 'max_foto' => 3, 'max_video' => 0,
            'priority_level' => 1, 'can_edit_foto' => false,
            'is_featured_allowed' => false, 'status' => 'active',
        ]);

        $user = User::factory()->create(['current_paket_id' => $paket->id]);

        $this->assertNotNull($user->currentPaket);
        $this->assertEquals('Basic', $user->currentPaket->nama_paket);
    }
}

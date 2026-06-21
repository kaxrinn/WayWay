<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\Favorit;
use Tests\TestCase;
use Tests\WithoutRefresh;
use PHPUnit\Framework\Attributes\Test;

class ApiTest extends TestCase
{
    use WithoutRefresh;

    protected User $wisatawan;
    protected Destinasi $destinasi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wisatawan = User::factory()->create([
            'email' => 'wisatawan_api_' . uniqid() . '@test.com',
            'role' => 'wisatawan',
        ]);

        $kategori = Kategori::create(['nama_kategori' => 'Wisata Alam API']);
        
        $pemilik = User::factory()->create([
            'email' => 'pemilik_api_' . uniqid() . '@test.com',
            'role' => 'pemilik_wisata'
        ]);

        $this->destinasi = Destinasi::create([
            'nama_destinasi' => 'Destinasi API Test',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Testing API',
            'harga' => 50000,
            'kategori_id' => $kategori->id,
            'user_id' => $pemilik->id,
            'status' => 'active',
        ]);
    }

    #[Test]
    public function wisatawan_can_view_profile()
    {
        $this->actingAs($this->wisatawan);
        
        $response = $this->get('/wisatawan/profil');
        
        $response->assertStatus(200);
    }

    #[Test]
    public function wisatawan_can_update_profile()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->put('/wisatawan/profile', [
            'name' => 'Nama Baru API',
            'email' => $this->wisatawan->email, // Wajib dikirim menurut validasi
            'no_telepon' => '08123456789',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->wisatawan->id,
            'name' => 'Nama Baru API',
            'no_telepon' => '08123456789',
        ]);
    }

    #[Test]
    public function wisatawan_can_toggle_favorit()
    {
        $this->actingAs($this->wisatawan);

        // Tambah ke favorit
        $response = $this->postJson('/favorit/toggle', [
            'destinasi_id' => $this->destinasi->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'added']);
        $this->assertDatabaseHas('favorit', [
            'user_id' => $this->wisatawan->id,
            'destinasi_id' => $this->destinasi->id
        ]);

        // Hapus dari favorit (toggle lagi)
        $response2 = $this->postJson('/favorit/toggle', [
            'destinasi_id' => $this->destinasi->id
        ]);

        $response2->assertStatus(200);
        $response2->assertJson(['status' => 'removed']);
        $this->assertDatabaseMissing('favorit', [
            'user_id' => $this->wisatawan->id,
            'destinasi_id' => $this->destinasi->id
        ]);
    }

    #[Test]
    public function wisatawan_can_view_favorit_list()
    {
        $this->actingAs($this->wisatawan);
        
        Favorit::create([
            'user_id' => $this->wisatawan->id,
            'destinasi_id' => $this->destinasi->id
        ]);

        $response = $this->get('/wisatawan/favorit');
        
        $response->assertStatus(200);
        $response->assertSee('Destinasi API Test');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\PaketPromosi;
use Tests\WithoutRefresh;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestinasiTest extends TestCase
{
    use WithoutRefresh;

    protected User $pemilik;
    protected User $pemilikStandard;
    protected Kategori $kategori;
    protected PaketPromosi $basicPaket;
    protected PaketPromosi $standardPaket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basicPaket = PaketPromosi::create([
            'nama_paket' => 'Basic',
            'deskripsi' => 'Free package',
            'harga' => 0,
            'durasi_hari' => 0,
            'fitur' => 'Listed on WayWay',
            'status' => 'active',
            'max_destinasi' => 1,
            'max_foto' => 3,
            'max_video' => 0,
            'priority_level' => 1,
            'can_edit_foto' => false,
            'is_featured_allowed' => false,
        ]);

        $this->standardPaket = PaketPromosi::create([
            'nama_paket' => 'Standard',
            'deskripsi' => 'Standard package',
            'harga' => 49000,
            'durasi_hari' => 30,
            'fitur' => 'Edit photos',
            'status' => 'active',
            'max_destinasi' => 3,
            'max_foto' => 8,
            'max_video' => 0,
            'priority_level' => 2,
            'can_edit_foto' => true,
            'is_featured_allowed' => false,
        ]);

        $this->kategori = Kategori::create(['nama_kategori' => 'Pantai']);

        $this->pemilik = User::factory()->create([
            'role' => 'pemilik_wisata',
            'current_paket_id' => $this->basicPaket->id,
        ]);

        $this->pemilikStandard = User::factory()->create([
            'role' => 'pemilik_wisata',
        ]);
        $this->pemilikStandard->update([
            'current_paket_id' => $this->standardPaket->id,
        ]);
    }

    #[Test]
    public function public_can_view_destinasi_list()
    {
        $response = $this->get('/destinasi');
        $response->assertStatus(200);
    }

    #[Test]
    public function public_can_search_destinasi()
    {
        Destinasi::create([
            'nama_destinasi' => 'Pantai Nongsa',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Pantai indah',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->pemilik->id,
            'status' => 'active',
        ]);

        $response = $this->get('/destinasi?q=Nongsa');
        $response->assertStatus(200);
        $response->assertSee('Pantai Nongsa');
    }

    #[Test]
    public function public_can_view_destinasi_detail()
    {
        $destinasi = Destinasi::create([
            'nama_destinasi' => 'Pantai Nongsa',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Pantai indah di Batam',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->pemilik->id,
            'status' => 'active',
        ]);

        $response = $this->get("/destinasi/{$destinasi->id}");
        $response->assertStatus(200);
        $response->assertSee('Pantai Nongsa');
    }

    #[Test]
    public function destinasi_detail_returns_404_for_nonexistent()
    {
        $response = $this->get('/destinasi/99999');
        $response->assertStatus(404);
    }

    #[Test]
    public function pemilik_can_create_destinasi_within_limit()
    {
        $this->actingAs($this->pemilik);

        $response = $this->post('/pemilik/destinasi', [
            'nama_destinasi' => 'Pantai Nongsa',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Pantai indah di Batam',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
        ]);

        $response->assertRedirect('/pemilik/destinasi');
        $this->assertDatabaseHas('destinasi', [
            'nama_destinasi' => 'Pantai Nongsa',
            'user_id' => $this->pemilik->id,
            'status' => 'active',
        ]);
    }

    #[Test]
    public function pemilik_cannot_exceed_destinasi_limit()
    {
        $this->actingAs($this->pemilik);

        // Create 1 destinasi (Basic limit)
        Destinasi::create([
            'nama_destinasi' => 'Existing Destinasi',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Existing',
            'harga' => 10000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->pemilik->id,
            'status' => 'active',
        ]);

        // Try to create another (should fail)
        $response = $this->get('/pemilik/destinasi/create');
        $response->assertRedirect('/pemilik/destinasi');
        $response->assertSessionHas('error');
    }

    #[Test]
    public function pemilik_with_standard_package_can_create_multiple_destinasi()
    {
        $this->actingAs($this->pemilikStandard);

        // Create 3 destinasi (Standard limit)
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->post('/pemilik/destinasi', [
                'nama_destinasi' => "Destinasi Test $i",
                'latitude' => 1.15 + ($i * 0.01),
                'longitude' => 104.12,
                'deskripsi' => "Deskripsi $i",
                'harga' => 25000,
                'kategori_id' => $this->kategori->id,
            ]);

            $response->assertRedirect('/pemilik/destinasi');
        }

        $this->assertEquals(3, Destinasi::where('user_id', $this->pemilikStandard->id)->count());
    }

    #[Test]
    public function pemilik_basic_cannot_edit_destinasi_directly()
    {
        $this->actingAs($this->pemilik);

        $destinasi = Destinasi::create([
            'nama_destinasi' => 'Test Destinasi',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Test',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->pemilik->id,
            'status' => 'active',
        ]);

        $response = $this->get("/pemilik/destinasi/{$destinasi->id}/edit");
        // Should redirect to edit-request
        $response->assertRedirect();
    }

    #[Test]
    public function pemilik_standard_can_edit_destinasi_directly()
    {
        $this->actingAs($this->pemilikStandard);

        $destinasi = Destinasi::create([
            'nama_destinasi' => 'Test Destinasi',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Test',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->pemilikStandard->id,
            'status' => 'active',
        ]);

        $response = $this->get("/pemilik/destinasi/{$destinasi->id}/edit");
        $response->assertStatus(200);
    }

    #[Test]
    public function pemilik_cannot_edit_other_users_destinasi()
    {
        $otherPemilik = User::factory()->create(['role' => 'pemilik_wisata']);
        $destinasi = Destinasi::create([
            'nama_destinasi' => 'Other Destinasi',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Other',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $otherPemilik->id,
            'status' => 'active',
        ]);

        $this->actingAs($this->pemilik);
        $response = $this->get("/pemilik/destinasi/{$destinasi->id}/edit");
        $response->assertStatus(404);
    }

    #[Test]
    public function pemilik_delete_destinasi_sets_status_inactive()
    {
        $this->actingAs($this->pemilikStandard);

        $destinasi = Destinasi::create([
            'nama_destinasi' => 'To Delete',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Will be deleted',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->pemilikStandard->id,
            'status' => 'active',
        ]);

        $response = $this->delete("/pemilik/destinasi/{$destinasi->id}");
        $response->assertRedirect('/pemilik/destinasi');

        $this->assertDatabaseHas('destinasi', [
            'id' => $destinasi->id,
            'status' => 'inactive',
        ]);
    }

    #[Test]
    public function api_returns_destinasi_by_kategori()
    {
        Destinasi::create([
            'nama_destinasi' => 'Pantai Test',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Test',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->pemilik->id,
            'status' => 'active',
        ]);

        $response = $this->getJson("/api/destinasi/kategori/{$this->kategori->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }

    #[Test]
    public function unauthenticated_cannot_create_destinasi()
    {
        $response = $this->post('/pemilik/destinasi', [
            'nama_destinasi' => 'Test',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Test',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
        ]);

        $response->assertRedirect('/login');
    }
}

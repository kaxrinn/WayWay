<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\PaketPromosi;
use Tests\WithoutRefresh;
use Tests\TestCase;

class SystemTest extends TestCase
{
    use WithoutRefresh;

    protected Kategori $kategori;
    protected PaketPromosi $basicPaket;
    protected PaketPromosi $standardPaket;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed necessary reference data for e2e workflow
        $this->kategori = Kategori::firstOrCreate(['nama_kategori' => 'Pantai']);

        $this->basicPaket = PaketPromosi::firstOrCreate(
            ['nama_paket' => 'Basic'],
            [
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
            ]
        );

        $this->standardPaket = PaketPromosi::firstOrCreate(
            ['nama_paket' => 'Standard'],
            [
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
            ]
        );
    }

    /** @test */
    public function tourist_complete_journey_e2e()
    {
        // 1. Tourist Registration
        $email = 'tourist_e2e_' . uniqid() . '@test.com';
        $registerResponse = $this->post('/register', [
            'name' => 'Siti Rahayu',
            'email' => $email,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);
        $registerResponse->assertRedirect('/login');

        // Retrieve created user
        $tourist = User::where('email', $email)->first();
        $this->assertNotNull($tourist);
        $this->assertEquals('wisatawan', $tourist->role);

        // Authenticate as Tourist
        $this->actingAs($tourist);

        // Create a test destinasi for search & detail page
        $pemilik = User::factory()->create(['role' => 'pemilik_wisata']);
        $destinasi = Destinasi::create([
            'nama_destinasi' => 'Pantai Nongsa E2E',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Pantai indah E2E',
            'harga' => 25000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $pemilik->id,
            'status' => 'active',
        ]);

        // 2. View homepage / beranda
        $berandaResponse = $this->get('/wisatawan/beranda');
        $berandaResponse->assertStatus(200);

        // 3. Search for Pantai
        $searchResponse = $this->get('/destinasi?q=Nongsa');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Pantai Nongsa E2E');

        // 4. View Detail Destinasi
        $detailResponse = $this->get("/destinasi/{$destinasi->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Pantai Nongsa E2E');

        // 5. Add to Favorites
        $favResponse = $this->postJson('/favorit/toggle', [
            'destinasi_id' => $destinasi->id,
        ]);
        $favResponse->assertStatus(200);
        $this->assertDatabaseHas('favorit', [
            'user_id' => $tourist->id,
            'destinasi_id' => $destinasi->id,
        ]);

        // 6. Submit a review
        $reviewResponse = $this->post('/ulasan', [
            'destinasi_id' => $destinasi->id,
            'rating' => 5,
            'komentar' => 'Sangat mengesankan!',
        ]);
        $reviewResponse->assertRedirect();
        $this->assertDatabaseHas('ulasan', [
            'destinasi_id' => $destinasi->id,
            'user_id' => $tourist->id,
            'rating' => 5,
            'komentar' => 'Sangat mengesankan!',
        ]);

        // 7. Access Itinerary AI page
        $itineraryPage = $this->get('/itinerary');
        $itineraryPage->assertStatus(200);
    }

    /** @test */
    public function pemilik_complete_journey_e2e()
    {
        // 1. Register as Pemilik Wisata
        $email = 'pemilik_e2e_' . uniqid() . '@test.com';
        $registerResponse = $this->post('/register', [
            'name' => 'Budi Pemilik',
            'email' => $email,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'pemilik_wisata', // standard form handles role if present or default middleware
        ]);
        
        // Manual creation if signup registers as wisatawan by default
        $pemilik = User::where('email', $email)->first();
        if ($pemilik) {
            $pemilik->update(['role' => 'pemilik_wisata', 'current_paket_id' => $this->basicPaket->id]);
        } else {
            $pemilik = User::factory()->create([
                'email' => $email,
                'role' => 'pemilik_wisata',
                'current_paket_id' => $this->basicPaket->id,
            ]);
        }

        $this->actingAs($pemilik);

        // 2. View Pemilik Dashboard
        $dashboardResponse = $this->get('/pemilik/dashboard');
        $dashboardResponse->assertStatus(200);

        // 3. Create 1st Destinasi (Basic limit allows this)
        $createResponse = $this->post('/pemilik/destinasi', [
            'nama_destinasi' => 'Destinasi Basic E2E',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Testing Basic E2E',
            'harga' => 10000,
            'kategori_id' => $this->kategori->id,
        ]);
        $createResponse->assertRedirect('/pemilik/destinasi');
        $this->assertDatabaseHas('destinasi', [
            'nama_destinasi' => 'Destinasi Basic E2E',
            'user_id' => $pemilik->id,
        ]);

        // 4. Attempt to create 2nd destinasi (fails/redirects due to Basic package limit)
        $createSecondResponse = $this->get('/pemilik/destinasi/create');
        $createSecondResponse->assertRedirect('/pemilik/destinasi');
        $createSecondResponse->assertSessionHas('error');

        // 5. Upgrade package to Standard
        $pemilik->update(['current_paket_id' => $this->standardPaket->id]);
        $this->actingAs($pemilik->fresh());

        // 6. Create 2nd Destinasi (now allowed under Standard limits)
        $createStandardResponse = $this->post('/pemilik/destinasi', [
            'nama_destinasi' => 'Destinasi Standard E2E',
            'latitude' => 1.16,
            'longitude' => 104.13,
            'deskripsi' => 'Testing Standard E2E',
            'harga' => 20000,
            'kategori_id' => $this->kategori->id,
        ]);
        $createStandardResponse->assertRedirect('/pemilik/destinasi');
        $this->assertDatabaseHas('destinasi', [
            'nama_destinasi' => 'Destinasi Standard E2E',
            'user_id' => $pemilik->id,
        ]);
    }
}

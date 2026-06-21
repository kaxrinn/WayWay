<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\ItineraryHistory;
use Tests\WithoutRefresh;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItineraryTest extends TestCase
{
    use WithoutRefresh;

    protected User $wisatawan;
    protected Kategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wisatawan = User::factory()->create(['role' => 'wisatawan']);
        $this->kategori = Kategori::create(['nama_kategori' => 'Pantai']);

        $pemilik = User::factory()->create(['role' => 'pemilik_wisata']);

        // Create destinations for itinerary generation
        for ($i = 1; $i <= 5; $i++) {
            Destinasi::create([
                'nama_destinasi' => "Destinasi Test $i",
                'latitude' => 1.1296758 + ($i * 0.01),
                'longitude' => 104.0452254 + ($i * 0.01),
                'deskripsi' => "Deskripsi destinasi $i",
                'harga' => 25000 * $i,
                'kategori_id' => $this->kategori->id,
                'user_id' => $pemilik->id,
                'status' => 'active',
            ]);
        }
    }

    #[Test]
    public function itinerary_page_requires_authentication()
    {
        $response = $this->get('/itinerary');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_view_itinerary_page()
    {
        $this->actingAs($this->wisatawan);
        $response = $this->get('/itinerary');
        $response->assertStatus(200);
    }

    #[Test]
    public function itinerary_generation_requires_authentication()
    {
        $response = $this->postJson('/itinerary/generate', [
            'kategori_ids' => [$this->kategori->id],
            'budget' => 100000,
            'companion' => 'keluarga',
            'tanggal' => '2026-12-25',
            'origin_lat' => 1.1296758,
            'origin_lon' => 104.0452254,
            'origin_label' => 'Batam Center',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function itinerary_generation_validates_required_fields()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->postJson('/itinerary/generate', [
            // Missing required fields
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function itinerary_generation_validates_budget_is_positive()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->postJson('/itinerary/generate', [
            'kategori_ids' => [$this->kategori->id],
            'budget' => -1000,
            'companion' => 'keluarga',
            'tanggal' => '2026-12-25',
            'origin_lat' => 1.1296758,
            'origin_lon' => 104.0452254,
            'origin_label' => 'Batam Center',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function itinerary_history_page_loads_for_authenticated_user()
    {
        $this->actingAs($this->wisatawan);
        $response = $this->get('/itinerary/history');
        $response->assertStatus(200);
    }

    #[Test]
    public function itinerary_show_returns_404_for_nonexistent()
    {
        $this->actingAs($this->wisatawan);
        $response = $this->get('/itinerary/show/99999');
        $response->assertStatus(404);
    }

    #[Test]
    public function user_cannot_view_other_users_itinerary()
    {
        $otherUser = User::factory()->create(['role' => 'wisatawan']);
        $history = ItineraryHistory::create([
            'user_id' => $otherUser->id,
            'params' => json_encode(['test' => 'data']),
            'result' => json_encode(['routes' => []]),
            'tanggal_kunjungan' => '2026-12-25',
            'companion' => 'keluarga',
            'origin_label' => 'Batam Center',
        ]);

        $this->actingAs($this->wisatawan);
        $response = $this->get("/itinerary/show/{$history->id}");
        $response->assertStatus(404);
    }
}

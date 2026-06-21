<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\Ulasan;
use Tests\WithoutRefresh;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UlasanTest extends TestCase
{
    use WithoutRefresh;

    protected User $wisatawan;
    protected User $pemilik;
    protected Destinasi $destinasi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wisatawan = User::factory()->create(['role' => 'wisatawan']);
        $this->pemilik = User::factory()->create(['role' => 'pemilik_wisata']);

        $kategori = Kategori::create(['nama_kategori' => 'Pantai']);

        $this->destinasi = Destinasi::create([
            'nama_destinasi' => 'Pantai Test',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Pantai untuk testing',
            'harga' => 25000,
            'kategori_id' => $kategori->id,
            'user_id' => $this->pemilik->id,
            'status' => 'active',
        ]);
    }

    #[Test]
    public function wisatawan_can_submit_review()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->post('/ulasan', [
            'destinasi_id' => $this->destinasi->id,
            'rating' => 5,
            'komentar' => 'Tempat yang sangat indah dan bersih!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ulasan', [
            'destinasi_id' => $this->destinasi->id,
            'user_id' => $this->wisatawan->id,
            'rating' => 5,
            'komentar' => 'Tempat yang sangat indah dan bersih!',
        ]);
    }

    #[Test]
    public function review_requires_authentication()
    {
        $response = $this->post('/ulasan', [
            'destinasi_id' => $this->destinasi->id,
            'rating' => 5,
            'komentar' => 'Test review',
        ]);

        $response->assertRedirect('/login');
    }

    #[Test]
    public function review_requires_valid_rating()
    {
        $this->actingAs($this->wisatawan);

        // Rating out of range
        $response = $this->post('/ulasan', [
            'destinasi_id' => $this->destinasi->id,
            'rating' => 6,
            'komentar' => 'Test',
        ]);

        $response->assertSessionHasErrors('rating');
    }

    #[Test]
    public function review_requires_rating_field()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->post('/ulasan', [
            'destinasi_id' => $this->destinasi->id,
            'komentar' => 'Test without rating',
        ]);

        $response->assertSessionHasErrors('rating');
    }

    #[Test]
    public function review_requires_valid_destinasi()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->post('/ulasan', [
            'destinasi_id' => 99999,
            'rating' => 5,
            'komentar' => 'Test',
        ]);

        $response->assertSessionHasErrors('destinasi_id');
    }

    #[Test]
    public function review_appears_on_destinasi_detail_page()
    {
        Ulasan::create([
            'destinasi_id' => $this->destinasi->id,
            'user_id' => $this->wisatawan->id,
            'rating' => 5,
            'komentar' => 'Tempat yang sangat indah!',
        ]);

        $response = $this->get("/destinasi/{$this->destinasi->id}");
        $response->assertStatus(200);
        $response->assertSee('Tempat yang sangat indah!');
    }

    #[Test]
    public function pemilik_can_view_reviews_for_their_destinasi()
    {
        Ulasan::create([
            'destinasi_id' => $this->destinasi->id,
            'user_id' => $this->wisatawan->id,
            'rating' => 4,
            'komentar' => 'Bagus!',
        ]);

        $this->actingAs($this->pemilik);
        $response = $this->get('/ulasan');
        $response->assertStatus(200);
    }
}

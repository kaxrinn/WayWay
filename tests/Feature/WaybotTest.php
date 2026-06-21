<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\PaketPromosi;
use Tests\WithoutRefresh;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WaybotTest extends TestCase
{
    use WithoutRefresh;

    protected User $wisatawan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wisatawan = User::factory()->create([
            'role' => 'wisatawan',
        ]);

        $kategori = Kategori::create(['nama_kategori' => 'Pantai']);
        $pemilik = User::factory()->create(['role' => 'pemilik_wisata']);

        // Create some destinations for chatbot to recommend
        Destinasi::create([
            'nama_destinasi' => 'Pantai Nongsa',
            'latitude' => 1.1500,
            'longitude' => 104.1200,
            'deskripsi' => 'Pantai indah di Batam',
            'harga' => 25000,
            'kategori_id' => $kategori->id,
            'user_id' => $pemilik->id,
            'status' => 'active',
        ]);
    }

    #[Test]
    public function waybot_chat_returns_response_for_authenticated_user()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->postJson('/waybot/chat', [
            'message' => 'Rekomendasikan destinasi wisata',
            'gps_lat' => 1.1296758,
            'gps_lng' => 104.0452254,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'session_token',
        ]);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function waybot_returns_session_token()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->postJson('/waybot/chat', [
            'message' => 'Halo Waybot',
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertNotEmpty($data['session_token']);
    }

    #[Test]
    public function waybot_maintains_session_across_messages()
    {
        $this->actingAs($this->wisatawan);

        // First message
        $response1 = $this->postJson('/waybot/chat', [
            'message' => 'Halo, saya ingin wisata ke pantai',
        ]);
        $sessionToken = $response1->json('session_token');

        // Second message with same session
        $response2 = $this->postJson('/waybot/chat', [
            'message' => 'Berapa harga masuknya?',
            'session_token' => $sessionToken,
        ]);

        $response2->assertStatus(200);
        $this->assertEquals($sessionToken, $response2->json('session_token'));
    }

    #[Test]
    public function waybot_history_returns_messages()
    {
        $this->actingAs($this->wisatawan);

        // Send a message first
        $response = $this->postJson('/waybot/chat', [
            'message' => 'Test message',
        ]);
        $sessionToken = $response->json('session_token');

        // Get history
        $historyResponse = $this->getJson("/waybot/history?session_token={$sessionToken}");
        $historyResponse->assertStatus(200);
        $historyResponse->assertJsonStructure(['messages']);
        $this->assertIsArray($historyResponse->json('messages'));
    }

    #[Test]
    public function waybot_reset_clears_session()
    {
        $this->actingAs($this->wisatawan);

        // Create session
        $response = $this->postJson('/waybot/chat', [
            'message' => 'Test message',
        ]);
        $sessionToken = $response->json('session_token');

        // Reset session
        $resetResponse = $this->postJson('/waybot/reset', [
            'session_token' => $sessionToken,
        ]);

        $resetResponse->assertStatus(200);
        $resetResponse->assertJson(['success' => true]);
    }

    #[Test]
    public function waybot_rejects_empty_message()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->postJson('/waybot/chat', [
            'message' => '',
        ]);

        $response->assertStatus(422);
    }
}

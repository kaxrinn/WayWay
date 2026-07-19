<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\PaketPromosi;
use Tests\WithoutRefresh;
use Tests\TestCase;

class UserAcceptanceTest extends TestCase
{
    use WithoutRefresh;

    protected Kategori $kategori;
    protected User $wisatawan;
    protected User $pemilik;
    protected User $agent;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed necessary data
        $this->kategori = Kategori::firstOrCreate(['nama_kategori' => 'Pantai']);

        $this->wisatawan = User::factory()->create(['role' => 'wisatawan']);
        $this->pemilik = User::factory()->create(['role' => 'pemilik_wisata']);
        $this->agent = User::factory()->create(['role' => 'travel_agent']);

        // Create a destinasi for chatbot/itinerary tests
        Destinasi::create([
            'nama_destinasi' => 'Pantai UAT Test',
            'latitude' => 1.15,
            'longitude' => 104.12,
            'deskripsi' => 'Pantai UAT',
            'harga' => 20000,
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->pemilik->id,
            'status' => 'active',
        ]);
    }

    /**
     * US-01: Sebagai Wisatawan, saya ingin berkonsultasi dengan Waybot AI Chatbot
     * untuk mendapatkan rekomendasi destinasi wisata di Batam.
     * 
     * @test
     */
    public function uat_wisatawan_can_use_waybot_for_recommendations()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->postJson('/waybot/chat', [
            'message' => 'Rekomendasikan pantai yang bagus di Batam.',
            'gps_lat' => 1.1296758,
            'gps_lng' => 104.0452254,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'session_token',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('message'));
    }

    /**
     * US-02: Sebagai Wisatawan, saya ingin menggunakan fitur Itinerary AI Planner
     * untuk menyusun rencana perjalanan harian sesuai budget.
     * 
     * @test
     */
    public function uat_wisatawan_can_generate_itinerary_with_budget()
    {
        $this->actingAs($this->wisatawan);

        $response = $this->postJson('/itinerary/generate', [
            'kategori_ids' => [$this->kategori->id],
            'budget' => 100000,
            'companion' => 'keluarga',
            'tanggal' => '2026-12-25',
            'origin_lat' => 1.1296758,
            'origin_lon' => 104.0452254,
            'origin_label' => 'Batam Center',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'history_id'
            ]
        ]);
        $this->assertTrue($response->json('success'));
    }

    /**
     * US-03: Sebagai Pemilik Wisata, saya ingin membeli paket Standard/Premium
     * agar mendapatkan kapasitas upload destinasi dan fitur promosi yang lebih besar.
     * 
     * @test
     */
    public function uat_pemilik_wisata_receives_increased_limits_upon_upgrading()
    {
        $basic = PaketPromosi::firstOrCreate(
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

        $premium = PaketPromosi::firstOrCreate(
            ['nama_paket' => 'Premium'],
            [
                'deskripsi' => 'Premium package',
                'harga' => 149000,
                'durasi_hari' => 30,
                'fitur' => 'Priority listing',
                'status' => 'active',
                'max_destinasi' => 10,
                'max_foto' => 20,
                'max_video' => 20,
                'priority_level' => 3,
                'can_edit_foto' => true,
                'is_featured_allowed' => true,
            ]
        );

        // 1. Initial Basic Limits
        $this->pemilik->update(['current_paket_id' => $basic->id]);
        $limitsBasic = $this->pemilik->getPaketLimits();
        $this->assertEquals(1, $limitsBasic['max_destinasi']);
        $this->assertFalse($limitsBasic['can_edit_foto']);

        // 2. Simulated Payment & Package Activation
        $this->pemilik->update(['current_paket_id' => $premium->id]);
        $this->pemilik->refresh();
        
        // 3. Upgraded Premium Limits
        $limitsPremium = $this->pemilik->getPaketLimits();
        $this->assertEquals(10, $limitsPremium['max_destinasi']);
        $this->assertTrue($limitsPremium['can_edit_foto']);
    }

    /**
     * US-04: Sebagai Travel Agent, saya ingin mempublikasikan paket wisata
     * agar dapat dicari dan dibooking oleh wisatawan di platform.
     * 
     * @test
     */
    public function uat_travel_agent_can_publish_travel_packages()
    {
        $this->actingAs($this->agent);

        $response = $this->post('/travel-agent/packages', [
            'nama_paket' => 'Paket Batam UAT 3D2N',
            'deskripsi' => 'Paket wisata keliling Batam E2E',
            'harga_per_orang' => 1500000,
            'durasi_hari' => 3,
            'destinasi' => ['Pantai Nongsa', 'Jembatan Barelang'],
            'meeting_point' => 'Batam Center Ferry Terminal',
            'min_peserta' => 2,
            'max_peserta' => 10,
            'tanggal_keberangkatan' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $response->assertRedirect('/travel-agent/packages');
        $this->assertDatabaseHas('travel_packages', [
            'nama_paket' => 'Paket Batam UAT 3D2N',
            'travel_agent_id' => $this->agent->id,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TravelPackage;
use App\Models\TravelAgentSubscription;
use App\Models\TravelAgentSubscriptionPackage;
use Tests\WithoutRefresh;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TravelAgentTest extends TestCase
{
    use WithoutRefresh;

    protected User $travelAgent;
    protected TravelAgentSubscriptionPackage $basicPackage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basicPackage = TravelAgentSubscriptionPackage::create([
            'nama_paket' => 'Basic',
            'harga' => 0,
            'durasi_hari' => 0,
            'max_packages' => 1,
            'status' => 'active',
        ]);

        $this->travelAgent = User::factory()->create([
            'role' => 'travel_agent',
        ]);

        // Create active subscription
        TravelAgentSubscription::create([
            'travel_agent_id' => $this->travelAgent->id,
            'package_id' => $this->basicPackage->id,
            'status' => 'active',
            'started_at' => now(),
            'expired_at' => null,
        ]);
    }

    #[Test]
    public function travel_agent_dashboard_requires_authentication()
    {
        $response = $this->get('/travel-agent/dashboard');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function travel_agent_can_view_dashboard()
    {
        $this->actingAs($this->travelAgent);
        $response = $this->get('/travel-agent/dashboard');
        $response->assertStatus(200);
    }

    #[Test]
    public function travel_agent_can_view_packages_list()
    {
        $this->actingAs($this->travelAgent);
        $response = $this->get('/travel-agent/packages');
        $response->assertStatus(200);
    }

    #[Test]
    public function travel_agent_can_create_package_within_limit()
    {
        $this->actingAs($this->travelAgent);

        $response = $this->post('/travel-agent/packages', [
            'nama_paket' => 'Paket Batam 3D2N',
            'deskripsi' => 'Paket wisata lengkap di Batam',
            'harga_per_orang' => 500000,
            'durasi_hari' => 3,
            'tanggal_keberangkatan' => now()->addMonths(3)->format('Y-m-d'),
            'destinasi' => ['Pantai Nongsa', 'Jembatan Barelang'],
            'min_peserta' => 2,
            'max_peserta' => 20,
            'meeting_point' => 'Batam Center Ferry Terminal',
        ]);

        $response->assertRedirect('/travel-agent/packages');
        $this->assertDatabaseHas('travel_packages', [
            'nama_paket' => 'Paket Batam 3D2N',
            'travel_agent_id' => $this->travelAgent->id,
        ]);
    }

    #[Test]
    public function travel_agent_cannot_exceed_package_limit()
    {
        $this->actingAs($this->travelAgent);

        // Create 1 package (Basic limit = 1)
        TravelPackage::create([
            'travel_agent_id' => $this->travelAgent->id,
            'nama_paket' => 'Existing Package',
            'harga_per_orang' => 500000,
            'durasi_hari' => 3,
            'tanggal_keberangkatan' => now()->addMonths(3),
            'destinasi' => ['Pantai Nongsa'],
            'min_peserta' => 2,
            'max_peserta' => 20,
            'meeting_point' => 'Batam Center',
        ]);

        // Try to create another
        $response = $this->get('/travel-agent/packages/create');
        $response->assertRedirect('/travel-agent/packages');
        $response->assertSessionHas('error');
    }

    #[Test]
    public function travel_agent_can_update_own_package()
    {
        $this->actingAs($this->travelAgent);

        $package = TravelPackage::create([
            'travel_agent_id' => $this->travelAgent->id,
            'nama_paket' => 'Original Package',
            'harga_per_orang' => 500000,
            'durasi_hari' => 3,
            'tanggal_keberangkatan' => now()->addMonths(3),
            'destinasi' => ['Pantai Nongsa'],
            'min_peserta' => 2,
            'max_peserta' => 20,
            'meeting_point' => 'Batam Center',
        ]);

        $response = $this->put("/travel-agent/packages/{$package->id}", [
            'nama_paket' => 'Updated Package',
            'harga_per_orang' => 550000,
            'durasi_hari' => 3,
            'tanggal_keberangkatan' => now()->addMonths(4)->format('Y-m-d'),
            'destinasi' => ['Pantai Nongsa'],
            'min_peserta' => 2,
            'max_peserta' => 25,
            'meeting_point' => 'Batam Center',
        ]);

        $response->assertRedirect('/travel-agent/packages');
        $this->assertDatabaseHas('travel_packages', [
            'id' => $package->id,
            'nama_paket' => 'Updated Package',
        ]);
    }

    #[Test]
    public function travel_agent_cannot_edit_other_agents_package()
    {
        $otherAgent = User::factory()->create(['role' => 'travel_agent']);
        $package = TravelPackage::create([
            'travel_agent_id' => $otherAgent->id,
            'nama_paket' => 'Other Package',
            'harga_per_orang' => 500000,
            'durasi_hari' => 3,
            'tanggal_keberangkatan' => now()->addMonths(3),
            'destinasi' => ['Pantai Nongsa'],
            'min_peserta' => 2,
            'max_peserta' => 20,
            'meeting_point' => 'Batam Center',
        ]);

        $this->actingAs($this->travelAgent);
        $response = $this->get("/travel-agent/packages/{$package->id}");
        $response->assertStatus(404);
    }

    #[Test]
    public function travel_agent_can_delete_own_package()
    {
        $this->actingAs($this->travelAgent);

        $package = TravelPackage::create([
            'travel_agent_id' => $this->travelAgent->id,
            'nama_paket' => 'To Delete',
            'harga_per_orang' => 500000,
            'durasi_hari' => 3,
            'tanggal_keberangkatan' => now()->addMonths(3),
            'destinasi' => ['Pantai Nongsa'],
            'min_peserta' => 2,
            'max_peserta' => 20,
            'meeting_point' => 'Batam Center',
        ]);

        $response = $this->delete("/travel-agent/packages/{$package->id}");
        $response->assertRedirect('/travel-agent/packages');
        $this->assertDatabaseMissing('travel_packages', ['id' => $package->id]);
    }

    #[Test]
    public function wisatawan_cannot_access_travel_agent_routes()
    {
        $wisatawan = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($wisatawan);

        $response = $this->get('/travel-agent/dashboard');
        $response->assertStatus(403);
    }
}

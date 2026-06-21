<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Destinasi;
use App\Models\Kategori;
use App\Models\TransaksiPromosi;
use App\Models\PaketPromosi;
use Tests\WithoutRefresh;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use WithoutRefresh;

    protected User $admin;
    protected User $wisatawan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->wisatawan = User::factory()->create(['role' => 'wisatawan']);
    }

    #[Test]
    public function admin_can_access_dashboard()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    #[Test]
    public function non_admin_cannot_access_admin_dashboard()
    {
        $this->actingAs($this->wisatawan);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_wisatawan_list()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin/wisatawan');
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_destinasi_list()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin/destinasi');
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_create_kategori()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/kategori', [
            'nama_kategori' => 'Kategori Test',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'Kategori Test']);
    }

    #[Test]
    public function admin_can_update_kategori()
    {
        $this->actingAs($this->admin);
        $kategori = Kategori::create(['nama_kategori' => 'Old Name']);

        $response = $this->put("/admin/kategori/{$kategori->id}", [
            'nama_kategori' => 'New Name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kategori', ['id' => $kategori->id, 'nama_kategori' => 'New Name']);
    }

    #[Test]
    public function admin_can_delete_kategori()
    {
        $this->actingAs($this->admin);
        $kategori = Kategori::create(['nama_kategori' => 'To Delete']);

        $response = $this->delete("/admin/kategori/{$kategori->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
    }

    #[Test]
    public function admin_can_view_transaksi_list()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin/transaksi');
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_travel_agents_list()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin/travel-agents');
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_edit_requests()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/admin/edit-requests');
        $response->assertStatus(200);
    }

    #[Test]
    public function unauthenticated_cannot_access_admin_routes()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }
}

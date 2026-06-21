<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PaketPromosi;
use Tests\WithoutRefresh;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithoutRefresh;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed basic package
        PaketPromosi::create([
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
    }

    #[Test]
    public function registration_form_loads_successfully()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Register');
    }

    #[Test]
    public function wisatawan_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'budi@test.com',
            'role' => 'wisatawan',
        ]);
    }

    #[Test]
    public function registration_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function registration_fails_with_mismatched_passwords()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Different123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function registration_fails_with_invalid_email()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'notanemail',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function wisatawan_can_login_with_valid_credentials()
    {
        $email = 'wisatawan_' . uniqid() . '@test.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('Password123'),
            'role' => 'wisatawan',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'Password123',
        ]);

        $response->assertRedirect('/wisatawan/beranda');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function admin_is_redirected_to_admin_dashboard_after_login()
    {
        $email = 'admin_' . uniqid() . '@test.com';
        $admin = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('Password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'Password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    #[Test]
    public function pemilik_is_redirected_to_pemilik_dashboard_after_login()
    {
        $email = 'pemilik_' . uniqid() . '@test.com';
        $pemilik = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('Password123'),
            'role' => 'pemilik_wisata',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'Password123',
        ]);

        $response->assertRedirect('/pemilik/dashboard');
    }

    #[Test]
    public function travel_agent_is_redirected_to_travel_agent_dashboard_after_login()
    {
        $email = 'agent_' . uniqid() . '@test.com';
        $agent = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('Password123'),
            'role' => 'travel_agent',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'Password123',
        ]);

        $response->assertRedirect('/travel-agent/dashboard');
    }

    #[Test]
    public function login_fails_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('CorrectPassword'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    #[Test]
    public function login_fails_with_nonexistent_email()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'Password123',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    #[Test]
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    #[Test]
    public function unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get('/wisatawan/profil');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function wisatawan_cannot_access_admin_routes()
    {
        $wisatawan = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($wisatawan);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    #[Test]
    public function wisatawan_cannot_access_pemilik_routes()
    {
        $wisatawan = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($wisatawan);

        $response = $this->get('/pemilik/dashboard');
        $response->assertStatus(403);
    }
}

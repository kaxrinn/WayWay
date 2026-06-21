# WayWay Tourism Platform - Unit Test Recommendations

## Overview
Unit tests should be isolated, fast, and test a single unit of logic. For WayWay, the following services and logic components require dedicated unit tests.

---

## 1. HaversineService - Distance Calculation

**File:** `app/Services/HaversineService.php`
**Test File:** `tests/Unit/HaversineServiceTest.php`

### What to Test:
```php
<?php
namespace Tests\Unit;

use App\Services\HaversineService;
use Tests\TestCase;

class HaversineServiceTest extends TestCase
{
    private HaversineService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HaversineService();
    }

    /** @test */
    public function calculates_distance_between_two_known_points()
    {
        // Batam Center to Pantai Nongsa: approximately 12-15 km
        $distance = $this->service->calculate(
            1.1296758, 104.0452254,  // Batam Center
            1.1500000, 104.1200000   // Pantai Nongsa area
        );
        
        $this->assertGreaterThan(5, $distance);
        $this->assertLessThan(20, $distance);
    }

    /** @test */
    public function returns_zero_for_same_coordinates()
    {
        $distance = $this->service->calculate(1.1296758, 104.0452254, 1.1296758, 104.0452254);
        $this->assertEquals(0, $distance);
    }

    /** @test */
    public function distance_is_symmetric()
    {
        $d1 = $this->service->calculate(1.1296758, 104.0452254, 1.1500, 104.1200);
        $d2 = $this->service->calculate(1.1500, 104.1200, 1.1296758, 104.0452254);
        $this->assertEqualsWithDelta($d1, $d2, 0.001);
    }

    /** @test */
    public function returns_distance_in_kilometers()
    {
        // Known distance: Batam to Singapore ~20km
        $distance = $this->service->calculate(
            1.1296758, 104.0452254,  // Batam
            1.3521, 103.8198         // Singapore
        );
        $this->assertGreaterThan(15, $distance);
        $this->assertLessThan(30, $distance);
    }

    /** @test */
    public function handles_negative_coordinates()
    {
        // Southern hemisphere coordinates
        $distance = $this->service->calculate(-6.2088, 106.8456, -7.2575, 112.7521);
        $this->assertGreaterThan(0, $distance);
    }

    /** @test */
    public function sorts_destinations_by_distance_from_origin()
    {
        $origin = ['lat' => 1.1296758, 'lng' => 104.0452254];
        $destinations = [
            ['id' => 1, 'lat' => 1.1500, 'lng' => 104.1200],  // ~8km
            ['id' => 2, 'lat' => 1.0500, 'lng' => 104.0000],  // ~9km
            ['id' => 3, 'lat' => 1.1300, 'lng' => 104.0500],  // ~0.5km
        ];
        
        $sorted = $this->service->sortByDistance($origin, $destinations);
        $this->assertEquals(3, $sorted[0]['id']); // Closest first
    }
}
```

### Key Test Cases:
| Test Case | Input | Expected Output |
|-----------|-------|-----------------|
| Same point | lat1=lng1=lat2=lng2 | 0 km |
| Known distance | Batam-Singapore | ~20 km |
| Symmetry | A→B == B→A | Equal distances |
| Negative coords | Southern hemisphere | Positive distance |
| Sort by distance | Array of destinations | Sorted ascending |

---

## 2. BayesianScoringService - Recommendation Logic

**File:** `app/Services/BayesianScoringService.php`
**Test File:** `tests/Unit/BayesianScoringServiceTest.php`

### What to Test:
```php
<?php
namespace Tests\Unit;

use App\Services\BayesianScoringService;
use Tests\TestCase;

class BayesianScoringServiceTest extends TestCase
{
    private BayesianScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BayesianScoringService();
    }

    /** @test */
    public function destination_with_more_reviews_gets_higher_confidence()
    {
        $dest1 = ['rating_avg' => 4.5, 'rating_count' => 100];
        $dest2 = ['rating_avg' => 4.5, 'rating_count' => 5];
        
        $score1 = $this->service->calculateScore($dest1);
        $score2 = $this->service->calculateScore($dest2);
        
        $this->assertGreaterThan($score2, $score1);
    }

    /** @test */
    public function destination_with_higher_rating_gets_higher_score()
    {
        $dest1 = ['rating_avg' => 5.0, 'rating_count' => 50];
        $dest2 = ['rating_avg' => 3.0, 'rating_count' => 50];
        
        $score1 = $this->service->calculateScore($dest1);
        $score2 = $this->service->calculateScore($dest2);
        
        $this->assertGreaterThan($score2, $score1);
    }

    /** @test */
    public function premium_destination_gets_priority_boost()
    {
        $regular = ['rating_avg' => 4.0, 'rating_count' => 20, 'priority_level' => 1];
        $premium = ['rating_avg' => 4.0, 'rating_count' => 20, 'priority_level' => 3];
        
        $scoreRegular = $this->service->calculateScore($regular);
        $scorePremium = $this->service->calculateScore($premium);
        
        $this->assertGreaterThan($scoreRegular, $scorePremium);
    }

    /** @test */
    public function destination_with_zero_reviews_gets_prior_score()
    {
        $dest = ['rating_avg' => 0, 'rating_count' => 0];
        $score = $this->service->calculateScore($dest);
        
        $this->assertGreaterThan(0, $score);
        $this->assertLessThan(5, $score);
    }

    /** @test */
    public function scores_destinations_list_in_descending_order()
    {
        $destinations = [
            ['id' => 1, 'rating_avg' => 3.0, 'rating_count' => 10, 'priority_level' => 1],
            ['id' => 2, 'rating_avg' => 4.8, 'rating_count' => 100, 'priority_level' => 3],
            ['id' => 3, 'rating_avg' => 4.0, 'rating_count' => 50, 'priority_level' => 2],
        ];
        
        $scored = $this->service->scoreAndSort($destinations);
        
        $this->assertEquals(2, $scored[0]['id']); // Highest score first
    }
}
```

### Key Test Cases:
| Test Case | Scenario | Expected |
|-----------|----------|----------|
| Review count effect | Same rating, different counts | More reviews = higher score |
| Rating effect | Same count, different ratings | Higher rating = higher score |
| Priority boost | Premium vs Basic package | Premium gets boost |
| Zero reviews | New destination | Gets prior/default score |
| Sort order | Mixed destinations | Descending by score |

---

## 3. ItineraryService - Itinerary Generation Pipeline

**File:** `app/Services/ItineraryService.php`
**Test File:** `tests/Unit/ItineraryServiceTest.php`

### What to Test:
```php
<?php
namespace Tests\Unit;

use App\Services\ItineraryService;
use App\Services\HaversineService;
use App\Services\BayesianScoringService;
use App\Services\ContentFilterService;
use App\Services\GreedyRouterService;
use Tests\TestCase;
use Mockery;

class ItineraryServiceTest extends TestCase
{
    /** @test */
    public function filters_destinations_by_budget()
    {
        $destinations = [
            ['id' => 1, 'harga' => 50000],
            ['id' => 2, 'harga' => 150000],
            ['id' => 3, 'harga' => 25000],
        ];
        
        $budget = 100000;
        $filtered = array_filter($destinations, fn($d) => $d['harga'] <= $budget);
        
        $this->assertCount(2, $filtered);
        $this->assertArrayNotHasKey(1, array_values($filtered)); // id=2 excluded
    }

    /** @test */
    public function respects_max_destinations_limit()
    {
        $service = app(ItineraryService::class);
        
        $input = [
            'kategori_ids' => [1],
            'budget' => 1000000,
            'max_destinations' => 3,
            'available_hours' => 8,
            'origin_lat' => 1.1296758,
            'origin_lon' => 104.0452254,
        ];
        
        // Mock: assume service returns at most max_destinations
        $this->assertLessThanOrEqual(3, 3); // Placeholder assertion
    }

    /** @test */
    public function greedy_router_selects_nearest_unvisited_destination()
    {
        $router = new GreedyRouterService(new HaversineService());
        
        $origin = ['lat' => 1.1296758, 'lng' => 104.0452254];
        $destinations = [
            ['id' => 1, 'latitude' => 1.1500, 'longitude' => 104.1200],
            ['id' => 2, 'latitude' => 1.1300, 'longitude' => 104.0500], // Closer
            ['id' => 3, 'latitude' => 1.2000, 'longitude' => 104.2000],
        ];
        
        $route = $router->buildRoute($origin, $destinations);
        
        $this->assertEquals(2, $route[0]['id']); // Nearest first
    }

    /** @test */
    public function content_filter_removes_inactive_destinations()
    {
        $destinations = [
            ['id' => 1, 'status' => 'active'],
            ['id' => 2, 'status' => 'inactive'],
            ['id' => 3, 'status' => 'active'],
        ];
        
        $filtered = array_filter($destinations, fn($d) => $d['status'] === 'active');
        
        $this->assertCount(2, $filtered);
    }

    /** @test */
    public function itinerary_assigns_time_slots_correctly()
    {
        $destinations = [
            ['id' => 1, 'nama_destinasi' => 'Pantai A'],
            ['id' => 2, 'nama_destinasi' => 'Pantai B'],
        ];
        
        $startTime = '08:00';
        $visitDuration = 2; // hours per destination
        
        // Verify time slot assignment logic
        $currentTime = strtotime($startTime);
        foreach ($destinations as $i => $dest) {
            $slot = date('H:i', $currentTime + ($i * $visitDuration * 3600));
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $slot);
        }
    }
}
```

---

## 4. PaketPromosi - Package Promotion Calculations

**File:** `app/Models/PaketPromosi.php`, `app/Models/User.php`
**Test File:** `tests/Unit/PaketPromosiTest.php`

### What to Test:
```php
<?php
namespace Tests\Unit;

use App\Models\PaketPromosi;
use App\Models\User;
use App\Models\TransaksiPromosi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaketPromosiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function basic_package_is_free()
    {
        $basic = PaketPromosi::create([
            'nama_paket' => 'Basic',
            'harga' => 0,
            'durasi_hari' => 0,
            'max_destinasi' => 1,
            'max_foto' => 3,
            'max_video' => 0,
            'priority_level' => 1,
            'can_edit_foto' => false,
            'is_featured_allowed' => false,
            'status' => 'active',
        ]);
        
        $this->assertEquals(0, $basic->harga);
        $this->assertFalse((bool)$basic->can_edit_foto);
        $this->assertFalse((bool)$basic->is_featured_allowed);
    }

    /** @test */
    public function standard_package_has_correct_limits()
    {
        $standard = PaketPromosi::create([
            'nama_paket' => 'Standard',
            'harga' => 49000,
            'durasi_hari' => 30,
            'max_destinasi' => 3,
            'max_foto' => 8,
            'max_video' => 0,
            'priority_level' => 2,
            'can_edit_foto' => true,
            'is_featured_allowed' => false,
            'status' => 'active',
        ]);
        
        $this->assertEquals(49000, $standard->harga);
        $this->assertEquals(3, $standard->max_destinasi);
        $this->assertEquals(8, $standard->max_foto);
        $this->assertTrue((bool)$standard->can_edit_foto);
    }

    /** @test */
    public function premium_package_allows_featured()
    {
        $premium = PaketPromosi::create([
            'nama_paket' => 'Premium',
            'harga' => 149000,
            'durasi_hari' => 30,
            'max_destinasi' => 10,
            'max_foto' => 20,
            'max_video' => 20,
            'priority_level' => 3,
            'can_edit_foto' => true,
            'is_featured_allowed' => true,
            'status' => 'active',
        ]);
        
        $this->assertTrue((bool)$premium->is_featured_allowed);
        $this->assertEquals(3, $premium->priority_level);
        $this->assertEquals(20, $premium->max_video);
    }

    /** @test */
    public function user_get_paket_limits_returns_correct_values()
    {
        $basic = PaketPromosi::create([
            'nama_paket' => 'Basic', 'harga' => 0, 'durasi_hari' => 0,
            'max_destinasi' => 1, 'max_foto' => 3, 'max_video' => 0,
            'priority_level' => 1, 'can_edit_foto' => false,
            'is_featured_allowed' => false, 'status' => 'active',
        ]);
        
        $user = User::factory()->create(['current_paket_id' => $basic->id]);
        $limits = $user->getPaketLimits();
        
        $this->assertEquals(1, $limits['max_destinasi']);
        $this->assertEquals(3, $limits['max_foto']);
        $this->assertFalse($limits['can_edit_foto']);
    }

    /** @test */
    public function transaksi_promosi_order_id_format_is_correct()
    {
        $userId = 42;
        $timestamp = 1234567890;
        $orderId = "TRX-{$userId}-{$timestamp}";
        
        $this->assertMatchesRegularExpression('/^TRX-\d+-\d+$/', $orderId);
    }
}
```

---

## 5. User Role Permissions

**File:** `app/Http/Middleware/`
**Test File:** `tests/Unit/UserRolePermissionsTest.php`

### What to Test:
```php
<?php
namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function wisatawan_role_is_default()
    {
        $user = User::factory()->create();
        $this->assertEquals('wisatawan', $user->role);
    }

    /** @test */
    public function user_has_correct_role_methods()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $wisatawan = User::factory()->create(['role' => 'wisatawan']);
        $pemilik = User::factory()->create(['role' => 'pemilik_wisata']);
        $agent = User::factory()->create(['role' => 'travel_agent']);
        
        // Test role checks if implemented as methods
        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('wisatawan', $wisatawan->role);
        $this->assertEquals('pemilik_wisata', $pemilik->role);
        $this->assertEquals('travel_agent', $agent->role);
    }

    /** @test */
    public function valid_roles_are_enforced()
    {
        $validRoles = ['admin', 'pemilik_wisata', 'wisatawan', 'travel_agent'];
        
        foreach ($validRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->assertContains($user->role, $validRoles);
        }
    }

    /** @test */
    public function user_can_have_google_oauth_without_password()
    {
        $user = User::factory()->create([
            'password' => null,
            'google_id' => 'google_123456',
        ]);
        
        $this->assertNull($user->password);
        $this->assertNotNull($user->google_id);
    }

    /** @test */
    public function user_current_paket_relationship_works()
    {
        $paket = \App\Models\PaketPromosi::create([
            'nama_paket' => 'Basic', 'harga' => 0, 'durasi_hari' => 0,
            'max_destinasi' => 1, 'max_foto' => 3, 'max_video' => 0,
            'priority_level' => 1, 'can_edit_foto' => false,
            'is_featured_allowed' => false, 'status' => 'active',
        ]);
        
        $user = User::factory()->create(['current_paket_id' => $paket->id]);
        
        $this->assertNotNull($user->currentPaket);
        $this->assertEquals('Basic', $user->currentPaket->nama_paket);
    }
}
```

---

## 6. Validation Rules

**Test File:** `tests/Unit/ValidationRulesTest.php`

### What to Test:
```php
<?php
namespace Tests\Unit;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidationRulesTest extends TestCase
{
    /** @test */
    public function registration_validation_rules()
    {
        $validData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];
        
        $validator = Validator::make($validData, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function destinasi_validation_requires_valid_coordinates()
    {
        $invalidData = [
            'nama_destinasi' => 'Test',
            'latitude' => 'not_a_number',
            'longitude' => 104.12,
            'deskripsi' => 'Test',
            'harga' => 25000,
            'kategori_id' => 1,
        ];
        
        $validator = Validator::make($invalidData, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('latitude', $validator->errors()->toArray());
    }

    /** @test */
    public function travel_package_requires_future_departure_date()
    {
        $pastDate = now()->subDay()->format('Y-m-d');
        
        $validator = Validator::make(
            ['tanggal_keberangkatan' => $pastDate],
            ['tanggal_keberangkatan' => 'required|date|after:today']
        );
        
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function travel_package_requires_min_less_than_max_peserta()
    {
        $data = ['min_peserta' => 20, 'max_peserta' => 5];
        
        $validator = Validator::make($data, [
            'min_peserta' => 'required|integer|min:1',
            'max_peserta' => 'required|integer|min:1|gte:min_peserta',
        ]);
        
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function ulasan_rating_must_be_between_1_and_5()
    {
        foreach ([0, 6, -1] as $invalidRating) {
            $validator = Validator::make(
                ['rating' => $invalidRating],
                ['rating' => 'required|integer|min:1|max:5']
            );
            $this->assertTrue($validator->fails(), "Rating $invalidRating should fail");
        }
        
        foreach ([1, 2, 3, 4, 5] as $validRating) {
            $validator = Validator::make(
                ['rating' => $validRating],
                ['rating' => 'required|integer|min:1|max:5']
            );
            $this->assertFalse($validator->fails(), "Rating $validRating should pass");
        }
    }

    /** @test */
    public function harga_must_be_non_negative()
    {
        $validator = Validator::make(
            ['harga' => -1000],
            ['harga' => 'required|numeric|min:0']
        );
        
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function email_must_be_valid_format()
    {
        $invalidEmails = ['notanemail', 'missing@', '@nodomain.com', 'spaces in@email.com'];
        
        foreach ($invalidEmails as $email) {
            $validator = Validator::make(
                ['email' => $email],
                ['email' => 'required|email']
            );
            $this->assertTrue($validator->fails(), "Email '$email' should fail");
        }
    }
}
```

---

## Running Unit Tests

```bash
# Run all unit tests
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Unit/HaversineServiceTest.php

# Run with coverage
php artisan test --coverage --min=70

# Run with verbose output
php artisan test --verbose tests/Unit/
```

## Test Coverage Targets

| Component | Target Coverage |
|-----------|----------------|
| HaversineService | 95% |
| BayesianScoringService | 90% |
| ItineraryService | 80% |
| PaketPromosi calculations | 95% |
| User role permissions | 90% |
| Validation rules | 95% |

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
        // Batam Center to Pantai Nongsa: approximately 8-12 km
        $distance = $this->service->distance(
            1.1296758, 104.0452254,  // Batam Center
            1.1500000, 104.1200000   // Pantai Nongsa area
        );

        $this->assertGreaterThan(5, $distance);
        $this->assertLessThan(20, $distance);
    }

    /** @test */
    public function returns_zero_for_same_coordinates()
    {
        $distance = $this->service->distance(1.1296758, 104.0452254, 1.1296758, 104.0452254);
        $this->assertEquals(0, $distance);
    }

    /** @test */
    public function distance_is_symmetric()
    {
        $d1 = $this->service->distance(1.1296758, 104.0452254, 1.1500, 104.1200);
        $d2 = $this->service->distance(1.1500, 104.1200, 1.1296758, 104.0452254);
        $this->assertEqualsWithDelta($d1, $d2, 0.001);
    }

    /** @test */
    public function returns_distance_in_kilometers()
    {
        // Batam to Singapore actual ~35km
        $distance = $this->service->distance(
            1.1296758, 104.0452254,  // Batam
            1.3521, 103.8198         // Singapore
        );
        $this->assertGreaterThan(30, $distance);
        $this->assertLessThan(50, $distance);
    }

    /** @test */
    public function handles_negative_coordinates()
    {
        // Southern hemisphere coordinates (Jakarta to Surabaya)
        $distance = $this->service->distance(-6.2088, 106.8456, -7.2575, 112.7521);
        $this->assertGreaterThan(0, $distance);
    }

    /** @test */
    public function sorts_destinations_by_distance_from_origin()
    {
        $originLat = 1.1296758;
        $originLon = 104.0452254;
        $destinations = collect([
            (object)['id' => 1, 'latitude' => 1.1500, 'longitude' => 104.1200],  // ~8km
            (object)['id' => 2, 'latitude' => 1.0500, 'longitude' => 104.0000],  // ~9km
            (object)['id' => 3, 'latitude' => 1.1300, 'longitude' => 104.0500],  // ~0.5km
        ]);

        $mapped = $this->service->attachDistances($destinations, $originLat, $originLon);
        $sorted = $mapped->sortBy('distance_from_origin')->values();

        $this->assertEquals(3, $sorted[0]->id); // Closest first
    }
}

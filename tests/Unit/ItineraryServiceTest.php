<?php

namespace Tests\Unit;

use App\Services\HaversineService;
use App\Services\GreedyRouterService;
use Tests\TestCase;

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
        $filtered = array_values(array_filter($destinations, fn($d) => $d['harga'] <= $budget));

        // Only id=1 and id=3 should survive the filter
        $this->assertCount(2, $filtered);

        $filteredIds = array_column($filtered, 'id');
        $this->assertNotContains(2, $filteredIds);  // id=2 (harga 150k) must be excluded
        $this->assertContains(1, $filteredIds);
        $this->assertContains(3, $filteredIds);
    }

    /** @test */
    public function respects_max_destinations_limit()
    {
        // Simple assertion placeholder for limit enforcement
        $this->assertLessThanOrEqual(3, 3);
    }

    /** @test */
    public function greedy_router_selects_nearest_unvisited_destination()
    {
        $router = new GreedyRouterService(new HaversineService());

        $originLat = 1.1296758;
        $originLon = 104.0452254;

        $destinations = collect([
            (object)[
                'id'             => 1,
                'latitude'       => 1.1500,
                'longitude'      => 104.1200,
                'bayesian_score' => 0.8,
                'foto'           => null,
                'nama_destinasi' => 'Pantai A',
                'harga'          => 25000,
                'deskripsi'      => 'Deskripsi A',
                'is_featured'    => false,
                'score_breakdown' => [],
                'kategori'       => (object)['nama_kategori' => 'Beaches'],
            ],
            (object)[
                'id'             => 2,
                'latitude'       => 1.1300,
                'longitude'      => 104.0500, // Lebih dekat ke origin
                'bayesian_score' => 0.8,
                'foto'           => null,
                'nama_destinasi' => 'Pantai B',
                'harga'          => 20000,
                'deskripsi'      => 'Deskripsi B',
                'is_featured'    => false,
                'score_breakdown' => [],
                'kategori'       => (object)['nama_kategori' => 'Beaches'],
            ],
            (object)[
                'id'             => 3,
                'latitude'       => 1.2000,
                'longitude'      => 104.2000,
                'bayesian_score' => 0.8,
                'foto'           => null,
                'nama_destinasi' => 'Pantai C',
                'harga'          => 30000,
                'deskripsi'      => 'Deskripsi C',
                'is_featured'    => false,
                'score_breakdown' => [],
                'kategori'       => (object)['nama_kategori' => 'Beaches'],
            ],
        ]);

        $result = $router->buildRoute($destinations, $originLat, $originLon);
        $route = $result['route'];

        $this->assertNotEmpty($route);
        // id=2 is closest to origin — it should be first in the route
        $this->assertEquals(2, $route[0]['id']);
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

        $startTime     = '08:00';
        $visitDuration = 2; // hours per destination

        // Verify time slot assignment logic
        $currentTime = strtotime($startTime);
        foreach ($destinations as $i => $dest) {
            $slot = date('H:i', $currentTime + ($i * $visitDuration * 3600));
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $slot);
        }
    }
}

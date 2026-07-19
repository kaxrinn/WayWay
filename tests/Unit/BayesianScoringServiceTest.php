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

    /**
     * Bayesian scoring pulls scores toward global mean.
     * When rating > global mean, more reviews = higher score (more confidence).
     * 
     * @test
     */
    public function candidate_with_more_reviews_gets_higher_confidence()
    {
        // Include a low-rating candidate to pull the global mean down to ~3.0
        // So that the high-rating candidates (5.0) benefit from having more reviews
        $destLow = (object)[
            'id' => 0,
            'average_rating' => 1.0,
            'ulasan' => collect([(object)['rating' => 1.0]]),
            'is_featured' => false,
            'harga' => 100000,
            'companion_affinity' => 0,
        ];

        $dest1 = (object)[
            'id' => 1,
            'average_rating' => 5.0,
            'ulasan' => collect(array_fill(0, 10, (object)['rating' => 5.0])), // 10 reviews
            'is_featured' => false,
            'harga' => 100000,
            'companion_affinity' => 0,
        ];

        $dest2 = (object)[
            'id' => 2,
            'average_rating' => 5.0,
            'ulasan' => collect(array_fill(0, 2, (object)['rating' => 5.0])), // only 2 reviews
            'is_featured' => false,
            'harga' => 100000,
            'companion_affinity' => 0,
        ];

        // Global mean = (1.0 + 5.0 + 5.0) / 3 = 3.67
        // dest1: (10 * 3.67 + 10 * 5.0) / (10+10) = 4.33
        // dest2: (10 * 3.67 + 2 * 5.0)  / (10+2)  = 3.89
        $candidates = collect([$destLow, $dest1, $dest2]);
        $ranked = $this->service->rankCandidates($candidates);

        $score1 = $ranked->firstWhere('id', 1)->bayesian_score;
        $score2 = $ranked->firstWhere('id', 2)->bayesian_score;

        $this->assertGreaterThan($score2, $score1);
    }

    /** @test */
    public function candidate_with_higher_rating_gets_higher_score()
    {
        $dest1 = (object)[
            'id' => 1,
            'average_rating' => 5.0,
            'ulasan' => collect(array_fill(0, 5, (object)['rating' => 5.0])),
            'is_featured' => false,
            'harga' => 100000,
            'companion_affinity' => 0,
        ];

        $dest2 = (object)[
            'id' => 2,
            'average_rating' => 3.0,
            'ulasan' => collect(array_fill(0, 5, (object)['rating' => 3.0])),
            'is_featured' => false,
            'harga' => 100000,
            'companion_affinity' => 0,
        ];

        $candidates = collect([$dest1, $dest2]);
        $ranked = $this->service->rankCandidates($candidates);

        $score1 = $ranked->firstWhere('id', 1)->bayesian_score;
        $score2 = $ranked->firstWhere('id', 2)->bayesian_score;

        $this->assertGreaterThan($score2, $score1);
    }

    /** @test */
    public function featured_destination_gets_priority_boost()
    {
        $regular = (object)[
            'id' => 1,
            'average_rating' => 4.0,
            'ulasan' => collect(array_fill(0, 5, (object)['rating' => 4.0])),
            'is_featured' => false,
            'harga' => 100000,
            'companion_affinity' => 0,
        ];

        $featured = (object)[
            'id' => 2,
            'average_rating' => 4.0,
            'ulasan' => collect(array_fill(0, 5, (object)['rating' => 4.0])),
            'is_featured' => true,
            'harga' => 100000,
            'companion_affinity' => 0,
        ];

        $candidates = collect([$regular, $featured]);
        $ranked = $this->service->rankCandidates($candidates);

        $scoreRegular = $ranked->firstWhere('id', 1)->bayesian_score;
        $scoreFeatured = $ranked->firstWhere('id', 2)->bayesian_score;

        $this->assertGreaterThan($scoreRegular, $scoreFeatured);
    }

    /** @test */
    public function candidate_with_zero_reviews_gets_prior_score()
    {
        $dest = (object)[
            'id' => 1,
            'average_rating' => 3.5,
            'ulasan' => collect([]),
            'is_featured' => false,
            'harga' => 100000,
            'companion_affinity' => 0,
        ];

        $candidates = collect([$dest]);
        $ranked = $this->service->rankCandidates($candidates);

        $score = $ranked->first()->bayesian_score;
        $this->assertGreaterThan(0, $score);
        $this->assertLessThanOrEqual(1.0, $score);
    }
}

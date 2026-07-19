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
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $validator = Validator::make($validData, [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function destinasi_validation_requires_valid_coordinates()
    {
        $invalidData = [
            'nama_destinasi' => 'Test',
            'latitude'       => 'not_a_number',
            'longitude'      => 104.12,
            'deskripsi'      => 'Test',
            'harga'          => 25000,
            'kategori_id'    => 1,
        ];

        $validator = Validator::make($invalidData, [
            'latitude'  => 'required|numeric',
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

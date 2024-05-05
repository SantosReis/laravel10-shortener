<?php

namespace Database\Factories;

use App\Models\User;
use App\Service\UrlShortenerService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UrlShortener>
 */
class UrlShortenerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $urlShortenerService = new UrlShortenerService();
        $fakeUrl = fake()->url();
        return [
            'user_id' => function () {
                // return User::factory()->create()->id;  //generated base on new ones
                return User::all()->random()->id; //generated base on existing ones
            },
            'long' => $fakeUrl,
            'short' => $urlShortenerService->encrypter($fakeUrl),
            'counter' => $this->faker->numberBetween(0, 1000),
        ];
    }
}

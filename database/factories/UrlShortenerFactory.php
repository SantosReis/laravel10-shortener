<?php

namespace Database\Factories;

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
            'long' => $fakeUrl,
            'short' => $urlShortenerService->encrypter($fakeUrl),
        ];
    }
}

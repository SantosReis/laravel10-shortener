<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Service\UrlShortenerService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_it_can_send_url_to_be_shortened(): void
    {

        $url = $this->faker->url;
        $urlShortenerService = new UrlShortenerService();
        $urlShortener = $urlShortenerService->generateShortUrl($url); 

        $response = $this->json('POST', '/api/shortener', ['url' => $url]); //test a fresh fake url
        $content = json_decode($response->content());

        $this->assertDatabaseHas('url_shorteners', ['long' => $url, 'short' => $urlShortener['short_url']]);
        $this->assertEquals($content->short_url, $urlShortener['short_url']); //compare to database. once exists it means generated
        $response->assertJson(['status' => true]);
        $response->assertStatus(201);
    }

    public function test_it_can_retrieve_shortened_url_if_exists(): void
    {

        $urlShortenerService = new UrlShortenerService();
        $urlShortener = $urlShortenerService->generateShortUrl($this->faker->url); 

        $response = $this->json('POST', '/api/shortener', ['url' => $urlShortener['short_url']]); //test a database fake url
        $content = json_decode($response->content());

        $this->assertDatabaseHas('url_shorteners', ['long' => $urlShortener['long_url'], 'short' => $urlShortener['short_url']]);
        $this->assertEquals($content->short_url, $urlShortener['short_url']); //compare to database. once exists it means generated
        $response->assertJson(['status' => true]);
        $response->assertStatus(201);
    }

    public function test_it_can_retrieve_all_shortened_urls(): void{
        //TODO
    }

    public function test_it_redirect_to_original_url(): void{
        //TODO
    }

    public function test_it_can_delete_shortener(): void{
        //TODO
    }
}

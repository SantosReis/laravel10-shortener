<?php

namespace Tests\Feature\V2;

use Tests\TestCase;
use App\Models\User;
use App\Models\UrlShortener;
use App\Services\UrlShortenerService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $urlShortenerService;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->urlShortenerService = new urlShortenerService();
    }

    public function test_it_can_generate_a_shortener(): void
    {

        $this->actingAs(User::factory()->create());
        $url = $this->faker->url;
        $urlShortener = $this->urlShortenerService->generateShortUrl($url); 

        $response = $this->json('POST', '/api/v2/shortener', ['url' => $url]); //test a fresh fake url
        $content = json_decode($response->content());

        $this->assertDatabaseHas('url_shorteners', ['long' => $url, 'short' => $urlShortener['short_url']]);
        $this->assertEquals($content->short_url, $urlShortener['short_url']); //compare to database. once exists it means generated
        $this->assertEquals($content->long_url, $urlShortener['long_url']); //compare to database. once exists it means generated
        $response->assertJson(['status' => true]);
        $response->assertStatus(201);
    }

    public function test_it_cant_generate_a_shortener(): void
    {
        $this->actingAs(User::factory()->create());
        $response = $this->json('POST', '/api/v2/shortener');
        $response->assertJson([
            'message' => 'The url field is required.',
            'errors' => [
                'url' => ["The url field is required."]
            ] 
        ])->assertStatus(422);

    }

    public function test_it_can_retrieve_shortener_if_exists(): void
    {
        $this->actingAs(User::factory()->create());
        $urlShortener = $this->urlShortenerService->generateShortUrl($this->faker->url); 

        $response = $this->json('POST', '/api/v2/shortener', ['url' => $urlShortener['short_url']]); //test a database fake url
        $content = json_decode($response->content());

        $this->assertDatabaseHas('url_shorteners', ['long' => $urlShortener['long_url'], 'short' => $urlShortener['short_url']]);
        $this->assertEquals($content->short_url, $urlShortener['short_url']); //compare to database. once exists it means generated
        $response->assertJson(['status' => true]);
        $response->assertStatus(201);
    }

    public function test_it_can_retrieve_latest_shorteners(): void{

        $this->actingAs($user = User::factory()->create());
        UrlShortener::factory(5)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->json('GET', '/api/v2/shortener')->assertStatus(200);
        $response->assertJsonCount(5);
    }

    public function test_it_unauthenticated_to_retrieve_latest_shorteners(): void{

        $this->json('GET', '/api/v2/shortener')->assertJson([
            'message' => 'unAuthenticated',
        ])->assertStatus(401);

    }

    public function test_it_redirect_to_original_url(): void{
        User::factory()->create();
        $shortener = UrlShortener::factory()->create();

        $short = explode('/',$shortener->short);
        $short = end($short);

        $response = $this->get('/'.$short);
        $response->assertRedirect($shortener->long)
            ->assertStatus(302);

        $this->assertDatabaseHas('url_shorteners', ['short' => $shortener->short, 'counter' => $shortener->counter+1]);
    }

    public function test_it_can_detect_invalid_shortener(): void{

        $shortener = $this->urlShortenerService->encrypter($this->faker->url); 
        $short = explode('/',$shortener);
        $short = end($short);

        $this->get('/'.$short)->assertNotFound();

    }

    public function test_it_can_delete_shortener(): void{

        $this->withoutExceptionHandling();
        $this->actingAs(User::factory()->create());
        $shortener = UrlShortener::factory()->create();

        $short = explode('/',$shortener->short);
        $short = end($short);

        $this->deleteJson('/api/v2/shortener/'.$short)
        ->assertStatus(204)
        ->assertNoContent();

        $this->assertDatabaseMissing('url_shorteners', ['short' => $shortener->short]);

    }
}

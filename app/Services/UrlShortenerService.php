<?php

namespace App\Services;

use App\Traits\Encrypter;
use App\Models\UrlShortener;
use App\Interfaces\UrlShortenerInferface;

class UrlShortenerService implements UrlShortenerInferface{

  use Encrypter;

  private const SHORT_URL_LENGTH = 9;
  private const RANDOM_BYTES = 32;
  private $localhost;

  private $urlShortener;

  public function __construct() {
    $this->localhost = env('APP_URL');
    $this->urlShortener = new urlShortener;
  }

  public function is_encrypited($url): bool{
    return substr($url, 0, strlen($this->localhost)) == $this->localhost ? true : false;
  }

  public function generateShortUrl(string $url): array
  {

    $findFor = $this->is_encrypited($url) ? 'short' : 'long';
    $urlShortener = $this->urlShortener::where($findFor, $url);

    $generated = false;
    if($urlShortener->count()){
      $longUrl = $urlShortener->first()->long;
      $shortUrl = $urlShortener->first()->short;
    }else{
      $longUrl = $url;
      $shortUrl = $this->encrypter($url);
      $generated = $this->persistUrl($url, $shortUrl) ? true : false;
    }

    return [
      'long_url' => $longUrl,
      'short_url' => $shortUrl,
      'generated' => $generated
    ];

  }

  public function persistUrl(string $longUrl, string $shortenedUrl): bool
  {

    $this->urlShortener->user_id = auth()->user()->id;
    $this->urlShortener->long = $longUrl;
    $this->urlShortener->short = $shortenedUrl;
    $this->urlShortener->save();

    return (bool)$this->urlShortener;
  }


  public function redirectToOrigin(string $shortUrl): string|bool
  {

    $response = $this->urlShortener::where('short', '=', env('APP_URL').'/'.$shortUrl)->first();

    if(!$response){
      return false;
    }

    $this->urlShortener::where('long', $response['long'])->update(['counter' => $response['counter']+1]);

    return $response['long'];

  }

}
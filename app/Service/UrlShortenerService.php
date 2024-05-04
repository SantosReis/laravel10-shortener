<?php

namespace App\Service;

use App\Models\UrlShortener;

class UrlShortenerService {

  private const SHORT_URL_LENGTH = 9;
  private const RANDOM_BYTES = 32;
  private $localhost;

  public function __construct() {
    $this->localhost = env('APP_URL');
  }

  protected function encrypter(string $longUrl): string
  {
    $shortenedUrl = substr(
        base64_encode(
            sha1(uniqid(random_bytes(self::RANDOM_BYTES),true))
        ),
        0,
        self::SHORT_URL_LENGTH
    );

      return $this->localhost.'/'.$shortenedUrl;
  }

  public function is_encrypited($url){
    return substr($url, 0, strlen($this->localhost)) == $this->localhost ? true : false;
  }

  public function generateShortUrl(string $url): array
  {

    $findFor = $this->is_encrypited($url) ? 'short' : 'long';
    $urlShortener = UrlShortener::where($findFor, $url);

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

    $urlShortener = new UrlShortener;
    $urlShortener->long = $longUrl;
    $urlShortener->short = $shortenedUrl;
    $urlShortener->save();

    return (bool)$urlShortener;
  }

}
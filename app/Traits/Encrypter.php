<?php

namespace App\Traits;

trait Encrypter {
    
    public function encrypter(string $longUrl): string
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
    
}
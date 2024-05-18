<?php

namespace App\Interfaces;

interface UrlShortenerInferface
{
    public function generateShortUrl(string $url): array;
    public function redirectToOrigin(string $shortUrl): string|bool;
    public function persistUrl(string $longUrl, string $shortenedUrl): bool;
}
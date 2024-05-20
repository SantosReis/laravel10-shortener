<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\UrlShortener;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\UrlShortenerService;

class UrlShortenerController extends Controller
{
    use HttpResponses;

    private $urlShortenerService;

    private $localhost;

    public function __construct() {
        $this->urlShortenerService = new urlShortenerService();
        $this->localhost = env('APP_URL');
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);
        $urlShortener = $this->urlShortenerService->generateShortUrl($request->url);

        $response = array_merge(["status" => true], $urlShortener);
        return response()->json($response, 201);

    }

    public function list(): JsonResponse
    {
        $list = UrlShortener::take(5)
            ->where('user_id', auth()->user()->id)
            ->orderBy('id', 'DESC')
            ->get(['long', 'short']);
        return response()->json($list, 200);
    }

    public function redirect($shortener)
    {
        $origin = $this->urlShortenerService->redirectToOrigin($shortener);
        return $origin ? redirect($origin) : $this->error('', 'Not found', 404);
    }

    public function delete($shortenedUrl): JsonResponse
    {
        UrlShortener::where('short', $this->localhost.'/'.$shortenedUrl)
            ->where('user_id', auth()->user()->id)
            ->delete();

        return response()->json([], 204);
    }

}
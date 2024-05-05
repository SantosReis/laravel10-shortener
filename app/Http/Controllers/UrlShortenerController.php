<?php

namespace App\Http\Controllers;

use App\Models\UrlShortener;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Service\UrlShortenerService;

class UrlShortenerController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);
        $urlShortenerService = new UrlShortenerService();
        $urlShortener = $urlShortenerService->generateShortUrl($request->url);

        $response = array_merge(["status" => true], $urlShortener);
        return response()->json($response, 201);

    }
    public function list(Request $request): JsonResponse
    {
        $list = UrlShortener::take(5)->orderBy('id', 'DESC')->get();
        return response()->json($list, 200);
    }

    public function redirect($shortener){
        $response = UrlShortener::where('short', '=', env('APP_URL').'/'.$shortener)->first();

        return $response ? redirect($response->long) : $this->error('', 'Invalid address', 404);
        // return $response ? redirect($response->long) : abort(404);
    }
}
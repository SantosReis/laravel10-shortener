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

    public function __construct() {
    
        $this->urlShortenerService = new urlShortenerService();
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);
        $urlShortener = $this->urlShortenerService->generateShortUrl($request->url);

        $response = array_merge(["status" => true], $urlShortener);
        return response()->json($response, 201);

    }
    public function list(Request $request): JsonResponse
    {
        $list = UrlShortener::take(5)->orderBy('id', 'DESC')->get();
        return response()->json($list, 200);
    }

    public function redirect($shortener){
        
        $origin = $this->urlShortenerService->redirectToOrigin($shortener);
        return $origin ? redirect($origin) : $this->error('', 'Invalid address', 404);
        // return $response ? redirect($response->long) : abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $post = UrlShortener::find($id)->delete();
        return response()->json([], 204);
    }

}
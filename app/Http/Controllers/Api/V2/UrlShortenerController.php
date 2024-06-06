<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\UrlShortener;
use App\Services\UrlShortenerService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *    title="URL Shortener API",
 *    description="An URL shortener generator",
 *    version="1.0.0",
 * ),
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="token",
 *     name="Authorization"
 * )
 */
class UrlShortenerController extends Controller
{
    use HttpResponses;

    private object $urlShortenerService;

    private string $localhost;

    public function __construct()
    {
        $this->urlShortenerService = new urlShortenerService;
        $this->localhost = env('APP_URL');
    }

    /**
     *@OA\Post(
     *     path="/api/v2/shortener",
     *     summary="Generate URL shortener",
     *     tags={"Shortener"},
     *
     *     @OA\Response(
     *          response=201,
     *          description="URL Shortener created",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="long_url", type="string", example="http://www.marks.com/aut-perspiciatis-aperiam-animi-inventore-modi-dolore-aut"),
     *              @OA\Property(property="shor_url", type="string", example="ttp://localhost:8000/Njk5M2IxN"),
     *              @OA\Property(property="generated", type="boolean", example=false),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Invalid request",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="The url field is required."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="errors", type="object",
     *                      @OA\Property(property="url", type="string", example="The url field is required."),
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);
        $urlShortener = $this->urlShortenerService->generateShortUrl($request->url);

        $response = array_merge(['status' => true], $urlShortener);

        return response()->json($response, 201);
    }

    /**
     *@OA\Get(
     *     path="/api/v2/shortener",
     *     summary="Retrieve URL Shorteners",
     *     tags={"Shortener"},
     *
     *     @OA\Response(
     *          response=200,
     *          description="Valid request",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="0", type="object",
     *                  @OA\Property(property="long", type="string", example="http://www.murray.com/eos-quod-aperiam-ea-ipsam-aut-quia-corporis.html"),
     *                  @OA\Property(property="short", type="string", example="http://localhost:8000/ZGY0Y2RmM")
     *              ),
     *              @OA\Property(property="1", type="object",
     *                  @OA\Property(property="long", type="string", example="http://www.murray.com/eos-quod-aperiam-ea-ipsam-aut-quia-corporis.html"),
     *                  @OA\Property(property="short", type="string", example="http://localhost:8000/ZGY0Y2RmM")
     *              ),
     *                  @OA\Property(property="long", type="string", example="http://www.murray.com/eos-quod-aperiam-ea-ipsam-aut-quia-corporis.html"),
     *                  @OA\Property(property="short", type="string", example="http://localhost:8000/ZGY0Y2RmM")
     *              ),
     *              @OA\Property(property="3", type="object",
     *                  @OA\Property(property="long", type="string", example="http://www.murray.com/eos-quod-aperiam-ea-ipsam-aut-quia-corporis.html"),
     *                  @OA\Property(property="short", type="string", example="http://localhost:8000/ZGY0Y2RmM")
     *              ),
     *              @OA\Property(property="4", type="object",
     *                  @OA\Property(property="long", type="string", example="http://www.murray.com/eos-quod-aperiam-ea-ipsam-aut-quia-corporis.html"),
     *                  @OA\Property(property="short", type="string", example="http://localhost:8000/ZGY0Y2RmM")
     *              ),
     *          )
     *      ),
     * )
     */
    public function list(): JsonResponse
    {
        $list = UrlShortener::take(5)
            ->where('user_id', auth()->user()->id)
            ->orderBy('id', 'DESC')
            ->get(['long', 'short']);

        return response()->json($list, 200);
    }

    /**
     *@OA\Get(
     *     path="/api/v2/{shortened-url}",
     *     summary="Redirect to original source",
     *     tags={"Shortener"},
     *
     *@OA\Parameter(
     *          name="shortenedUrl",
     *          in="query",
     *          required=true,
     *          description="The self specific URL refer to this shortener",
     *
     *          @OA\Schema(
     *              type="string", example="ZGY0Y2RmM"
     *          ),
     *     ),
     *
     *@OA\Response(response=302, description="Found"),
     *@OA\Response(response=404, description="Not found")
     * )
     */
    public function redirect(string $shortener): string|array
    {
        $origin = $this->urlShortenerService->redirectToOrigin($shortener);

        return $origin ? redirect($origin) : $this->error('', 'Not found', 404);
    }

    /**
     *@OA\Delete(
     *     path="/api/v2/shortener/delete/{shortened-url}",
     *     summary="Remove a URL shortened",
     *     tags={"Shortener"},
     *
     *@OA\Parameter(
     *          name="shortenedUrl",
     *          in="query",
     *          required=true,
     *          description="The self specific URL refer to this shortener",
     *
     *          @OA\Schema(
     *              type="string", example="ZGY0Y2RmM"
     *          ),
     *     ),
     *
     *@OA\Response(response=204, description="No Content"),
     * )
     */
    public function delete(string $shortenedUrl): JsonResponse
    {
        UrlShortener::where('short', $this->localhost.'/'.$shortenedUrl)
            ->where('user_id', auth()->user()->id)
            ->delete();

        return response()->json([], 204);
    }
}

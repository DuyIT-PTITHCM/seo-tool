<?php

namespace App\Http\Controllers;


use App\Jobs\GetRankJob;
use App\Repositories\Contracts\MeasurementRepositoryInterface;
use App\Repositories\Contracts\RankingRepositoryInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Info(
 *      title="API Documentation",
 *      version="1.0.0",
 *      description="Documentation for the Ranking API",
 *      @OA\Contact(
 *          name="Nguyen Anh Duy",
 *          email="duynait@gmail.com"
 *      ),
 * )
 */
class RankingController extends Controller
{
    protected $measurementRepository;
    protected $rankingRepository;

    public function __construct(
        MeasurementRepositoryInterface $measurementRepository,
        RankingRepositoryInterface $rankingRepository
    ) {
        $this->measurementRepository = $measurementRepository;
        $this->rankingRepository = $rankingRepository;
    }

    private function checkUrlAvailability($url)
    {
        $client = new GuzzleHttpClient();
        try {
            $response = $client->get($url);
            $statusCode = $response->getStatusCode();
            return $statusCode === 200;
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1.0/ranking/get",
     *     summary="Get ranking",
     *     tags={"Ranking"},
     *     @OA\Parameter(
     *         name="url",
     *         in="query",
     *         description="URL",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="keywords",
     *         in="query",
     *         description="Keywords",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ranking created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid keywords. Maximum of 5 words, separated by new lines.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="URL not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="URL không tồn tại.")
     *         )
     *     )
     * )
     */
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'keywords' => ['required', 'string', 'regex:/^([^\r\n]+(\r?\n|$)){1,5}$/'],
        ], [
            'keywords.regex' => 'Invalid keywords. Maximum of 5 words, separated by new lines.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $url = $request->input('url');
        $keywords = explode("\n", $request->input('keywords'));

        $isUrlAvailable = $this->checkUrlAvailability($url);

        if (!$isUrlAvailable) {
            return response()->json(['message' => 'URL không tồn tại.'], 400);
        }

        $measurement = $this->measurementRepository->create(['url' => $url]);
        $totalKeywords = count($keywords);
        $completedKeywords = 0;

        foreach ($keywords as $keyword) {
            $completedKeywords += 1;
            $keyword = trim($keyword);
            if (!empty($keyword)) {
                DB::transaction(function () use ($measurement, &$completedKeywords, $totalKeywords, $keyword, $url) {
                    GetRankJob::dispatch($measurement->id, $keyword, $url, $totalKeywords, $completedKeywords)
                        ->onQueue('rankings')
                        ->onConnection('database')
                        ->afterCommit();
                });
            }
        }

        return response()->json(['id' => $measurement->id], 200);
    }
}

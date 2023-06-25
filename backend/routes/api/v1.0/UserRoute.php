<?php

use App\Events\RankingsCompleted;
use App\Http\Controllers\RankingController;
use App\Models\Ranking;
use Illuminate\Support\Facades\Route;
use Goutte\Client;
use Pusher\Pusher;


Route::group(['middleware' => [], 'prefix' => 'ranking'], function () use ($router) {
    $router->get('get', [RankingController::class, 'get']);
});
Route::get('/search', function () {
    $numberOfPages = 3;
    $keyword = 'xe nâng dầu';
    $urlIn = 'https://xenangbinhminh.net/';
    $rank = 0;
    $searchResults = [];
    $linkDescription = '';
    $client = new Client();

    for ($page = 1; $page <= $numberOfPages; $page++) {
        if ($rank) {
            break;
        }
        $url = 'https://search.yahoo.com/search?q=' . urlencode($keyword) . '&start=' . (($page - 1) * 10);

        $crawler = $client->request('GET', $url);
        $results = $crawler->filter('div.dd.algo');
        $results->each(function ($node) use (&$searchResults, &$urlIn, &$rank, &$linkDescription) {
            $title = $node->filter('a')->text();
            $href = $node->filter('a')->attr('href');

            $searchResults[] = [
                'title' => $title,
                'url' => $href
            ];
            dump($href);
            dump($urlIn);
            if (strstr($href, $urlIn)) {
                dd(1);
                $rank = sizeof($searchResults);
                $linkDescription = $title;
                return false;
            }
        });
    }
    // dump($searchResults);
    return [
        'rank' => $rank,
        'searchResultsLink' => $linkDescription
    ];
});
Route::get('/chat', function () {
    return  Ranking::leftJoin('rankings AS r2', function ($join) {
        $join->on('rankings.keyword', '=', 'r2.keyword')
            ->where('r2.search_engine', '=', 'yahoo')
            ->where('r2.measurement_id', '=', 82);
    })
        ->where('rankings.measurement_id', 82)
        ->where('rankings.search_engine', 'google')
        ->select('rankings.keyword', 'rankings.rank AS googleRank', 'rankings.search_results_link AS googleLink', 'r2.rank AS yahooRank', 'r2.search_results_link AS yahooLink')
        ->get();
});

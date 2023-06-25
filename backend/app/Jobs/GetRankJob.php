<?php

namespace App\Jobs;

use App\Events\RankingsCompleted;
use App\Models\RankingCallback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Goutte\Client;

class GetRankJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $measurementId;
    protected $keyword;
    protected $url;
    protected $totalKeywords;
    protected $completedKeywords;

    public function __construct($measurementId, $keyword, $url, $totalKeywords, $completedKeywords)
    {
        $this->measurementId = $measurementId;
        $this->keyword = $keyword;
        $this->url = $url;
        $this->totalKeywords = $totalKeywords;
        $this->completedKeywords = $completedKeywords;
    }

    public function handle()
    {
        $dataGoogle = $this->getRankFromSearchEngines($this->url, $this->keyword, 'google');
        $dataYahoo = $this->getRankFromSearchEngines($this->url, $this->keyword, 'yahoo');

        RankingCallback::handle($this->measurementId, $this->keyword, $dataGoogle['rank'], 'google', $dataGoogle['searchResultsLink']);
        RankingCallback::handle($this->measurementId, $this->keyword, $dataYahoo['rank'], 'yahoo', $dataYahoo['searchResultsLink']);
        if ($this->totalKeywords == $this->completedKeywords) {
            event(new RankingsCompleted($this->measurementId));
        }
    }

    private function getRankFromSearchEngines($urlIn, $keyword, $searchEngine)
    {
        $numberOfPages = 3;
        if ($searchEngine == 'google') {
            $rank = 0;
            $linkDescription = '';
            $numberOfPages = 3; 

            $searchResults = [];
            $client = new Client();

            for ($page = 1; $page <= $numberOfPages; $page++) {
                if ($rank) {
                    break;
                }
                $url = 'https://www.google.com/search?q=' . urlencode($keyword) . '&start=' . (($page - 1) * 10);

                $crawler = $client->request('GET', $url);
                $results = $crawler->filter('a[href]');
                $results->each(function ($node) use (&$searchResults, &$urlIn, &$rank, &$linkDescription) {
                    $href = $node->attr('href');
                    if (strpos($href, '/url?') !== false && strpos($href, 'support.google.com') == false && strpos($href, 'accounts.google.com') == false) {
                        $title = $node->text();

                        $searchResults[] = [
                            'title' => $title,
                            'url' => $href
                        ];

                        if (strpos($href, $urlIn) == true) {
                            $rank = sizeof($searchResults);
                            $linkDescription = 'https://www.google.com' . $href;
                            return false;
                        }
                    }
                });
            }
            return [
                'rank' => $rank,
                'searchResultsLink' => $linkDescription
            ];
        } else if ($searchEngine == 'yahoo') {
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

                    if (strstr($href, $urlIn)) {
                        $rank = sizeof($searchResults);
                        $linkDescription = $href;
                        return false;
                    }
                });
            }
            return [
                'rank' => $rank,
                'searchResultsLink' => $linkDescription
            ];
        }
    }
}

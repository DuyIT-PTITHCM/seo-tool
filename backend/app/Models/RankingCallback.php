<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankingCallback extends Model
{
    use HasFactory;

    public static function handle($measurementId, $keyword, $rank, $searchEngine, $searchResultsLink)
    {
        Ranking::create([
            'measurement_id' => $measurementId,
            'keyword' => $keyword,
            'rank' => $rank,
            'search_engine' => $searchEngine,
            'search_results_link' => $searchResultsLink,
        ]);
    }
}

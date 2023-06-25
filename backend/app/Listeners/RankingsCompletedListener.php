<?php

namespace App\Listeners;

use App\Events\RankingsCompleted;
use App\Models\Ranking;
use Pusher\Pusher;

class RankingsCompletedListener
{

    /**
     * Handle the event.
     */
    public function handle(RankingsCompleted $event)
    {

        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('my-channel-' . $event->id, 'ranking-complete', [
            'data' => Ranking::leftJoin('rankings AS r2', function ($join) use ($event) {
                $join->on('rankings.keyword', '=', 'r2.keyword')
                    ->where('r2.search_engine', '=', 'yahoo')
                    ->where('r2.measurement_id', '=', $event->id);
            })
                ->where('rankings.measurement_id', $event->id)
                ->where('rankings.search_engine', 'google')
                ->select('rankings.keyword', 'rankings.rank AS googleRank', 'rankings.search_results_link AS googleLink', 'r2.rank AS yahooRank', 'r2.search_results_link AS yahooLink')
                ->get()
        ]);
    }
}

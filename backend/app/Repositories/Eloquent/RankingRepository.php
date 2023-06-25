<?php

namespace App\Repositories\Eloquent;

use App\Models\Ranking;
use App\Repositories\Contracts\RankingRepositoryInterface;

class RankingRepository extends EloquentRepository implements RankingRepositoryInterface
{
    public function __construct(Ranking $model)
    {
        parent::__construct($model);
    }
}

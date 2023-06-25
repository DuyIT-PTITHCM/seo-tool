<?php

namespace App\Repositories\Eloquent;

use App\Models\Measurement;
use App\Repositories\Contracts\MeasurementRepositoryInterface;

class MeasurementRepository extends EloquentRepository implements MeasurementRepositoryInterface
{
    public function __construct(Measurement $model)
    {
        parent::__construct($model);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'measurement_id',
        'keyword',
        'search_engine',
        'rank',
        'search_results_link'
    ];

    public function measurement()
    {
        return $this->belongsTo(Measurement::class, 'measurement_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
    ];

    public function rankings()
    {
        return $this->hasMany(Ranking::class, 'measurement_id', 'id');
    }
}

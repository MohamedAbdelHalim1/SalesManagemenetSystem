<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OpenClose; // Import the OpenClose model

class Coin extends Model
{
    protected $fillable = [
        'coin_0_5',
        'coin_1',
        'coin_10',
        'coin_20',
        'coin_50',
        'coin_100',
        'coin_200',
        'open_close_id'
    ];

    public function open_close(){
        return $this->belongsTo(OpenClose::class , 'open_close_id');
    }
}

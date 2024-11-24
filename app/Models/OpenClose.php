<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coin; // Import the Coin model

class OpenClose extends Model
{
    protected $fillable = [
        'user_id',
        'open_at',
        'close_at',
        'pending_close'
    ];

    protected $table = 'open_closes';

    public function transactions(){
        return $this->hasMany(Transaction::class , 'open_close_id');
    }

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function coin(){
        return $this->hasOne(Coin::class , 'open_close_id');
    }

}

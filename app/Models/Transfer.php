<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    
    protected $fillable = [
        'transfer_key',
        'transfer_value',
        'transaction_id',
        'image',
    ];

    public function transaction(){
        return $this->belongsTo(Transaction::class , 'transaction_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'reference_collection',
        'order_number',
        'order_delivered',
        'total_cash',
        'sales_commission',
        'total_remaining',
        'user_id',
        'open_close_id',
    ];

    

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function openclose(){
        return $this->belongsTo(OpenClose::class , 'open_close_id');
    }

    public function transfers(){
        return $this->hasMany(Transfer::class , 'transaction_id');
    }
    public function expenses(){
        return $this->hasMany(Expenses::class , 'transaction_id');
    }
    
}

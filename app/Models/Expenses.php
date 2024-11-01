<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    protected $table = "expenses";
    protected $fillable = [
        'expenses_key',
        'expenses_value',
        'transaction_id',

    ];

    public function transaction(){
        return $this->belongsTo(Transaction::class , 'transaction_id');
    }

}

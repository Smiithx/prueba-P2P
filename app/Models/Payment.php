<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payments";
    protected $appends = ["amount_format"];

    public function getAmountFormatAttribute()
    {
        return "$this->currency $ ".number_format($this->amount,2);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}

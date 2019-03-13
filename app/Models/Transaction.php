<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $table = "transactions";

    protected $appends = ["amount_format","discount_format"];

    public function getAmountFormatAttribute()
    {
        return "$this->currency $ ". number_format($this->amount,2);
    }

    public function getDiscountFormatAttribute()
    {
        return "$this->currency $ ".number_format($this->discount,2);
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_amount',
        'balance',
        'remarks', 
        'payment_date',
    ];

    public $timestamps = false;


    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function loan()
    {
    	return $this->belongsTo(Loan::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'arrangement_fee', 
        'interest_rate',
        'term', 
        'frequency',
    ];


    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function repayments()
    {
    	return $this->hasMany(Repayment::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $table = 'distributors';

    protected $fillable = [
        'name',
        'city',
        'phone',
        'state',
        'notes',
    ];

    // use default timestamps

    public function coupons()
    {
      return $this->hasMany(DistributorCoupon::class);
    }



}
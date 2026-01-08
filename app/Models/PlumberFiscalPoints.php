<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlumberFiscalPoints extends Model
{
    protected $fillable = [
        'plumber_id',
        'fiscal_year_id',
        'gift_points',
        'fixed_points',
        'instant_withdrawal',
        'withdraw_money'
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function plumber()
    {
        return $this->belongsTo(User::class, 'plumber_id');
    }
}

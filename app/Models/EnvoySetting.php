<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvoySetting extends Model
{
    protected $table = 'envoy_settings';

    protected $fillable = [
        'user_id',
        'weight_sales',
        'weight_visits',
        'weight_retention_rate',
        'weight_conversion_rate',
        'target_sales',
        'target_visits',
        'target_retention_rate',
        'target_conversion_rate',
        'salary',
        'incentives',
        'region',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

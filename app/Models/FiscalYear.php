<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    protected $fillable = ['year', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function inspectionRequests()
    {
        return $this->hasMany(InspectionRequest::class);
    }
}

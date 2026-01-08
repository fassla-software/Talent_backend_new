<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionRequestItem extends Model
{
    protected $table = 'inspection_requests_items';

    protected $fillable = [
        'inspection_request_id',
        'subcategory_id',
        'count',
    ];

    public $timestamps = true;

    // Optional: relationship to product name
    public function subcategory()
    {
        return $this->belongsTo(PlumberCategory::class, 'subcategory_id');
    }
}

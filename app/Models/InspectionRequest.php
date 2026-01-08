<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionRequest extends Model
{
    protected $table = 'inspection_requests'; // Table name from your database
    public $timestamps = true;

    // Prevent Laravel from trying to insert/update/delete
    public $incrementing = false;
    protected $guarded = [];
    
    // Optional: Prevent accidental writes (strict mode)
    protected static function boot()
    {
        parent::boot();

        static::saving(function () {
            return false;
        });

        static::creating(function () {
            return false;
        });

        static::updating(function () {
            return false;
        });

        static::deleting(function () {
            return false;
        });
    }
}

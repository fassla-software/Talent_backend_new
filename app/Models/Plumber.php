<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plumber extends Model
{
    protected $table = 'plumbers';

    protected $fillable = [
        'user_id',
        'gift_points',
        'fixed_points',
        'nationality_id',
        'inspector_id',
        'city',
        'area',
        'nationality_image1',
        'nationality_image2',
        'is_verified',
        'status',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
public function plumber()
{
    return $this->belongsTo(Plumber::class, 'plumber_id');
}

}

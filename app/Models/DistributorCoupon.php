<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'code',
        'sales_value',
        'points',
        'status',
        'area_name',
        'expired_at',
        'used_by',
        'is_printed',
    ];

    // العلاقة مع جدول المندوبين
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    // Relationship to the user who used the coupon
    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}

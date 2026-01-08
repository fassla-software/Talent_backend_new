<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trader extends Model
{
    use HasFactory;

    protected $table = 'traders';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'city',
        'area',
        'nationality_id',
        'nationality_image1',
        'nationality_image2',
        'image',
        'is_verified',
        'otp',
        'expiration_date',
        'instant_withdrawal',
        'withdraw_money',
        'points',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expiration_date' => 'datetime',
    ];

    /**
     * Example relationship:
     * A trader belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to coupons used by this trader.
     */
    public function usedCoupons()
    {
        return $this->hasMany(DistributorCoupon::class, 'used_by', 'user_id');
    }

    /**
     * Get the total sales value from all used coupons.
     */
    public function getTotalSalesValue()
    {
        return $this->usedCoupons()->sum('sales_value');
    }

    public function getTotalCouponsCount()
    {
        return $this->usedCoupons()->count();
    }
    /**
     * Get the current level based on total sales value.
     */
    public function getCurrentLevel()
    {
        $totalCoupons = $this->getTotalCouponsCount();

        // Find the level where total sales falls within min_sales and max_sales
        // If total sales exceeds all levels, return the highest level
        $level = \App\Models\Level::where('min_sales', '<=', $totalCoupons)
            ->where('max_sales', '>=', $totalCoupons)
            ->first();

        if (!$level) {
            // If no level matches, check if total sales exceeds the highest level
            $highestLevel = \App\Models\Level::orderBy('max_sales', 'desc')->first();
            if ($highestLevel && $totalCoupons > $highestLevel->max_sales) {
                return $highestLevel;
            }
        }

        return $level;
    }
}

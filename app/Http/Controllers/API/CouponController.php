<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyCouponRequest;
use App\Http\Requests\VoucherRequest;
use App\Http\Resources\CouponResource;
use App\Repositories\CouponCollectRepository;
use App\Repositories\CouponRepository;
use Illuminate\Http\Request;
use App\Models\DistributorCoupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Trader;
use App\Models\Level;
class CouponController extends Controller
{
    /**
     * get shop voucher from shop
     */
    public function index(VoucherRequest $request)
    {
        $shopId = $request->shop_id;

        $coupons = CouponRepository::query()->whereShopId($shopId)->Active()->isValid()->get();

        // $coupons = CouponRepository::query()->whereShopId($shopId)
        //     ->orWhereHas('shops', function ($query) use ($shopId) {
        //         $query->where('id', $shopId);
        //     })->Active()->isValid()->get();

        return $this->json('Shop vouchers', [
            'coupons' => CouponResource::collection($coupons),
        ]);
    }

    /**
     * collect voucher
     * */
    public function store(VoucherRequest $request)
    {
        $hasExistCoupon = CouponCollectRepository::hasExistCoupon($request);

        if ($hasExistCoupon) {
            return $this->json('Voucher already collected');
        }

        $coupon = CouponCollectRepository::storeByRequest($request);

        return $this->json('Voucher collected successfully', [
            'coupon' => CouponResource::make($coupon->coupon),
        ]);
    }

    /**
     * get collected vouchers
     *
     * @param  VoucherRequest  $request
     * */
    public function collectedVouchers(Request $request)
    {
        // get shop id
        $shopId = $request->shop_id;

        // get collected vouchers from repository
        $coupons = CouponRepository::getCollectedCoupons($shopId);

        return $this->json('available collected vouchers', [
            'coupons' => CouponResource::collection($coupons),
        ]);
    }

    /**
     * Apply voucher from user collected vouchers
     */
    public function applyVoucher(ApplyCouponRequest $request)
    {
        $couponDiscount = CouponRepository::getCouponDiscount($request);

        $message = $couponDiscount['discount_amount'] > 0 ? 'Voucher applied successfully' : 'Voucher not applied';

        $status = $couponDiscount['discount_amount'] > 0 ? 200 : 201;

        return $this->json($message, [
            'total_order_amount' => (float) number_format($couponDiscount['total_amount'], 2, '.', ''),
            'total_discount_amount' => (float) number_format($couponDiscount['discount_amount'], 2, '.', ''),
        ], $status);
    }

    /**
     * Apply coupon from coupon code
     * */
    public function getDiscount(ApplyCouponRequest $request)
    {
        $couponDiscount = CouponRepository::getCouponDiscount($request);

        $message = $couponDiscount['discount_amount'] > 0 ? 'Voucher applied successfully' : 'Voucher not applied';

        $status = $couponDiscount['discount_amount'] > 0 ? 200 : 201;

        return $this->json($message, [
            'total_order_amount' => (float) round($couponDiscount['total_amount'], 2),
            'total_discount_amount' => (float) round($couponDiscount['discount_amount'], 2),
        ], $status);
    }

    // public function redeem(Request $request)
    // {
    //     $data = $request->validate([
    //         'code' => 'required|string|exists:distributor_coupons,code',
    //     ]);

    //     // Find the coupon by code
    //     $coupon = DistributorCoupon::where('code', $data['code'])->first();
    //     if (!$coupon) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Coupon not found.',
    //         ], 404);
    //     }
    //     // Check if the coupon is already redeemed or expired
    //     if ($coupon->status !== 'active') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Coupon already uesed or expired.',
    //         ], 400);
    //     }
    //     // Update the coupon status to redeemed
    //     $coupon->status = 'used';
    //     $coupon->used_at = now();
    //     // $coupon->used_by = $user->id; // Set the user who used the coupon
    //     $coupon->save();

    //     // get log in user search for trader

    //      $user = auth()->user();
    //     $trader = Trader::where('user_id', $user->id)->first();
    //     $trader->points = $trader->points + $coupon->points;
    //     $trader->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Coupon used successfully.',
    //         'points'  => $coupon->points,
    //     ], 200);
    // }

    public function redeem(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|exists:distributor_coupons,code',
        ]);

        // get authenticated user (works with standard auth or JWT middleware)
        $auth = $request->auth;
        $userId = $auth->id;

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        DB::beginTransaction();
        try {
            // lock row to avoid race conditions
            $coupon = DistributorCoupon::where('code', $data['code'])->lockForUpdate()->first();

            if (!$coupon) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Coupon not found.'], 404);
            }

            if ($coupon->status !== 'active') {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Coupon already used or expired.'], 400);
            }

            $coupon->status = 'used';
            $coupon->used_at = now();
            $coupon->used_by = $userId;
            $coupon->save();

            $trader = Trader::where('user_id', $userId)->first();
            if ($trader) {
                $trader->increment('points', $coupon->points);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Coupon used successfully.',
                'points'  => $coupon->points,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.'], 500);
        }
    }

    public function getTraderPoints(Request $request)
    {
        $auth = $request->auth;
        $userId = $auth->id;
        $trader = Trader::where('user_id', $userId)->first();

        if (!$trader) {
            return response()->json([
                'success' => false,
                'message' => 'Trader not found',
            ], 404);
        }

        $currentLevel = $trader->getCurrentLevel();

        // Retrieve all levels with min_sales as min_coupon and max_sales as max_coupon
        $levels = Level::all()->map(function ($level) {
            return [
                'level' => $level->level,
                'min_coupon' => $level->min_sales,
                'max_coupon' => $level->max_sales,
            ];
        });

        return response()->json([
            'success' => true,
            'coupons' => $trader->usedCoupons()->count(),
            'current_level' => $currentLevel ? $currentLevel->level : null,
            'levels' => $levels,
        ], 200);
    }

    
}

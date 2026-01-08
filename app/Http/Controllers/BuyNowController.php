<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Requests\OrderRequest;
use App\Models\Product;
use App\Repositories\CouponRepository;
use App\Repositories\OrderRepository;
use App\Repositories\VatTaxRepository;
use Illuminate\Http\Request;

class BuyNowController extends Controller
{
    /**
     * Handle the 'Buy Now' request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyNow(Request $request)
    {
        // START: New buyNow calculations
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string',
        ]);

        $productId = $validatedData['product_id'];
        $quantity = $validatedData['quantity'];
        $couponCode = $validatedData['coupon_code'] ?? null;

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Calculate product price
        $price = $product->discount_price > 0 ? $product->discount_price : $product->price;

        // Check for flashsale
        $flashsale = $product->flashSales?->first();
        if ($flashsale) {
            $flashsaleProduct = $flashsale->products()->where('id', $product->id)->first();
            if ($flashsaleProduct && ($flashsaleProduct->pivot->quantity - $flashsaleProduct->pivot->sale_quantity) > 0) {
                $price = $flashsaleProduct->pivot->price;
            }
        }

        // Add VAT taxes
        $priceTaxAmount = 0;
        foreach ($product->vatTaxes ?? [] as $tax) {
            if ($tax->percentage > 0) {
                $priceTaxAmount += $price * ($tax->percentage / 100);
            }
        }
        $price += $priceTaxAmount;

        // total_amount = price * quantity
        $totalAmount = $price * $quantity;

        // delivery_charge
        $deliveryCharge = getDeliveryCharge($quantity);

        // coupon_discount
        $couponDiscount = 0;
        $applyCoupon = false;
        if ($couponCode) {
            $productsArray = (object) [
                'coupon_code' => $couponCode,
                'products' => [
                    [
                        'id' => $productId,
                        'quantity' => $quantity,
                        'shop_id' => $product->shop_id,
                    ]
                ],
            ];
            $getDiscount = CouponRepository::getCouponDiscount($productsArray);
            $couponDiscount = $getDiscount['discount_amount'];
            $applyCoupon = $couponDiscount > 0;
        }

        // order_tax_amount
        $orderTaxAmount = 0;
        $orderBaseTax = VatTaxRepository::getOrderBaseTax();
        if ($orderBaseTax && $orderBaseTax->deduction == \App\Enums\DeductionType::EXCLUSIVE->value && $orderBaseTax->percentage > 0) {
            $orderTaxAmount = $totalAmount * ($orderBaseTax->percentage / 100);
        }

        // total_payable_amount = total_amount + delivery_charge - coupon_discount + order_tax_amount
        $totalPayableAmount = $totalAmount + $deliveryCharge - $couponDiscount + $orderTaxAmount;

        // gift_charge = 0 for buyNow
        $giftCharge = 0;

        return response()->json([
            'total_amount' => (float) round($totalAmount, 2),
            'total_payable_amount' => (float) round($totalPayableAmount, 2),
            'coupon_discount' => (float) round($couponDiscount, 2),
            'delivery_charge' => (float) round($deliveryCharge, 2),
            'apply_coupon' => $applyCoupon,
            'gift_charge' => (float) round($giftCharge, 2),
            'total_tax_amount' => (float) round($orderTaxAmount, 2),
        ]);
        // END: New buyNow calculations
    }

    /**
     * Place order for Buy Now
     *
     * @param  \App\Http\Requests\OrderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeOrder(OrderRequest $request)
    {
        // Set is_buy_now to true
        $request->merge(['is_buy_now' => true]);

        $carts = auth()->user()->customer->carts()->whereIn('shop_id', $request->shop_ids)->where('is_buy_now', true)->get();

        if ($carts->isEmpty()) {
            return $this->json('Sorry shop cart is empty', [], 422);
        }

        $toUpper = strtoupper($request->payment_method);
        $paymentMethods = PaymentMethod::cases();

        $paymentMethod = $paymentMethods[array_search($toUpper, array_column(PaymentMethod::cases(), 'name'))];

        // Store the order
        $payment = OrderRepository::storeByrequestFromCart($request, $paymentMethod, $carts);

        $paymentUrl = null;
        if ($paymentMethod->name != 'CASH') {
            $paymentUrl = route('order.payment', ['payment' => $payment, 'gateway' => $request->payment_method]);
        }

        return $this->json('Order created successfully', [
            'order_payment_url' => $paymentUrl,
        ]);
    }
}

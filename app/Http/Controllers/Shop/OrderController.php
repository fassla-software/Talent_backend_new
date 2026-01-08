<?php

namespace App\Http\Controllers\Shop;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Order;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use App\Services\NotificationServices;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use ArPHP\I18N\Arabic;

class OrderController extends Controller
{
    /**
     * Display the order list with filter status.
     */
    public function index($status = null)
    {
        $status = $status ? str_replace('_', ' ', $status) : '';

        $shop = generaleSetting('shop');

        $orders = $shop?->orders()->when($status, function ($query) use ($status) {
            $query->where('order_status', $status);
        })->latest('id')->paginate(20);

        return view('shop.order.index', compact('orders', 'status'));
    }

    /**
     * Display the order details.
     */
    public function show($orderId)
    {
        $order = OrderRepository::query()->withoutGlobalScopes()->whereId($orderId)->firstOrFail();

        $orderStatus = OrderStatus::cases();

        $riders = Driver::whereHas('user', function ($query) {
            return $query->where('is_active', true);
        })->get();

        return view('shop.order.show', compact('order', 'orderStatus', 'riders'));
    }

    /**
     * Update the order status.
     */
    public function statusChange(Order $order, Request $request)
    {
        $request->validate(['status' => 'required']);

        $order->update(['order_status' => $request->status]);

        if ($request->status == OrderStatus::DELIVERED->value) {
            $this->updateWalletAndTransaction($order);
        }

        if ($request->status == OrderStatus::CANCELLED->value) {
            foreach ($order->products as $product) {

                $qty = $product->pivot->quantity;

                $product->update(['quantity' => $product->quantity + $qty]);

                $flashsale = $product->flashSales?->first();
                $flashsaleProduct = null;

                if ($flashsale) {
                    $flashsaleProduct = $flashsale?->products()->where('id', $product->id)->first();

                    if ($flashsaleProduct && $product->pivot?->price) {
                        if ($flashsaleProduct->pivot->sale_quantity >= $qty && ($product->pivot?->price == $flashsaleProduct->pivot->price)) {
                            $flashsale->products()->updateExistingPivot($product->id, [
                                'sale_quantity' => $flashsaleProduct->pivot->sale_quantity - $qty,
                            ]);
                        }
                    }
                }
            }
        }

        $title = 'Order status updated';
        $message = 'Your order status updated to '.$request->status.' order code: '.$order->prefix.$order->order_code;
        $deviceKeys = $order->customer->user->devices->pluck('key')->toArray();

        try {
            NotificationServices::sendNotification($message, $deviceKeys, $title);
        } catch (\Throwable $th) {
        }

        $nofify = (object) [
            'title' => $title,
            'content' => $message,
            'user_id' => $order->customer->user_id,
            'type' => 'order',
        ];
        NotificationRepository::storeByRequest($nofify);

        return back()->with('success', __('Order status updated successfully.'));
    }

    /**
     * Update the payment status.
     */
    public function paymentStatusToggle(Order $order)
    {
        if ($order->payment_status->value == PaymentStatus::PAID->value) {
            return back()->with('error', __('When order is paid, payment status cannot be changed.'));
        }
        $order->update(['payment_status' => PaymentStatus::PAID->value]);

        return back()->with('success', __('Payment status updated successfully'));
    }

    public function downloadInvoice($id)
    {
        $order = Order::withoutGlobalScopes()->findOrFail($id);
        $orderCode = '#' . $order->prefix . $order->order_code;

        // Generate QR code
        $qrCode = new EndroidQrCode($orderCode);
        $qrCode->setSize(100);
        $writer = new PngWriter;
        $qrCodeImage = $writer->write($qrCode)->getDataUri();

        $generaleSetting = generaleSetting('setting');
        $logoPath = $generaleSetting?->favicon 
            ? public_path($generaleSetting->favicon) 
            : public_path('assets/favicon.png');
        
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $imageData = file_get_contents($logoPath);
            $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
        }

        // Step 1: Render Blade HTML
        $reportHtml = view('PDF.invoice', compact('order', 'qrCodeImage', 'logoBase64', 'generaleSetting'))->render();

        // Step 2: Arabic text shaping (fixes reversed + separated letters)
        $arabic = new Arabic();
        $positions = $arabic->arIdentify($reportHtml);

        for ($i = count($positions) - 1; $i >= 0; $i -= 2) {
            $utf8ar = $arabic->utf8Glyphs(substr($reportHtml, $positions[$i - 1], $positions[$i] - $positions[$i - 1]));
            $reportHtml = substr_replace($reportHtml, $utf8ar, $positions[$i - 1], $positions[$i] - $positions[$i - 1]);
        }

        // Step 3: Generate PDF using DomPDF
        $pdf = PDF::loadHTML($reportHtml)
            ->setPaper('a4', 'portrait');

        // Step 4: Return as download
        $fileName = 'invoice-' . $order->prefix . $order->order_code . '.pdf';
        return $pdf->download($fileName);
    }

    private function updateWalletAndTransaction($order)
    {

        $generaleSetting = generaleSetting('setting');

        $commission = 0;

        if ($generaleSetting?->commission_charge != 'monthly') {

            if ($generaleSetting?->commission_type != 'fixed') {
                $commission = $order->total_amount * $generaleSetting->commission / 100;
            } else {
                $commission = $generaleSetting->commission ?? 0;
            }
        }

        $order->update([
            'delivery_date' => now(),
            'delivered_at' => now(),
            'payment_status' => PaymentStatus::PAID->value,
            'admin_commission' => $commission,
        ]);

        $wallet = $order->shop->user->wallet;

        WalletRepository::updateByRequest($wallet, $order->payable_amount, 'credit');

        TransactionRepository::storeByRequest($wallet, $commission, 'debit', true, true, 'admin commission', 'order');
    }
}

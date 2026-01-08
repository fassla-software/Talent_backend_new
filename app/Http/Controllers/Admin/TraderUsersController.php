<?php

namespace App\Http\Controllers\Admin;

use App\Models\Trader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\User;

class TraderUsersController extends Controller
{
    public function index(Request $request)
    {
        // Get filters from the request
        $statusFilter = $request->input('status');
        $nationalityIdFilter = $request->input('nationality_id');
        $nameFilter = $request->input('name');
        $cityFilter = $request->input('city');
        $phoneFilter = $request->input('phone');
        $monthFilter = $request->input('created_month');

        // Retrieve all unique cities before applying filters
        $allCities = Trader::pluck('city')->unique()->sort();

        // Retrieve unique months from the created_at column
        $availableMonths = Trader::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        // Retrieve traders and apply filters
        $traders = Trader::with('user')
            ->select([
                'id',
                'user_id',
                'city',
                'area',
                'nationality_id',
                'nationality_image1',
                'nationality_image2',
                'is_verified',
                'otp',
                'expiration_date',
                'instant_withdrawal',
                'points',
                'created_at',
                'updated_at',
                'image',
                'withdraw_money'
            ])
            ->when($statusFilter, function ($query) use ($statusFilter) {
                return $query->whereHas('user', function ($query) use ($statusFilter) {
                    $query->where('status', $statusFilter);
                });
            })
            ->when($nationalityIdFilter, function ($query) use ($nationalityIdFilter) {
                return $query->where('nationality_id', $nationalityIdFilter);
            })
            ->when($cityFilter, function ($query) use ($cityFilter) {
                return $query->where('city', 'LIKE', '%' . $cityFilter . '%');
            })
            ->when($nameFilter, function ($query) use ($nameFilter) {
                return $query->whereHas('user', function ($query) use ($nameFilter) {
                    $query->where('name', 'LIKE', '%' . $nameFilter . '%');
                });
            })
            ->when($phoneFilter, function ($query) use ($phoneFilter) {
                return $query->whereHas('user', function ($query) use ($phoneFilter) {
                    $query->where('phone', 'LIKE', '%' . $phoneFilter . '%');
                });
            })
            ->when($monthFilter, function ($query) use ($monthFilter) {
                return $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$monthFilter]);
            })
            ->latest()
            ->paginate(20);

        return view('admin.traderUsers', compact('traders', 'allCities', 'availableMonths'));
    }

    public function approve(Request $request, $id)
    {
        // Find the trader by ID and load the related user
        $trader = Trader::with('user')->findOrFail($id);

        if (!$trader->user) {
            return redirect()->route('admin.traders')->with('error', 'Associated user not found.');
        }

        $userId = $trader->user->id;
        $phone = $trader->user->phone; // Get trader's phone number

        // Send request to approve trader via Node.js API
        $response = Http::put("https://app.talentindustrial.com/plumber/trader/{$userId}/accept");

        if ($response->successful()) {
            // Send SMS Notification
            $message = "تهانينا! تم تفعيل حسابك في برنامج “شكرًا شركاء النجاح”. ابدأ الآن بكسب النقاط والاستفادة من المزايا الحصرية!";
            $this->sendSMS($phone, $message);

            return redirect()->route('admin.traders')->with('success', 'Trader approved successfully.');
        }

        return redirect()->route('admin.traders')->with('error', 'Failed to approve trader.');
    }

    public function reject(Request $request, $id)
    {
        // Find the trader by ID and load the related user
        $trader = Trader::with('user')->findOrFail($id);

        if (!$trader->user) {
            return redirect()->route('admin.traders')->with('error', 'Associated user not found.');
        }

        $userId = $trader->user->id;
        $phone = $trader->user->phone; // Get trader's phone number

        // Send request to reject trader via Node.js API
        $response = Http::put("https://app.talentindustrial.com/plumber/trader/{$userId}/reject");

        if ($response->successful()) {
            // Send SMS Notification
            $message = "نأسف لعدم تفعيل حسابك في برنامج “شكرًا شركاء النجاح”، يرجى استكمال بياناتك لإتمام التفعيل. لمزيد من التفاصيل، تواصل معنا.";
            $this->sendSMS($phone, $message);

            return redirect()->route('admin.traders')->with('success', 'Trader rejected successfully.');
        }

        return redirect()->route('admin.traders')->with('error', 'Failed to reject trader.');
    }

    private function sendSMS($phone, $message)
    {
        if (!$phone) {
            \Log::error("SMS Error: No phone number provided.");
            return false;
        }

        // Ensure phone number starts with "20"
        $formattedPhone = preg_replace('/^0/', '20', $phone);

        // API Credentials (Hardcoded)
        $smsUrl = "https://smsmisr.com/api/sms";
        $smsUsername = "389a30b9f671853179aa4dc08e2c25f2615c48351c59998ca8b475bf667b4823";
        $smsPassword = "86203bf1ae7ee0890041b0e62d4c4533edb5a015504aef0a54e9690ef7a81d5f";
        $smsSender = "aa1b696074eaec8c2063a0d1c394f1d2f35aaffd430399229738916de26b3900";

        // Make API request
        $response = Http::post($smsUrl, [
            'environment' => '1', // 1 for Live, 2 for Test
            'username'    => $smsUsername,
            'password'    => $smsPassword,
            'sender'      => $smsSender,
            'mobile'      => $formattedPhone,
            'language'    => '2', // 1 for English, 2 for Arabic
            'message'     => $message,
        ]);

        // Get API Response
        $responseData = $response->json();

        // Log response for debugging
        \Log::info("SMS API Response: " . json_encode($responseData));

        // Check if SMS was successfully sent
        if (isset($responseData['code']) && $responseData['code'] == "1901") {
            return true;
        }

        \Log::error("SMS Sending Failed: " . json_encode($responseData));
        return false;
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'nullable|max:50',
            'phone' => 'nullable',
            'points' => 'nullable|integer|min:0',
            'nationality_id' => 'nullable|string|regex:/^\d{14}$/',
        ]);

        $user = User::find($id);
        if (!$user) {
            return redirect()->route('admin.traders')->with('error', 'Associated user not found.');
        }

        // Update User fields
        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);

        // Update Trader fields
        $trader = Trader::where('user_id', $id)->first();
        if ($trader) {
            $trader->update([
                'nationality_id' => $data['nationality_id'],
                'points' => $data['points'],
            ]);
        }

        return redirect()->route('admin.traders')->with('success', 'Trader updated successfully.');
    }

    public function destroy($id)
    {
        $trader = Trader::findOrFail($id);
        $trader->delete();
        return response()->json('Trader deleted successfully.', 200);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['error' => 'Invalid IDs provided.'], 400);
        }

        // Ensure IDs are integers
        $ids = array_map('intval', $ids);

        // Call Node.js API for bulk delete
        $response = Http::withBody(json_encode(['ids' => $ids]), 'application/json')
            ->delete('https://app.talentindustrial.com/plumber/trader/bulk-delete');

        if ($response->successful()) {
            $data = $response->json();
            return response()->json(['message' => 'Traders deleted successfully', 'deletedCount' => $data['deletedCount'] ?? count($ids)]);
        }

        return response()->json(['error' => 'Failed to delete traders.'], 500);
    }


    public function show($id)
    {
        $trader = Trader::with('user')->findOrFail($id);

        $totalCoupons = $trader->usedCoupons()->where('status', 'used')->count();

        // Total sales value from used coupons
        $totalSalesValue = $trader->getTotalSalesValue();

        // Current level based on total sales
        $currentLevel = $trader->getCurrentLevel();

        // Used coupons (same as total)
        $usedCoupons = $totalCoupons;

        return view('admin.traders.show', compact(
            'trader',
            'totalCoupons',
            'totalSalesValue',
            'currentLevel',
            'usedCoupons',
        ));
    }

    public function download($userId)
    {
        // Fetch the PDF/report from the Node.js API
        $response = Http::get("https://app.talentindustrial.com/plumber/trader/info/{$userId}/download");

        if ($response->successful()) {
            // Return the response with appropriate headers for download
            return response($response->body(), 200, [
                'Content-Type' => $response->header('Content-Type') ?: 'application/pdf',
                'Content-Disposition' => 'attachment; filename="trader_report_' . $userId . '.pdf"',
            ]);
        }

        return redirect()->route('admin.traders')->with('error', 'Failed to download trader report.');
    }
}

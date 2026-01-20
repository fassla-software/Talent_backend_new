<?php
namespace App\Http\Controllers\Admin;

use App\Models\Plumber; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FiscalYear;
use App\Models\PlumberFiscalPoints;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PlumberUsersController extends Controller
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
    $fiscalYearFilter = $request->input('fiscal_year_id');

    // Retrieve all unique cities before applying filters
    $allCities = Plumber::pluck('city')->unique()->sort();

    // Retrieve unique months from the created_at column
    $availableMonths = Plumber::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
        ->distinct()
        ->orderBy('month', 'desc')
        ->pluck('month');

    $allFiscalYears = FiscalYear::orderBy('year', 'desc')->get();
    $activeYear = FiscalYear::where('is_active', true)->first();

    // Retrieve plumbers and apply filters
    $plumbers = Plumber::with('user')
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
        'gift_points',
        'fixed_points',
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

    // If a historical fiscal year is selected, swap current points with archived points
    if ($fiscalYearFilter && (!$activeYear || $fiscalYearFilter != $activeYear->id)) {
        foreach ($plumbers as $plumber) {
            $historicalPoints = PlumberFiscalPoints::where('plumber_id', $plumber->user_id)
                ->where('fiscal_year_id', $fiscalYearFilter)
                ->first();
            
            if ($historicalPoints) {
                $plumber->gift_points = $historicalPoints->gift_points;
                $plumber->fixed_points = $historicalPoints->fixed_points;
                $plumber->instant_withdrawal = $historicalPoints->instant_withdrawal;
                $plumber->withdraw_money = $historicalPoints->withdraw_money;
            } else {
                // If no record exists for that year, points are 0
                $plumber->gift_points = 0;
                $plumber->fixed_points = 0;
                $plumber->instant_withdrawal = 0;
                $plumber->withdraw_money = 0;
            }
        }
    }

    $envoys = User::role('envoy')->get(); // Fetch all envoys
    return view('admin.plumberUsers', compact('plumbers', 'allCities', 'availableMonths', 'allFiscalYears', 'activeYear', 'envoys'));
}

public function create()
{
    $envoys = User::role('envoy')->get();
    return view('admin.plumberUsers.create', compact('envoys'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:users,phone',
        'password' => 'required|string|min:6',
        'city' => 'required|string',
        'area' => 'required|string',
        'nationality_id' => 'nullable|string',
        'inspector_id' => 'nullable|exists:users,id',
        'nationality_image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'nationality_image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    DB::beginTransaction();
    try {
        // Create User
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'phone_verified_at' => now(),
            'refer_code' => Str::upper(Str::random(5)),
            'status' => 'PENDING',
        ]);

        $user->assignRole('plumber');

        // Handle File Uploads
        $nationalityImage1 = null;
        $nationalityImage2 = null;

        if ($request->hasFile('nationality_image1')) {
            $file = $request->file('nationality_image1');
            $filename = 'optimized-' . time() . '_1_' . $file->getClientOriginalName();
            $file->move(base_path('plumber/uploads'), $filename);
            $nationalityImage1 = $filename;
        }

        if ($request->hasFile('nationality_image2')) {
            $file = $request->file('nationality_image2');
            $filename = 'optimized-' . time() . '_2_' . $file->getClientOriginalName();
            $file->move(base_path('plumber/uploads'), $filename);
            $nationalityImage2 = $filename;
        }

        // Create Plumber
        Plumber::create([
            'user_id' => $user->id,
            'city' => $request->city,
            'area' => $request->area,
            'nationality_id' => $request->nationality_id,
            'nationality_image1' => $nationalityImage1,
            'nationality_image2' => $nationalityImage2,
            'inspector_id' => $request->inspector_id,
            'status' => 'PENDING',
            'is_verified' => true,
        ]);

        DB::commit();

        // Send SMS
        $message = "Welcome to Talanet! You have been registered successfully. Your credentials: Phone: {$request->phone}, Password: {$request->password}.";
        $this->sendSMS($request->phone, $message);

        return redirect()->route('admin.plumberUsers')->with('success', 'Plumber created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to create plumber: ' . $e->getMessage())->withInput();
    }
}




    public function approve(Request $request, $id)
    {
        // Find the plumber by ID and load the related user
        $plumber = Plumber::with('user')->findOrFail($id);

        if (!$plumber->user) {
            return redirect()->route('admin.plumberUsers')->with('error', 'Associated user not found.');
        }

        $userId = $plumber->user->id; 
        $phone = $plumber->user->phone; // Get plumber's phone number

        // Send request to approve plumber
        $response = Http::put("https://app.talentindustrial.com/plumber/plumber/{$userId}/accept");

        if ($response->successful()) {
            // Send SMS Notification
            $message = "تهانينا! تم تفعيل حسابك في برنامج “شكرًا شركاء النجاح”. ابدأ الآن بكسب النقاط والاستفادة من المزايا الحصرية!";
            $this->sendSMS($phone, $message);

            return redirect()->route('admin.plumberUsers')->with('success', 'Plumber approved successfully.');
        }

        return redirect()->route('admin.plumberUsers')->with('error', 'Failed to approve plumber.');
    }

    public function reject(Request $request, $id)
    {
        // Find the plumber by ID and load the related user
        $plumber = Plumber::with('user')->findOrFail($id);

        if (!$plumber->user) {
            return redirect()->route('admin.plumberUsers')->with('error', 'Associated user not found.');
        }

        $userId = $plumber->user->id; 
        $phone = $plumber->user->phone; // Get plumber's phone number

        // Send request to reject plumber
        $response = Http::put("https://app.talentindustrial.com/plumber/plumber/{$userId}/reject");

        if ($response->successful()) {
            // Send SMS Notification
            $message = "نأسف لعدم تفعيل حسابك في برنامج “شكرًا شركاء النجاح”، يرجى استكمال بياناتك لإتمام التفعيل. لمزيد من التفاصيل، تواصل معنا.";
            $this->sendSMS($phone, $message);

            return redirect()->route('admin.plumberUsers')->with('success', 'Plumber rejected successfully.');
        }

        return redirect()->route('admin.plumberUsers')->with('error', 'Failed to reject plumber.');
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


     public function show($id)
    {
        $plumber = Plumber::with('user')->findOrFail($id);
        
        $activeYear = FiscalYear::where('is_active', true)->first();
        
        return view('admin.plumber_details', compact('plumber', 'activeYear'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'nullable|max:50',
            'phone' => 'nullable',
            'instant_withdrawal' => 'nullable|integer',
            'gift_points' => 'nullable|integer',
            'fixed_points' => 'nullable|integer',
            'nationality_id' => 'nullable|string|max:50', // Added validation for nationality_id
            'inspector_id' => 'nullable|exists:users,id',
        ]);

        $user = User::with('plumber')->find($id); // Eager load plumber
        if (!$user) {
            return redirect()->route('admin.plumberUsers')->with('error', 'Associated user not found.');
        }
        
        // Update User model
        $user->update([
            'name' => $data['name'] ?? $user->name,
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        // Update or Create Plumber model
        if ($user->plumber) {
            $user->plumber->update([
                'gift_points' => $data['gift_points'] ?? $user->plumber->gift_points,
                'fixed_points' => $data['fixed_points'] ?? $user->plumber->fixed_points,
                'instant_withdrawal' => $data['instant_withdrawal'] ?? $user->plumber->instant_withdrawal,
                'nationality_id' => $data['nationality_id'] ?? $user->plumber->nationality_id,
                'inspector_id' => $data['inspector_id'] ?? $user->plumber->inspector_id,
            ]);
        }

        return redirect()->route('admin.plumberUsers')->with('success', 'Plumber updated successfully.');
    }

    public function destroy($id)
    {
        $plumber = Plumber::findOrFail($id);
        $plumber->delete();
        return response()->json('Plumber deleted successfully.', 200);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:plumbers,id',
        ]);

        Plumber::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => 'Selected plumbers deleted successfully.']);
    }

    public function resetFiscalYear(Request $request)
    {
        $request->validate([
            'year' => 'required|string|unique:fiscal_years,year',
        ]);

        try {
            DB::beginTransaction();

            // 1. Get current active year if exists
            $currentActive = FiscalYear::where('is_active', true)->first();

            if ($currentActive) {
                // Archive current points
                $plumbers = Plumber::all();
                foreach ($plumbers as $plumber) {
                    PlumberFiscalPoints::create([
                        'plumber_id' => $plumber->user_id,
                        'fiscal_year_id' => $currentActive->id,
                        'gift_points' => $plumber->gift_points,
                        'fixed_points' => $plumber->fixed_points,
                        'instant_withdrawal' => $plumber->instant_withdrawal,
                        'withdraw_money' => $plumber->withdraw_money,
                    ]);

                    // Reset points in plumbers table
                    $plumber->update([
                        'gift_points' => 0,
                        'fixed_points' => 0,
                        'instant_withdrawal' => 0,
                        'withdraw_money' => 0,
                    ]);
                }

                // Deactivate old year
                $currentActive->update(['is_active' => false]);
            }

            // 2. Create and activate new year
            FiscalYear::create([
                'year' => $request->year,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Fiscal Year {$request->year} started and points reset successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reset fiscal year: ' . $e->getMessage());
        }
    }
}

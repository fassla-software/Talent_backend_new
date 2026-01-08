<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DistributorCoupon;
use Illuminate\Support\Str;
use App\Models\Trader;
use App\Models\Level;
use Milon\Barcode\DNS1D;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;


class DistributorCouponController extends Controller
{
 
    public function store(Request $request)
    {

        $data = $request->validate([
            'distributor_id' => 'required|integer|exists:distributors,id',
            'sales_value'    => 'required|numeric|min:0',
            'area_name'      => 'required|string|max:255',
            'expired_at'    => 'required|date_format:m/d/Y',
            'points'        => 'required|numeric|min:0',
            'base_amount'   =>  'required|numeric|min:0',
        ]);

        $couponCount = floor($data['sales_value'] / $data['base_amount']);

        if ($couponCount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sales value can not  generate a coupon.',
            ], 400);
        }


        // Parse the expired_at date
        $expiredAt = \Carbon\Carbon::createFromFormat('m/d/Y', $data['expired_at'])->endOfDay();

        $coupons = [];

        for ($i = 0; $i < $couponCount; $i++) {
            $coupons[] = [
                'distributor_id' => $data['distributor_id'],
                'area_name'      => $data['area_name'],
                'sales_value'    =>  $data['base_amount'],
                'code'           => strtoupper(Str::random(10)),
                'status'        => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
                'expired_at'    => $expiredAt,
                'points'         => $data['points'],
            ];
        }

        DistributorCoupon::insert($coupons);

        return redirect()->route('admin.coupons.index');

    }


public function index(Request $request)
{
    $query = DistributorCoupon::query()->with('distributor');

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter by distributor
    if ($request->filled('distributor_id')) {
        $query->where('distributor_id', $request->distributor_id);
    }

    // Filter by area name
    if ($request->filled('area_name')) {
        $query->where('area_name', 'like', '%' . $request->area_name . '%');
    }

    // Filter by points range
    if ($request->filled('points_min')) {
        $query->where('points', '>=', $request->points_min);
    }
    if ($request->filled('points_max')) {
        $query->where('points', '<=', $request->points_max);
    }


    if ($request->filled('created_from')) {
        $query->whereDate('created_at', '>=', $request->created_from);
    }
    if ($request->filled('created_to')) {
        $query->whereDate('created_at', '<=', $request->created_to);
    }     

    // Pagination
    $coupons = $query->orderBy('id', 'desc')->paginate(15)->appends($request->query());

    // For distributor filter dropdown
    $distributors = \App\Models\Distributor::orderBy('name')->get();
    $areas = \App\Models\DistributorCoupon::select('area_name')
                ->distinct()
                ->pluck('area_name');

    return view('admin.coupons.index', compact('coupons', 'distributors' , 'areas'));
}


public function create()
{
    $distributors = \App\Models\Distributor::all();

    // Get distinct areas from existing coupons
    $couponAreas = \App\Models\DistributorCoupon::select('area_name')
                    ->distinct()
                    ->pluck('area_name')
                    ->filter();

    // Get distinct addresses from distributors
    $distributorAddresses = \App\Models\Distributor::select('city')
                            ->distinct()
                            ->pluck('city')
                            ->filter();

    // Merge and remove duplicates
    $areas = $couponAreas
                ->merge($distributorAddresses)
                ->unique()
                ->sort()
                ->values();

    return view('admin.coupons.create', compact('distributors', 'areas'));
}


public function destroy($id)
{
    $coupon = DistributorCoupon::findOrFail($id);
    $coupon->delete();

    return redirect()->back()->with('success', 'Coupon deleted successfully.');
}


public function generateCouponLabels(Request $request, $distributorId)
{
    $ids = explode(',', $request->query('ids', ''));
    $ids = array_filter($ids, function($id) {
        return !empty($id) && is_numeric($id);
    });

     $query = DistributorCoupon::with('distributor')->where('is_printed', false);

    // If distributorId provided → filter
    if (!empty($distributorId)) {
        $query->where('distributor_id', $distributorId);
    }

    // If ids passed → filter
    if (!empty($ids)) {
        $query->whereIn('id', $ids);
    }

    if (!empty($ids)) {
        $coupons = $query->whereIn('id', $ids)->get();
    } else {
        $coupons = $query->get();
    }

    $data = [];
    foreach ($coupons as $coupon) {
        // QR code
        $qrCode = new QrCode($coupon->code, errorCorrectionLevel: ErrorCorrectionLevel::High, size: 100);
        $writer = new PngWriter();
        $qrResult = $writer->write($qrCode);
        $qrBase64 = "data:image/png;base64," . base64_encode($qrResult->getString());

        // Barcode
        $barcode = new DNS1D();
        $barcode->setStorPath(storage_path('app/barcodes'));
        $barcodeBase64 = "data:image/png;base64," . $barcode->getBarcodePNG((string) $coupon->id, "C39", 4, 40);

        $data[] = [
            'id'          => $coupon->id,
            'code'        => $coupon->code,
            'distributor' => $coupon->distributor->name ?? '',
            'area'        => $coupon->area_name,
            'status'      => ucfirst($coupon->status),
            'qr'          => $qrBase64,
            'barcode'     => $barcodeBase64,
        ];
    }

    // Assuming you have a view for labels, return it with data
    return view('admin.coupons.labels', compact('data'));
}

    public function togglePrinted($id)
    {
        $coupon = DistributorCoupon::findOrFail($id);
        $coupon->update(['is_printed' => !$coupon->is_printed]);

        return response()->json([
            'success' => true,
            'is_printed' => $coupon->is_printed
        ]);
    }

    public function markPrinted(Request $request, $distributorId)
    {
        $ids = explode(',', $request->query('ids', ''));
        $ids = array_filter($ids, function ($id) {
            return !empty($id) && is_numeric($id);
        });

        if (!empty($ids)) {
            $updatedCount = DistributorCoupon::whereIn('id', $ids)->where('is_printed', false)->update(['is_printed' => true]);
        } else {
            $updatedCount = DistributorCoupon::where('distributor_id', $distributorId)->where('is_printed', false)->update(['is_printed' => true]);
        }

        return response()->json(['success' => true, 'updated_count' => $updatedCount]);
    }

    public function exportCoupons(Request $request, $distributorId)
    {
        $ids = explode(',', $request->query('ids', ''));
        $ids = array_filter($ids, function($id) {
            return !empty($id) && is_numeric($id);
        });

        $query = DistributorCoupon::where('distributor_id', $distributorId)->where('is_printed', false);

        if (!empty($ids)) {
            $coupons = $query->whereIn('id', $ids)->get();
        } else {
            $coupons = $query->get();
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DistributorCouponExport($coupons), 'distributor_coupons.xlsx');
    }

}

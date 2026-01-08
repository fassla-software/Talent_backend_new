<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InspectionRequest;
use App\Models\Plumber;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Models\PlumberCategory;
use App\Models\InspectionRequestItem;
use App\Models\PlumberWithdraw;
use App\Models\PlumberReceivedGift;
use App\Models\PlumberGift;

class DashboardAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from') ?? Carbon::now()->startOfMonth()->toDateString();
        $to = $request->input('to') ?? Carbon::now()->endOfMonth()->toDateString();
        $city = $request->input('city');
        
        // Debug: Log the filters
        Log::info('Dashboard Filters:', [
            'from' => $from, 
            'to' => $to, 
            'city' => $city,
            'city_decoded' => urldecode($city ?? ''),
            'city_length' => strlen($city ?? '')
        ]);

        $inspectionsQuery = InspectionRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        if ($city) {
            $inspectionsQuery->where('city', $city);
        }
        
        // Debug: Check what cities exist in DB
        $allCities = InspectionRequest::select('city')->distinct()->get();
        Log::info('All cities in DB:', $allCities->toArray());
        
        // Debug: Log the query
        Log::info('Inspections Query SQL:', ['sql' => $inspectionsQuery->toSql(), 'bindings' => $inspectionsQuery->getBindings()]);
        
        // Debug: Check specific city data
        if ($city === 'كفر الشيخ') {
            $cityRecords = InspectionRequest::where('city', $city)->get(['id', 'created_at', 'city']);
            Log::info('كفر الشيخ Records:', $cityRecords->toArray());
            
            $cityInDateRange = InspectionRequest::where('city', $city)
                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                ->get(['id', 'created_at', 'city']);
            Log::info('كفر الشيخ in Date Range:', $cityInDateRange->toArray());
        }

        $filteredInspections = $inspectionsQuery->get();
        $totalInspections = $inspectionsQuery->count();
        
        $filteredInspectionIds = InspectionRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->when($city, function($q) use ($city) {
                return $q->where('city', $city);
            })->pluck('id');
            
        // Debug: Log the results
        Log::info('Query Results:', ['total_inspections' => $totalInspections, 'filtered_ids_count' => count($filteredInspectionIds ?? [])]);
        $approvedCount = InspectionRequest::where('status', 'APPROVED')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->when($city, function($q) use ($city) {
                return $q->where('city', $city);
            })->count();

        $inspectionStats = $inspectionsQuery->select('status', DB::raw('count(*) as count'))
    ->groupBy('status')->pluck('count', 'status');


        $cityWiseInspections = InspectionRequest::select('city', DB::raw('count(*) as total'))
            ->groupBy('city')->pluck('total', 'city');

$totalPlumbers = Plumber::when($city, function($q) use ($city) {
    return $q->where('city', $city);
})->count();
$envoyRole = Role::where('name', 'envoy')->first();
$envoyUserIds = $envoyRole ? $envoyRole->users()->pluck('id')->toArray() : [];
$inspectorQuery = InspectionRequest::whereIn('inspector_id', $envoyUserIds)
    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
    ->when($city, function($q) use ($city) {
        return $q->where('city', $city);
    });
$totalInspectors = $inspectorQuery->distinct('inspector_id')->count('inspector_id');

$topInspectors = $inspectorQuery
    ->select('inspector_id', DB::raw('count(*) as total'))
    ->groupBy('inspector_id')
    ->orderByDesc('total')
    ->take(10)
    ->get()
    ->map(function ($item) {
        $user = User::find($item->inspector_id);
        return [
            'name' => $user ? "{$user->name} {$user->last_name}" : 'Unknown',
            'total' => $item->total
        ];
    });


       $topPlumbers = Plumber::with('user')
    ->when($city, function($q) use ($city) {
        return $q->where('city', $city);
    })
    ->select('id', 'user_id', 'fixed_points')
    ->orderByDesc('fixed_points')
    ->take(10)
    ->get();

        $instantWithdrawalTotal = PlumberWithdraw::whereRaw('LOWER(status) = ?', ['approved'])
            ->whereBetween('request_date', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->whereIn('requestor_id', function($query) use ($city) {
                $query->select('user_id')
                    ->from('plumbers')
                    ->when($city, function($q) use ($city) {
                        return $q->where('city', $city);
                    });
            })->sum('amount');

        $totalFixedPoints = InspectionRequestItem::whereIn('inspection_request_id', function($query) use ($from, $to, $city) {
                $query->select('id')
                    ->from('inspection_requests')
                    ->where('status', 'APPROVED')
                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                    ->when($city, function($q) use ($city) {
                        return $q->where('city', $city);
                    });
            })
            ->join('plumber_categories', 'inspection_requests_items.subcategory_id', '=', 'plumber_categories.id')
            ->sum(DB::raw('inspection_requests_items.count * plumber_categories.points'));

        $totalGiftPoints = PlumberReceivedGift::whereRaw('LOWER(plumber_received_gifts.status) NOT IN (?, ?)', ['rejected', 'canceled'])
            ->whereBetween('plumber_received_gifts.createdAt', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->whereIn('plumber_received_gifts.user_id', function($query) use ($city) {
                $query->select('user_id')
                    ->from('plumbers')
                    ->when($city, function($q) use ($city) {
                        return $q->where('city', $city);
                    });
            })
            ->join('plumber_gifts', 'plumber_received_gifts.gift_id', '=', 'plumber_gifts.id')
            ->sum('plumber_gifts.points_required');

        $cities = InspectionRequest::select('city')->distinct()->pluck('city');
        $filteredInspectionIds = InspectionRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->when($city, function($q) use ($city) {
                return $q->where('city', $city);
            })->pluck('id');
        $usedProductIds = InspectionRequestItem::whereIn('inspection_request_id', $filteredInspectionIds)
            ->distinct('subcategory_id')
            ->pluck('subcategory_id');
        $totalProducts = PlumberCategory::where('product_flag', 1)
            ->whereIn('id', $usedProductIds)
            ->count();

        $mostRequestedProducts = InspectionRequestItem::whereIn('inspection_request_id', $filteredInspectionIds)
            ->select('subcategory_id', DB::raw('SUM(count) as total'))
            ->groupBy('subcategory_id')
            ->orderByDesc('total')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $product = PlumberCategory::find($item->subcategory_id);
                return [
                    'name' => $product?->name ?? 'Unknown',
                    'image' => $product?->image ? 'https://app.talentindustrial.com/plumber/uploads/' . $product->image : null,
                    'total' => $item->total
                ];
            });


        return view('admin.analysis.dashboard-analysis', compact(
            'from', 'to', 'city',
            'totalInspections', 'approvedCount', 'inspectionStats',
            'cityWiseInspections', 'totalPlumbers', 'totalInspectors',
            'topInspectors', 'topPlumbers',
            'instantWithdrawalTotal', 'totalFixedPoints', 'totalGiftPoints',
            'cities', 'totalProducts', 'mostRequestedProducts'
        ));
    }
}
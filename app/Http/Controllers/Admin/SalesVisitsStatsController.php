<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InspectionVisit;
use App\Models\VisitReport;
use App\Models\Trader;
use App\Models\Plumber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesVisitsStatsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from') ?? Carbon::now()->startOfMonth()->toDateString();
        $to = $request->input('to') ?? Carbon::now()->toDateString();
        $envoyId = $request->input('envoy_id');
        $clientType = $request->input('client_type'); // 'plumber' or 'trader'

        $dateRange = [$from . ' 00:00:00', $to . ' 23:59:59'];

        // 1. Total Visits (Only APPROVED as requested)
        $visitsQuery = InspectionVisit::whereBetween('inspection_visits.created_at', $dateRange);
        if ($envoyId) {
            $visitsQuery->where('inspector_id', $envoyId);
        }
        if ($clientType === 'plumber') {
            $visitsQuery->whereNotNull('inspection_visits.plumber_id');
        } elseif ($clientType === 'trader') {
            $visitsQuery->whereNotNull('inspection_visits.trader_id');
        }
        $totalVisits = (clone $visitsQuery)->where('inspection_visits.status', 'APPROVED')->count();

        // 2. Sales Reps
        $salesReps = User::role('envoy')->count();

        // 3. Customers (Count of plumbers + traders based on filter)
        $plumbersQuery = Plumber::whereBetween('plumbers.created_at', $dateRange);
        $tradersQuery = Trader::whereBetween('traders.created_at', $dateRange);

        if ($envoyId) {
            $plumbersQuery->where('inspector_id', $envoyId);
            $tradersQuery->where('inspector_id', $envoyId);
        }

        $totalCustomers = 0;
        $activeCustomers = 0;
        $expectedCustomers = 0;
        $areas = collect();

        if (!$clientType || $clientType === 'plumber') {
            $totalCustomers += (clone $plumbersQuery)->count();
            $activeCustomers += (clone $plumbersQuery)->where('status', 'ACTIVE')->count();
            $expectedCustomers += (clone $plumbersQuery)->where('status', 'PENDING')->count();
            $areas = $areas->merge((clone $plumbersQuery)->distinct()->pluck('area'));
        }

        if (!$clientType || $clientType === 'trader') {
            $totalCustomers += (clone $tradersQuery)->count();
            $activeCustomers += (clone $tradersQuery)->where('status', 'ACTIVE')->count();
            $expectedCustomers += (clone $tradersQuery)->where('status', 'PENDING')->count();
            $areas = $areas->merge((clone $tradersQuery)->distinct()->pluck('area'));
        }

        $uniqueAreasCount = $areas->unique()->filter()->count();

        // 4. Revenue
        $revenueQuery = VisitReport::whereBetween('report_visits.created_at', $dateRange)
            ->where('report_visits.status', 'SUBMITTED');
        
        if ($envoyId) {
            $revenueQuery->whereHas('inspectionVisit', function($q) use ($envoyId) {
                $q->where('inspector_id', $envoyId)->where('status', 'APPROVED');
            });
        } else {
            $revenueQuery->whereHas('inspectionVisit', function($q) {
                $q->where('status', 'APPROVED');
            });
        }

        if ($clientType === 'plumber') {
            $revenueQuery->whereNotNull('report_visits.plumber_id');
        } elseif ($clientType === 'trader') {
            $revenueQuery->whereNotNull('report_visits.trader_id');
        }

        $drRevenue = (clone $revenueQuery)->where('sales_classification', 'direct_sales')->sum('sales_value');
        $indRevenue = (clone $revenueQuery)->where('sales_classification', 'indirect_sales')->sum('sales_value');

        // New Chart Data

        // 1. Revenue per customer (Pie)
        $revenuePerCustomer = (clone $revenueQuery)
            ->select(
                DB::raw("COALESCE(customer_name, company_name, (SELECT name FROM users WHERE id = (SELECT user_id FROM plumbers WHERE id = report_visits.plumber_id)), (SELECT name FROM users WHERE id = (SELECT user_id FROM traders WHERE id = report_visits.trader_id)), 'Unknown') as customer"),
                DB::raw('SUM(sales_value) as total')
            )
            ->groupBy('customer')
            ->having('total', '>', 0)
            ->orderByDesc('total')
            ->pluck('total', 'customer');

        // 2. Visits by sales rep (Pie)
        $visitsByEnvoy = (clone $visitsQuery)
            ->join('users', 'inspection_visits.inspector_id', '=', 'users.id')
            ->select(DB::raw("CONCAT(users.name, ' ', COALESCE(users.last_name, '')) as envoy_name"), DB::raw('count(*) as total'))
            ->groupBy('users.name', 'users.last_name', 'inspection_visits.inspector_id')
            ->pluck('total', 'envoy_name');

        // 3. Visits by customer (Pie)
        $visitsByCustomer = (clone $visitsQuery)
            ->select(
                DB::raw("COALESCE(
                    (SELECT name FROM users WHERE id = (SELECT user_id FROM plumbers WHERE id = inspection_visits.plumber_id)),
                    (SELECT name FROM users WHERE id = (SELECT user_id FROM traders WHERE id = inspection_visits.trader_id)),
                    'Unknown'
                ) as customer_name"), 
                DB::raw('count(*) as total')
            )
            ->groupBy('customer_name')
            ->pluck('total', 'customer_name');

        // 4. Customer statuses (Bar)
        $statusCounts = collect([
            'ACTIVE' => 0,
            'PENDING' => 0,
            'INACTIVE' => 0,
            'DORMANT' => 0,
        ]);
        
        if (!$clientType || $clientType === 'plumber') {
            $pStatuses = (clone $plumbersQuery)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
            foreach ($pStatuses as $status => $count) {
                if ($statusCounts->has($status)) $statusCounts[$status] += $count;
            }
        }
        if (!$clientType || $clientType === 'trader') {
            $tStatuses = (clone $tradersQuery)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
            foreach ($tStatuses as $status => $count) {
                if ($statusCounts->has($status)) $statusCounts[$status] += $count;
            }
        }

        // 5. Customer interest (Pie)
        $customerInterest = (clone $revenueQuery)
            ->select('interest_level', DB::raw('count(*) as total'))
            ->whereNotNull('interest_level')
            ->groupBy('interest_level')
            ->pluck('total', 'interest_level');

        // 6. Visits per rep (Stacked Bar)
        $visitsPerRepByStatusRaw = (clone $visitsQuery)
            ->join('users as envoys', 'inspection_visits.inspector_id', '=', 'envoys.id')
            ->select(
                DB::raw("CONCAT(envoys.name, ' ', COALESCE(envoys.last_name, '')) as envoy_name"),
                DB::raw("COALESCE(
                    (SELECT status FROM plumbers WHERE id = inspection_visits.plumber_id),
                    (SELECT status FROM traders WHERE id = inspection_visits.trader_id),
                    'Unknown'
                ) as customer_status"),
                DB::raw('count(*) as total')
            )
            ->groupBy('envoys.name', 'envoys.last_name', 'inspection_visits.inspector_id', 'customer_status')
            ->get();

        $visitsPerRepByStatus = [];
        foreach ($visitsPerRepByStatusRaw as $row) {
            $visitsPerRepByStatus[$row->envoy_name][$row->customer_status] = $row->total;
        }


        // 7 & 8. Customer count by month (total + by status)
        $allStatuses = ['ACTIVE', 'PENDING', 'INACTIVE', 'DORMANT'];
        $fromCarbon  = Carbon::parse($from)->startOfMonth();
        $toCarbon    = Carbon::parse($to)->endOfMonth();

        $monthLabels          = [];
        $customerCountByMonthStatus = array_fill_keys($allStatuses, []);
        
        // New data for Approved Visits Charts
        $approvedVisitsByMonth = []; 
        $approvedVisitsByMonthEnvoyRaw = []; // month_label => [envoy_name => count]

        $cursor = $fromCarbon->copy();
        while ($cursor->lte($toCarbon)) {
            $monthStart = $cursor->copy()->startOfMonth()->toDateTimeString();
            $monthEnd   = $cursor->copy()->endOfMonth()->toDateTimeString();
            $label      = $cursor->format('M Y');
            $monthLabels[] = $label;

            $monthStatusCounts = array_fill_keys($allStatuses, 0);

            if (!$clientType || $clientType === 'plumber') {
                $pBase = Plumber::whereBetween('plumbers.created_at', [$monthStart, $monthEnd]);
                if ($envoyId) $pBase->where('inspector_id', $envoyId);
                foreach ($allStatuses as $st) {
                    $monthStatusCounts[$st] += (clone $pBase)->where('status', $st)->count();
                }
            }
            if (!$clientType || $clientType === 'trader') {
                $tBase = Trader::whereBetween('traders.created_at', [$monthStart, $monthEnd]);
                if ($envoyId) $tBase->where('inspector_id', $envoyId);
                foreach ($allStatuses as $st) {
                    $monthStatusCounts[$st] += (clone $tBase)->where('status', $st)->count();
                }
            }

            foreach ($allStatuses as $st) {
                $customerCountByMonthStatus[$st][] = $monthStatusCounts[$st];
            }

            // --- Approved Visits Logic for Chart 7 and 13 ---
            $monthVisitsBase = InspectionVisit::whereBetween('inspection_visits.created_at', [$monthStart, $monthEnd])
                ->where('inspection_visits.status', 'APPROVED');
            
            if ($envoyId) $monthVisitsBase->where('inspector_id', $envoyId);
            if ($clientType === 'plumber') $monthVisitsBase->whereNotNull('inspection_visits.plumber_id');
            elseif ($clientType === 'trader') $monthVisitsBase->whereNotNull('inspection_visits.trader_id');

            $approvedVisitsByMonth[] = (clone $monthVisitsBase)->count();

            // Breakdown by Envoy for Chart 13
            $envoyBreakdown = (clone $monthVisitsBase)
                ->join('users', 'users.id', '=', 'inspection_visits.inspector_id')
                ->select(DB::raw("CONCAT(users.name, ' ', COALESCE(users.last_name, '')) as name"), DB::raw('count(*) as total'))
                ->groupBy('users.name', 'users.last_name', 'inspection_visits.inspector_id')
                ->pluck('total', 'name');

            $approvedVisitsByMonthEnvoyRaw[$label] = $envoyBreakdown->toArray();

            $cursor->addMonth();
        }

        $monthCount    = count($approvedVisitsByMonth);
        $monthAverage  = $monthCount > 0 ? round(array_sum($approvedVisitsByMonth) / $monthCount, 2) : 0;

        $approvedVisitMonthData = [
            'months'   => $monthLabels,
            'counts'   => $approvedVisitsByMonth,
            'average'  => $monthAverage,
            'byStatus' => $customerCountByMonthStatus, // retaining for chart 8
        ];

        // Format for stacked helper: { month: { envoy: count } }
        $approvedVisitsByEnvoyStacked = $approvedVisitsByMonthEnvoyRaw;

        // 9. Direct Sales per Envoy, stacked by Client
        $directSalesRaw = DB::table('report_visits')
            ->join('inspection_visits', 'inspection_visits.report_id', '=', 'report_visits.id')
            ->join('users as envoys', 'envoys.id', '=', 'inspection_visits.inspector_id')
            ->whereBetween('report_visits.created_at', $dateRange)
            ->where('report_visits.status', 'SUBMITTED')
            ->where('report_visits.sales_classification', 'direct_sales')
            ->where('inspection_visits.status', 'APPROVED')
            ->when($envoyId, fn($q) => $q->where('inspection_visits.inspector_id', $envoyId))
            ->when($clientType === 'plumber', fn($q) => $q->whereNotNull('report_visits.plumber_id'))
            ->when($clientType === 'trader',  fn($q) => $q->whereNotNull('report_visits.trader_id'))
            ->select(
                'inspection_visits.inspector_id',
                DB::raw("CONCAT(envoys.name, ' ', COALESCE(envoys.last_name, '')) as envoy_name"),
                DB::raw("COALESCE(report_visits.customer_name, report_visits.company_name, 'Unknown') as client_name"),
                DB::raw('SUM(report_visits.sales_value) as total')
            )
            ->groupBy(
                'inspection_visits.inspector_id',
                'envoys.name',
                'envoys.last_name',
                'client_name'
            )
            ->having('total', '>', 0)
            ->get();

        $directSalesPerEnvoy = [];
        foreach ($directSalesRaw as $row) {
            $directSalesPerEnvoy[$row->envoy_name][$row->client_name] = (float) $row->total;
        }

        // 10. Indirect Sales per Envoy, stacked by Client
        $indirectSalesRaw = DB::table('report_visits')
            ->join('inspection_visits', 'inspection_visits.report_id', '=', 'report_visits.id')
            ->join('users as envoys', 'envoys.id', '=', 'inspection_visits.inspector_id')
            ->whereBetween('report_visits.created_at', $dateRange)
            ->where('report_visits.status', 'SUBMITTED')
            ->where('report_visits.sales_classification', 'indirect_sales')
            ->where('inspection_visits.status', 'APPROVED')
            ->when($envoyId, fn($q) => $q->where('inspection_visits.inspector_id', $envoyId))
            ->when($clientType === 'plumber', fn($q) => $q->whereNotNull('report_visits.plumber_id'))
            ->when($clientType === 'trader',  fn($q) => $q->whereNotNull('report_visits.trader_id'))
            ->select(
                'inspection_visits.inspector_id',
                DB::raw("CONCAT(envoys.name, ' ', COALESCE(envoys.last_name, '')) as envoy_name"),
                DB::raw("COALESCE(report_visits.customer_name, report_visits.company_name, 'Unknown') as client_name"),
                DB::raw('SUM(report_visits.sales_value) as total')
            )
            ->groupBy(
                'inspection_visits.inspector_id',
                'envoys.name',
                'envoys.last_name',
                'client_name'
            )
            ->having('total', '>', 0)
            ->get();

        $indirectSalesPerEnvoy = [];
        foreach ($indirectSalesRaw as $row) {
            $indirectSalesPerEnvoy[$row->envoy_name][$row->client_name] = (float) $row->total;
        }

        // Data for dropdowns
        $envoys = User::role('envoy')->get(['id', 'name', 'last_name']);

        // 11. Paginated Inspection Visits Table
        $paginatedVisits = (clone $visitsQuery)
            ->with([
                'inspector:id,name,last_name',
                'trader:id,user_id,status',
                'trader.user:id,name',
                'plumber:id,user_id,status',
                'plumber.user:id,name',
                'visitReport:id,customer_name,company_name,next_action'
            ])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->all());

        // 12. Customer Visits Pivot Table
        // First, get a list of customer IDs that have visits in this range
        $customerVisitsBase = (clone $visitsQuery)
            ->where('inspection_visits.status', 'APPROVED')
            ->select(
                'inspection_visits.plumber_id', 
                'inspection_visits.trader_id', 
                DB::raw("DATE_FORMAT(inspection_visits.created_at, '%b %Y') as month_year"),
                DB::raw('count(*) as total')
            )
            ->groupBy('inspection_visits.plumber_id', 'inspection_visits.trader_id', 'month_year');

        // Get paginated customers (distinct plumber_id/trader_id combinations)
        $paginatedCustomersQuery = (clone $visitsQuery)
            ->where('inspection_visits.status', 'APPROVED')
            ->select('inspection_visits.plumber_id', 'inspection_visits.trader_id')
            ->groupBy('inspection_visits.plumber_id', 'inspection_visits.trader_id');
        
        $customerPagination = $paginatedCustomersQuery->paginate(10, ['*'], 'customer_page')->appends($request->all());

        $pivotData = [];
        foreach ($customerPagination as $row) {
            $customer = $row->plumber ?? $row->trader;
            if (!$customer) continue;
            
            $name = $customer->user->name ?? 'Unknown';
            $customerKey = ($row->plumber_id ? 'P' : 'T') . $customer->id;
            
            $pivotData[$customerKey] = [
                'name' => $name,
                'type' => $row->plumber_id ? 'Plumber' : 'Trader',
                'months' => array_fill_keys($monthLabels, 0),
                'total' => 0
            ];

            // Fetch monthly counts for this specific customer
            $counts = (clone $customerVisitsBase)
                ->when($row->plumber_id, fn($q) => $q->where('inspection_visits.plumber_id', $row->plumber_id))
                ->when($row->trader_id, fn($q) => $q->where('inspection_visits.trader_id', $row->trader_id))
                ->get();

            foreach ($counts as $c) {
                if (isset($pivotData[$customerKey]['months'][$c->month_year])) {
                    $pivotData[$customerKey]['months'][$c->month_year] = $c->total;
                    $pivotData[$customerKey]['total'] += $c->total;
                }
            }
        }

        // Calculate totals for each month (across ALL customers in the filtered range, not just paginated ones)
        $monthlyTotalsRaw = (clone $customerVisitsBase)->get();
        $monthlyTotals = array_fill_keys($monthLabels, 0);
        $grandTotal = 0;
        foreach ($monthlyTotalsRaw as $row) {
            if (isset($monthlyTotals[$row->month_year])) {
                $monthlyTotals[$row->month_year] += $row->total;
                $grandTotal += $row->total;
            }
        }

        // 13. Sales Value Pivot Table (Rep + Customer)
        $salesPivotBase = (clone $visitsQuery)
            ->join('report_visits', 'inspection_visits.report_id', '=', 'report_visits.id')
            ->where('inspection_visits.status', 'APPROVED')
            ->select(
                'inspection_visits.inspector_id',
                'inspection_visits.plumber_id',
                'inspection_visits.trader_id',
                DB::raw("DATE_FORMAT(inspection_visits.created_at, '%b %Y') as month_year"),
                DB::raw('SUM(report_visits.sales_value) as total_sales')
            )
            ->groupBy('inspection_visits.inspector_id', 'inspection_visits.plumber_id', 'inspection_visits.trader_id', 'month_year');

        $paginatedSalesQuery = (clone $visitsQuery)
            ->where('inspection_visits.status', 'APPROVED')
            ->select('inspection_visits.inspector_id', 'inspection_visits.plumber_id', 'inspection_visits.trader_id')
            ->groupBy('inspection_visits.inspector_id', 'inspection_visits.plumber_id', 'inspection_visits.trader_id');

        $salesPagination = $paginatedSalesQuery->paginate(10, ['*'], 'sales_page')->appends($request->all());

        $salesValuePivotData = [];
        foreach ($salesPagination as $row) {
            $repName = $row->inspector ? ($row->inspector->name . ' ' . ($row->inspector->last_name ?? '')) : 'Unknown';
            $customer = $row->plumber ?? $row->trader;
            if (!$customer) continue;
            
            $custName = $customer->user->name ?? 'Unknown';
            $compositeKey = $row->inspector_id . '-' . ($row->plumber_id ? 'P' : 'T') . $customer->id;

            $salesValuePivotData[$compositeKey] = [
                'rep_name' => $repName,
                'cust_name' => $custName,
                'type' => $row->plumber_id ? 'Plumber' : 'Trader',
                'months' => array_fill_keys($monthLabels, 0),
                'total' => 0
            ];

            $values = (clone $salesPivotBase)
                ->where('inspection_visits.inspector_id', $row->inspector_id)
                ->when($row->plumber_id, fn($q) => $q->where('inspection_visits.plumber_id', $row->plumber_id))
                ->when($row->trader_id, fn($q) => $q->where('inspection_visits.trader_id', $row->trader_id))
                ->get();

            foreach ($values as $v) {
                if (isset($salesValuePivotData[$compositeKey]['months'][$v->month_year])) {
                    $salesValuePivotData[$compositeKey]['months'][$v->month_year] = (float) $v->total_sales;
                    $salesValuePivotData[$compositeKey]['total'] += (float) $v->total_sales;
                }
            }
        }

        $monthlySalesTotalsRaw = (clone $salesPivotBase)->get();
        $monthlySalesTotals = array_fill_keys($monthLabels, 0);
        $grandSalesTotal = 0;
        foreach ($monthlySalesTotalsRaw as $row) {
            if (isset($monthlySalesTotals[$row->month_year])) {
                $monthlySalesTotals[$row->month_year] += (float) $row->total_sales;
                $grandSalesTotal += (float) $row->total_sales;
            }
        }

        return view('admin.analysis.sales-visits-stats', compact(
            'from', 'to', 'envoyId', 'clientType',
            'totalVisits', 'salesReps', 'totalCustomers', 'activeCustomers',
            'expectedCustomers', 'uniqueAreasCount', 'drRevenue', 'indRevenue',
            'envoys',
            'revenuePerCustomer', 'visitsByEnvoy', 'visitsByCustomer',
            'statusCounts', 'customerInterest', 'visitsPerRepByStatus',
            'directSalesPerEnvoy', 'indirectSalesPerEnvoy',
            'paginatedVisits',
            'pivotData', 'monthLabels', 'customerPagination',
            'monthlyTotals', 'grandTotal',
            'salesValuePivotData', 'salesPagination',
            'monthlySalesTotals', 'grandSalesTotal',
            'approvedVisitMonthData', 'approvedVisitsByEnvoyStacked'
        ));
    }
}

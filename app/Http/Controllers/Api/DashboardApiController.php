<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ApiLog ;
use App\Models\DataReplication ;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{

    private function monthlyStepStats($stepId)
    {
        $now = now();
        $lastMonth = $now->copy()->subMonth();

        // Current month count
        $current = AppLog::where('stepId', $stepId)
            ->whereMonth('Date', $now->month)
            ->whereYear('Date', $now->year)
            ->count();

        // Last month count
        $previous = AppLog::where('stepId', $stepId)
            ->whereMonth('Date', $lastMonth->month)
            ->whereYear('Date', $lastMonth->year)
            ->count();

        // Percent change (with division safety)
        $change = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
        // $changeFormatted = ($change >= 0 ? '+' : '') . number_format($change, 1) . '%';

        return [
            'current' => $current,
            'change' => $change,
        ];
    }

    public function getMetrics()
    {
        $cenId = auth()->user()->centerId ; 
        $role = auth()->user()->role_id ;   

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $lastMonth = $now->copy()->subMonth();
         $totalReceive = 0 ; 
         $servers = [];
 
         $logs = ApiLog::latest('id')
            ->take(15)
            ->get([
                'created_at',
                'api',
                'payload',
                'response'
            ]);
         return response()->json([
                'logs' => $logs,
                'totalReceive' => $totalReceive,
 
            ]);
       
    }

    public function getRecentBookings()
    {
        $bookings = DB::table('room_books')
            ->select('room_books.*', 'users.name as user_name', 'rooms.name as room_name', 'rooms.room_no')
            ->leftJoin('users', 'room_books.user_id', '=', 'users.id')
            ->leftJoin('rooms', 'room_books.room_id', '=', 'rooms.id')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($bookings);
    }

    public function getRecentBills()
    {
        $bills = DB::table('bill_masters')
            ->select('bill_masters.*', 'users.name as user_name', 'room_books.room_id', 'rooms.room_no')
            ->leftJoin('users', 'bill_masters.user_id', '=', 'users.id')
            ->leftJoin('room_books', 'bill_masters.booking_id', '=', 'room_books.id')
            ->leftJoin('rooms', 'room_books.room_id', '=', 'rooms.id')
            ->orderBy('bill_date', 'desc')
            ->whereNull('merge_master_id')
            ->limit(5)
            ->get();

        return response()->json($bills);
    }

    public function getBillCategories()
    {
        $categories = DB::table('bill_childs')
            ->select('bill_heads.name', DB::raw('SUM(bill_childs.amount) as amount'))
            ->join('bill_masters', 'bill_childs.bill_master_id', '=', 'bill_masters.id')
            ->join('bill_heads', 'bill_childs.bill_head_id', '=', 'bill_heads.id')
            ->groupBy('bill_heads.name')
            ->orderBy('amount', 'desc')
            ->limit(6)
            ->get();

        $labels = $categories->pluck('name')->toArray();
        $values = $categories->pluck('amount')->toArray();
        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444', '#6366F1'];

        return response()->json([
            'categories' => $categories,
            'labels' => $labels,
            'values' => $values,
            //'colors' => array_slice($colors, 0, count($labels))
        ]);
    }

    public function getRoomStatus()
    {
        $statuses = [
            ['status' => 'Occupied', 'count' => DB::table('room_books')->where('is_approved', 1)->where('is_released', 0)->count()],
            ['status' => 'Reserved', 'count' => DB::table('room_books')->where('is_approved', 0)->count()],
            ['status' => 'Maintenance', 'count' => DB::table('rooms')->where('status', 0)->count()],
            //['status' => 'Out of Service', 'count' => DB::table('rooms')->where('status', 2)->count()], // Assuming 2 is out of service
            ['status' => 'Available', 'count' => DB::table('rooms')->where('status', 1)->count() - DB::table('room_books')->where('is_approved', 1)->where('is_released', 0)->count()],
            ['status' => 'Total Rooms', 'count' => DB::table('rooms')->count()]
        ];

        return response()->json($statuses);
    }

    public function getRevenueData(Request $request)
    {
        $period = $request->query('period', 'year');
        $months = $period === 'year' ? 12 : 6;

        $revenueData = $this->getRevenueDataForPeriod($months);

        return response()->json($revenueData);
    }

    private function getRevenueDataForPeriod($months)
    {
        $revenueData = [];
        $labels = [];
        $values = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $monthName = $date->format('M Y');
            $total = DB::table('bill_masters')
                ->whereBetween('bill_date', [$start, $end])
                ->whereNull('merge_master_id')
                ->sum('total_payable');

            $labels[] = $monthName;
            $values[] = $total ?: 0;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
}

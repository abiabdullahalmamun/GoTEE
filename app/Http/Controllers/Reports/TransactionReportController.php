<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\BillMaster;
use App\Models\Room;
use App\Models\RoomBook;

use App\Models\User;
use App\Models\Shop;
use App\Models\TransactionLog;

use App\Services\Reports\BillingReportService;
use App\Services\Reports\TransactionReportService;
use App\Services\Reports\SearchReportService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionReportController extends Controller
{

    public function __construct(protected TransactionReportService $TransactionReportService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|RedirectResponse
    {
         // dd($request->all());
        try {
             $shop = Shop::orderBy('ShopName')->get(['id', 'ShopName']);
            $reportData = collect();

            if ($request->has('from_date') && $request->has('to_date') || $request->has('payment_type')) {

                 // dd($request->all());
              $validate =  $request->validate([
                    'from_date' => 'nullable|date',
                    'to_date' => 'nullable|date|after_or_equal:from_date',
                      'shopId' => 'nullable|exists:tbl_shopId,id',
                     'payment_type' => 'nullable|in:1,2,3',
                ]);
              // dd($validate);
              try {
                    $query = $this->TransactionReportService->all($request);
                    // dd($query->items());
                    // dd($query);
                } catch (\Throwable $e) {
                    dd('Error in TransactionReportService:', $e->getMessage(), $e->getTraceAsString());
                }

               //  $query = $this->TransactionReportService->all($request);
               // dd($query);
                // return view('reports.transaction_reports.index', compact('query'));
                return view('reports.transaction_reports.index', [
                    'shop' => $shop, 
                    'query' => $query
                    // 'bookingStatsByDate' => $bookingStatsByDate,
                    // 'grandTotal' => $grandTotal,
                    // 'from_date' => $request->from_date,
                    // 'to_date' => $request->to_date
                ]  );
            }

            return view('reports.transaction_reports.index', [
                    'shop' => $shop
                    // 'bookingStatsByDate' => $bookingStatsByDate,
                    // 'grandTotal' => $grandTotal,
                    // 'from_date' => $request->from_date,
                    // 'to_date' => $request->to_date
                ]  );

        } catch (Exception $e) {
            info('Error showing Billing Report!', [$e]);

            return redirect()->back()->with('error', 'Billing Report showing failed!');
        }
    }

    public function searchTx(Request $request): View|RedirectResponse
    {
        // dd($request->all()) ; 
          $shop = Shop::orderBy('ShopName')->get(['id', 'ShopName']);
        if($request->has('searchTx')){
             $input = $request['searchTx'] ; 
            if(substr( $input, 0,1)=='c'){
                  $input2 =  substr($input, 1);  
                  $res = TransactionLog::where('customerId',$input2)
                        ->orderBy('id','asc')
                         ->paginate(25); 
                        // ->get() ; 
                  return view('reports.transaction_reports.search_result', [
                    'shop' => $shop, 
                    'customer' =>  $input2, 
                     'audit' =>  '', 
                    'query' => $res
                     
                ]  );
            }
            else{
                $res = TransactionLog::where('tagsl',$input)
                        ->where('Date',Date('Y-m-d'))
                        ->orderBy('id','desc')
                         ->paginate(25); 
                        // ->get() ; 


                  // Get IDs that are referenced in audit
                $referencedIds = DB::table('audit_txn')
                    ->whereIn('RecptId', $res->pluck('id'))  // adjust column if needed
                    ->pluck('RecptId')
                    ->toArray();
                  return view('reports.transaction_reports.search_result', [
                    'shop' => $shop, 
                  'customer' => '' ,
                   'audit' =>  $referencedIds ,
                    'query' => $res
                
                ]  );
           
            }
        }

    }

    public function billingSummaryReport(Request $request): View|RedirectResponse
    {
        try {
            if ($request->has('from_date') && $request->has('to_date')) {
                $validate = $request->validate([
                    'from_date' => 'nullable|date',
                    'to_date' => 'nullable|date|after_or_equal:from_date',
                ]);

                // Get the grouped billing data
                $summary = BillMaster::with(['booking.room', 'user'])
                    ->whereBetween('bill_date', [
                        $request->from_date . ' 00:00:00',
                        $request->to_date . ' 23:59:59'
                    ])
                    ->whereNull('merge_master_id')
                    ->orderBy('bill_date')
                    ->get()
                    ->groupBy(function($item) {
                        return Carbon::parse($item->bill_date)->format('Y-m-d');
                    });

                // Get booking statistics for each date in the range
                $dateRange = CarbonPeriod::create($request->from_date, $request->to_date);
                $bookingStatsByDate = collect();

                foreach ($dateRange as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $stats = RoomBook::whereDate('checkin_at', '<=', $dateStr)
                        ->whereDate('checkout_at', '>=', $dateStr)
                        ->selectRaw('
                        COUNT(*) as total_bookings,
                        SUM(CASE WHEN DATE(checkin_at) = ? THEN 1 ELSE 0 END) as checkins,
                        SUM(CASE WHEN DATE(checkout_at) = ? THEN 1 ELSE 0 END) as checkouts
                    ', [$dateStr, $dateStr])
                        ->first();

                    $bookingStatsByDate->put($dateStr, [
                        'total_bookings' => $stats->total_bookings ?? 0,
                        'checkins' => $stats->checkins ?? 0,
                        'checkouts' => $stats->checkouts ?? 0
                    ]);
                }

                // Get overall booking statistics for the date range
                $bookingStats = RoomBook::whereBetween('checkin_at', [$request->from_date, $request->to_date])
                    ->orWhereBetween('checkout_at', [$request->from_date, $request->to_date])
                    ->selectRaw('
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN DATE(checkin_at) BETWEEN ? AND ? THEN 1 ELSE 0 END) as total_checkins,
                    SUM(CASE WHEN DATE(checkout_at) BETWEEN ? AND ? THEN 1 ELSE 0 END) as total_checkouts
                ', [
                        $request->from_date, $request->to_date,
                        $request->from_date, $request->to_date
                    ])
                    ->first();

                // Calculate billing grand totals
                $grandTotal = [
                    'total_invoices' => 0,
                    'total_amount' => 0,
                    'total_paid' => 0,
                    'total_unpaid' => 0,
                    'paid_count' => 0,
                    'unpaid_count' => 0,
                    'total_bookings' => $bookingStats->total_bookings ?? 0,
                    'total_checkins' => $bookingStats->total_checkins ?? 0,
                    'total_checkouts' => $bookingStats->total_checkouts ?? 0
                ];

                foreach ($summary as $date => $bills) {
                    $grandTotal['total_invoices'] += $bills->count();
                    $grandTotal['total_amount'] += $bills->sum('total_payable');
                    $grandTotal['total_paid'] += $bills->where('paid_status', 1)->sum('total_payable');
                    $grandTotal['total_unpaid'] += $bills->where('paid_status', 0)->sum('total_payable');
                    $grandTotal['paid_count'] += $bills->where('paid_status', 1)->count();
                    $grandTotal['unpaid_count'] += $bills->where('paid_status', 0)->count();
                }

                return view('reports.billing_summary_reports.index', [
                    'summary' => $summary,
                    'bookingStatsByDate' => $bookingStatsByDate,
                    'grandTotal' => $grandTotal,
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date
                ]);
            }

            return view('reports.billing_summary_reports.index');

        } catch (Exception $e) {
            info('Error showing Billing Summary Report!', [$e]);
            return redirect()->back()->with('error', 'Billing Summary Report showing failed!');
        }
    }

    public function roomBookingCalendar(Request $request): View
    {
        $rooms = Room::with(['roomType', 'floor'])->get();
        $bookingData = collect();

        if ($request->has('from_date') && $request->has('to_date')) {
            $request->validate([
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
            ]);

            $dateRange = CarbonPeriod::create($request->from_date, $request->to_date);
            $dates = collect($dateRange->toArray())->map(fn($date) => $date->format('Y-m-d'));

            // Get all bookings in the date range
            $bookings = RoomBook::with(['room', 'user'])
                ->where(function($query) use ($request) {
                    $query->whereBetween('checkin_at', [$request->from_date, $request->to_date])
                        ->orWhereBetween('checkout_at', [$request->from_date, $request->to_date])
                        ->orWhere(function($q) use ($request) {
                            $q->where('checkin_at', '<=', $request->from_date)
                                ->where('checkout_at', '>=', $request->to_date);
                        });
                })
                ->get()
                ->map(function($booking) {
                    // Ensure dates are Carbon instances
                    $booking->checkin_at = \Carbon\Carbon::parse($booking->checkin_at);
                    $booking->checkout_at = \Carbon\Carbon::parse($booking->checkout_at);
                    return $booking;
                });

            // Organize data by room and date
            $bookingData = $rooms->mapWithKeys(function($room) use ($dates, $bookings) {
                $roomBookings = $bookings->where('room_id', $room->id);

                $datesData = $dates->mapWithKeys(function($date) use ($roomBookings) {
                    $dateCarbon = \Carbon\Carbon::parse($date);

                    // Get all bookings for this date
                    $bookingsForDate = $roomBookings->filter(function($booking) use ($dateCarbon) {
                        return $dateCarbon->between($booking->checkin_at, $booking->checkout_at);
                    });

                    $isBooked = $bookingsForDate->isNotEmpty();

                    // Get the first booking (or modify this if you want to show multiple bookings)
                    $booking = $isBooked ? $bookingsForDate->first() : null;

                    return [$date => [
                        'booked' => $isBooked,
                        'user_id' => $booking ? $booking->user?->user_id : null,
                        'rank_name' => $booking ? $booking->user?->rank?->name : null,
                        'user_name' => $booking ? $booking->user?->name : null,
                        'booking_id' => $booking ? $booking->id : null,
                        // Add any other booking info you want to display
                        'checkin_at' => $booking ? $booking->checkin_at->format('Y-m-d') : null,
                        'checkout_at' => $booking ? $booking->checkout_at->format('Y-m-d') : null,
                    ]];
                });

                return [$room->id => [
                    'room' => $room,
                    'dates' => $datesData
                ]];
            });
        }

        return view('reports.room_booking_calendar.index', [
            'rooms' => $rooms,
            'bookingData' => $bookingData,
            'from_date' => $request->from_date ?? null,
            'to_date' => $request->to_date ?? null
        ]);
    }

    public function userBookingReport(Request $request): View
    {
        $users = User::orderBy('name')->get(['id', 'name', 'user_id']);
        $reportData = collect();

        if ($request->has('from_date') && $request->has('to_date')) {
            $request->validate([
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'user_id' => 'nullable|exists:users,id'
            ]);

            $dateRange = CarbonPeriod::create($request->from_date, $request->to_date);
            $dates = collect($dateRange->toArray())->map(fn($date) => $date->format('Y-m-d'));

            $bookingsQuery = RoomBook::with([
                'room.roomType',
                'user.rank'
            ])
                ->where(function($query) use ($request) {
                    $query->whereBetween('checkin_at', [$request->from_date, $request->to_date])
                        ->orWhereBetween('checkout_at', [$request->from_date, $request->to_date])
                        ->orWhere(function($q) use ($request) {
                            $q->where('checkin_at', '<=', $request->from_date)
                                ->where('checkout_at', '>=', $request->to_date);
                        });
                });

            if ($request->filled('user_id')) {
                $bookingsQuery->where('user_id', $request->user_id);
            }

            $bookings = $bookingsQuery->get()
                ->map(function($booking) {
                    try {
                        $booking->checkin_at = \Carbon\Carbon::parse($booking->checkin_at);
                        $booking->checkout_at = \Carbon\Carbon::parse($booking->checkout_at);
                        return $booking;
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->filter();

            $reportData = $bookings->groupBy('user_id')->map(function($userBookings, $userId) use ($dates) {
                $user = optional($userBookings->first())->user;

                if (!$user) {
                    return null;
                }

                // Group by room (including null) and mark unassigned rooms
                $roomGroups = $userBookings->groupBy(function($booking) {
                    return $booking->room_id ? 'room-'.$booking->room_id : 'unassigned';
                });

                $roomData = $roomGroups->map(function($roomBookings, $roomKey) use ($dates) {
                    $isUnassigned = $roomKey === 'unassigned';
                    $room = $isUnassigned ? null : optional($roomBookings->first())->room;

                    $datesData = $dates->map(function($date) use ($roomBookings) {
                        try {
                            $dateCarbon = \Carbon\Carbon::parse($date);
                            $bookingForDate = $roomBookings->first(function($booking) use ($dateCarbon) {
                                return $dateCarbon->between(
                                    \Carbon\Carbon::parse($booking->checkin_at),
                                    \Carbon\Carbon::parse($booking->checkout_at)
                                );
                            });

                            return [
                                'booked' => $bookingForDate !== null,
                                'booking_id' => optional($bookingForDate)->id,
                                'checkin_at' => optional($bookingForDate)->checkin_at?->format('Y-m-d'),
                                'checkout_at' => optional($bookingForDate)->checkout_at?->format('Y-m-d'),
                            ];
                        } catch (\Exception $e) {
                            return [
                                'booked' => false,
                                'booking_id' => null,
                                'checkin_at' => null,
                                'checkout_at' => null,
                            ];
                        }
                    });

                    return [
                        'room' => $isUnassigned ? (object)[
                            'name' => 'Unassigned Room',
                            'room_no' => 'N/A',
                            'roomType' => (object)['name' => 'N/A'],
                            'is_unassigned' => true
                        ] : $room,
                        'dates' => $datesData,
                        'total_days_booked' => $datesData->where('booked', true)->count(),
                        'is_unassigned' => $isUnassigned
                    ];
                });

                return [
                    'user' => $user,
                    'rooms' => $roomData,
                    'total_bookings' => $userBookings->count(),
                    'total_days_booked' => $roomData->sum('total_days_booked')
                ];
            })->filter();
        }

        return view('reports.user_booking_report.index', [
            'users' => $users,
            'reportData' => $reportData,
            'from_date' => $request->from_date ?? null,
            'to_date' => $request->to_date ?? null,
            'selected_user_id' => $request->user_id ?? null,
            'dateRange' => isset($dateRange) ? $dateRange : CarbonPeriod::create(now(), now())
        ]);
    }

    // PaymentController.php
    public function paymentHistoryReport(Request $request)
    {
        // Get all users for the filter dropdown
        $users = User::where('is_active', 1)->orderBy('name')->get();

        // Start query for paid bills only
        $query = BillMaster::with('user')
            ->where('paid_status', 1)

            ->orderBy('paid_at', 'desc');

        // Apply date filters if provided
        if ($request->filled('from_date')) {
            $query->whereDate('paid_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('paid_at', '<=', $request->to_date);
        }

        // Apply user filter if provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
            $selectedUser = User::find($request->user_id);
        }

        // Apply payment type filter if provided
        if ($request->filled('payment_type')) {
            $query->where('pay_type', $request->payment_type);
        }

        // Group by transaction number to show consolidated payments
        if (!$request->filled('show_all')) {
            $query->selectRaw('
            MIN(id) as id,
            paid_txn_no,
            MAX(user_id) as user_id,
            MAX(paid_at) as paid_at,
            SUM(total_payable) as total_payable,
            MAX(pay_type) as pay_type,
            MAX(paid_status) as paid_status,
            CASE
                WHEN COUNT(DISTINCT invoice_no) = 1 THEN MAX(invoice_no)
                ELSE NULL
            END as invoice_no
        ')->groupBy('paid_txn_no');
        }

        // Get the results
        $payments = $query->paginate(25)->withQueryString();

        // Get counts for summary
        $cashCount = BillMaster::where('paid_status', 1)
            ->where('pay_type', 1)
            ->when($request->filled('from_date'), function($q) use ($request) {
                $q->whereDate('paid_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function($q) use ($request) {
                $q->whereDate('paid_at', '<=', $request->to_date);
            })
            ->when($request->filled('user_id'), function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->distinct('paid_txn_no')
            ->count('paid_txn_no');

        $onlineCount = BillMaster::where('paid_status', 1)
            ->where('pay_type', 2)
            ->when($request->filled('from_date'), function($q) use ($request) {
                $q->whereDate('paid_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function($q) use ($request) {
                $q->whereDate('paid_at', '<=', $request->to_date);
            })
            ->when($request->filled('user_id'), function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->groupBy('paid_txn_no')
            ->count();

        return view('reports.billing_reports.payment_report', [
            'payments' => $payments,
            'users' => $users,
            'selectedUser' => $selectedUser ?? null,
            'cashCount' => $cashCount,
            'onlineCount' => $onlineCount,
        ]);
    }
}

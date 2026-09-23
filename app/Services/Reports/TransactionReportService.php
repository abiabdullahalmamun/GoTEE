<?php

namespace App\Services\Reports;

use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionReportService
{
    public function all(Request $request): LengthAwarePaginator
    {
        $length = $request->input('per_page', 10);
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $paymentType = $request->input('payment_type');
         $shopType = $request->input('shopId');

        $query = TransactionLog::query()
            ->with('shop:id,ShopName')
            ->with('operator:id,emp_name')
            ->whereBetween('Date', [$fromDate, $toDate])
             ->when(!is_null($paymentType), function ($query) use ($paymentType) {
                return $query->where('add_del', $paymentType);
            })
            ->when(!is_null($shopType), function ($query) use ($shopType) {
                return $query->where('shopId', $shopType);
            })
              ->latest('Date');
            // ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            //     // $start = min($fromDate, $toDate) . ' 00:00:00';
            //     // $end = max($fromDate, $toDate) . ' 23:59:59';
            //     // return $query->whereBetween('Date', [$start, $end]); 
            //     return $query->whereBetween('Date', [$fromDate, $toDate]);
            // })
            // ->whereNull('merge_master_id')
            // ->when($paymentType, function ($query) use ($paymentType) {
                // return $query
                    // ->when($paymentType === 'paid', function ($query) {
                    //     return $query->where('paid_status', 1);
                    // })
                    // ->when($paymentType === 'unpaid', function ($query) {
                    //     return $query->where('paid_status', 0);
                    // })
                    // ->when($paymentType === 'all', function ($query) {
                    //     return $query->whereIn('paid_status', [0, 1]);
                    // });
            // })
           

        return $query->paginate($length);
    }
}

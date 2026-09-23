<?php

namespace App\Services\Reports;

use App\Models\BillMaster;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BillingReportService
{
    public function all(Request $request): LengthAwarePaginator
    {
        $length = $request->input('per_page', 10);
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $paymentType = $request->input('payment_type');

        $query = BillMaster::query()
            ->with('user:id,name')

            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $start = min($fromDate, $toDate) . ' 00:00:00';
                $end = max($fromDate, $toDate) . ' 23:59:59';
                return $query->whereBetween('created_at', [$start, $end]);
            })
            ->whereNull('merge_master_id')
            ->when($paymentType, function ($query) use ($paymentType) {
                return $query
                    ->when($paymentType === 'paid', function ($query) {
                        return $query->where('paid_status', 1);
                    })
                    ->when($paymentType === 'unpaid', function ($query) {
                        return $query->where('paid_status', 0);
                    })
                    ->when($paymentType === 'all', function ($query) {
                        return $query->whereIn('paid_status', [0, 1]);
                    });
            })
            ->latest('created_at');

        return $query->paginate($length);
    }
}

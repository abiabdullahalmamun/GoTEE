@extends('layouts.app')

@push('style')
    <style>
        .print-title {
            display: none;
        }

        @media print {
            .print-title {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
            }

            .print-title h1 {
                font-size: 24px;
                font-weight: bold;
            }

            .print-title p {
                font-size: 16px;
                font-weight: bold;
            }

            .hidden-print {
                display: block !important;
            }

            body * {
                visibility: hidden !important;
            }

            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            #print-area,
            #print-area * {
                visibility: visible !important;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            #printButton {
                display: none !important;
            }
            .flex #perPage, .flex .text-gray-700, .flex .justify-end nav {
                display: none !important;
            }
        }

        /* Modern styling additions */
        .filter-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .payment-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .payment-badge.cash {
            background-color: #ECFDF5;
            color: #065F46;
        }

        .payment-badge.online {
            background-color: #EFF6FF;
            color: #1E40AF;
        }

        .total-summary {
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto">
        <x-page-header title="Payment Information Report" />

        <div class="filter-card mt-1 mx-auto">
            <div class="flex justify-between items-center mb-6">
                <form method="GET" class="flex gap-4 items-end flex-wrap" id="paymentReportsSearch">
                    <!-- Date Range Filters -->
                    <div>
                        <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                               class="px-3 py-2 border border-gray-300 rounded w-44 focus:ring-primary focus:outline-none">
                    </div>

                    <div>
                        <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                               class="px-3 py-2 border border-gray-300 rounded w-44 focus:ring-primary focus:outline-none">
                    </div>

                    <!-- User Filter -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <select name="user_id" id="user_id"
                                class="w-52 px-3 py-2 border rounded @error('user_id') border-red-500 @enderror">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->user_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Type Filter -->
                    <div>
                        <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                        <select name="payment_type" id="payment_type"
                                class="w-52 px-3 py-2 border rounded @error('payment_type') border-red-500 @enderror">
                            <option value="">All Types</option>
                            <option value="1" {{ request('payment_type') == '1' ? 'selected' : '' }}>Cash</option>
                            <option value="2" {{ request('payment_type') == '2' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark mt-1">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                        <button type="button" id="resetBtn"
                                class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mt-1 ml-2">
                            <i class="fas fa-undo mr-2"></i> Reset
                        </button>
                    </div>
                </form>

                @if (isset($payments) && $payments->count() > 0)
                    <button id="openPrint" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                @endif
            </div>

            <!-- Summary Cards -->
            @if(isset($payments) && $payments->count() > 0)
                <div class="total-summary grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-blue-50">
                        <h3 class="text-sm font-medium text-gray-500">Total Payments</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $payments->count() }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-green-50">
                        <h3 class="text-sm font-medium text-gray-500">Cash Payments</h3>
                        <p class="text-2xl font-bold text-green-600">{{ $cashCount ?? 0 }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-indigo-50">
                        <h3 class="text-sm font-medium text-gray-500">Online Payments</h3>
                        <p class="text-2xl font-bold text-indigo-600">{{ $onlineCount ?? 0 }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white shadow rounded p-6 mt-4 mx-auto">
            <div class="mt-4" id="print-area">
                <div id="printTitle" class="print-title hidden-print">
                    <h1 class="text-2xl font-bold">Payment Information Report</h1>
                    <p class="text-md font-bold">Total Payments: @if(isset($payments)) ({{ $payments->count() }}) @endif</p>
                    <p class="text-md font-bold">Date Range: {{ request('from_date') }} to {{ request('to_date') }}</p>
                    <p class="text-md font-bold">User: {{ isset($selectedUser) ? $selectedUser->name.' ('.$selectedUser->user_id.')' : 'All Users' }}</p>
                    <p class="text-md font-bold">Payment Type: {{ request('payment_type') ? (request('payment_type') == 1 ? 'Cash' : 'Online') : 'All Types' }}</p>
                </div>

                <div id="parent-payment-list" class="payment-list overflow-y-hidden overflow-x-auto">
                    <table class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">SL</th>
                            <th class="border px-4 py-2 text-left">Txn No</th>
                            <th class="border px-4 py-2 text-left">Invoice No</th>
                            <th class="border px-4 py-2 text-left">User</th>
                            <th class="border px-4 py-2 text-left">Payment Date</th>
                            <th class="border px-4 py-2 text-left">Amount</th>
                            <th class="border px-4 py-2 text-left">Type</th>
                            <th class="border px-4 py-2 text-left">Status</th>
                            <th class="border px-4 py-2 text-left">Bills Count</th>
                        </tr>
                        </thead>
                        <tbody id="datatable">
                        @if (!isset($payments))
                            <tr class="border hover:bg-gray-50 transform transition-all duration-500 ease-out row-animation h-80">
                                <td colspan="9" class="border px-4 py-4 text-center">
                                    <span class="text-green-700 text-2xl"><i class="fa fa-search"></i></span>
                                    <span class="text-gray-700 text-2xl ml-2">Search Payment Records</span>
                                </td>
                            </tr>
                        @else
                            @forelse ($payments as $key => $payment)
                                <tr class="border hover:bg-gray-50 transform transition-all duration-500 ease-out row-animation">
                                    <td class="border px-4 py-2">{{ ++$key }}</td>
                                    <td class="border px-4 py-2 text-left font-medium text-blue-600">
                                        <a target="_blank" href="{{ URL::to('/payment/receipt/'.$payment->paid_txn_no) }}">{{ $payment->paid_txn_no ?? 'N/A' }}</a>
                                    </td>
                                    <td class="border px-4 py-2 text-left">
                                        @if($payment->invoice_no)
                                            {{ $payment->invoice_no }}
                                        @else
                                            <span class="text-gray-400">Multiple</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-left">
                                        {{ $payment->user?->name ?? 'N/A' }}
                                        @if($payment->user?->user_id)
                                            <br><span class="text-xs text-gray-500">{{ $payment->user->user_id }}</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-left">
                                        {{ Carbon\Carbon::parse($payment->paid_at)->format('d-m-Y h:i A') }}
                                    </td>
                                    <td class="border px-4 py-2 text-left font-medium">
                                        {{ number_format($payment->total_payable, 2) }}
                                    </td>
                                    <td class="border px-4 py-2 text-left">
                                        @if($payment->pay_type == 1)
                                            <span class="payment-badge cash">
                                                    <i class="fas fa-money-bill-wave mr-1"></i> Cash
                                                </span>
                                        @else
                                            <span class="payment-badge online">
                                                    <i class="fas fa-credit-card mr-1"></i> Online
                                                </span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-left">
                                        @if ($payment->paid_status == 1)
                                            <span class="badge badge-success text-green-700">Paid</span>
                                        @else
                                            <span class="badge badge-danger text-red-700">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-left">
                                        @if($payment->invoice_no)
                                            1
                                        @else
                                            {{ App\Models\BillMaster::where('paid_txn_no', $payment->paid_txn_no)->count() }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="border hover:bg-gray-50 transform transition-all duration-500 ease-out row-animation">
                                    <td class="border px-4 py-1" colspan="9">
                                        <p class="py-2 text-center">No payment records found</p>
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                        </tbody>
                        @if(isset($payments) && $payments->count() > 0)
                            <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5" class="border px-4 py-2 text-right font-bold">Total:</td>
                                <td class="border px-4 py-2 text-left font-bold">
                                    {{ number_format($payments->sum('total_payable'), 2) }}
                                </td>
                                <td colspan="3" class="border px-4 py-2"></td>
                            </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                @if(isset($payments))
                    <x-table-pagination :paginator="$payments->appends(request()->query())" />
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reset form
            document.getElementById('resetBtn').addEventListener('click', function() {
                document.getElementById('paymentReportsSearch').reset();
                window.location.href = "{{ route('reports.patyment-history-report') }}";
            });

            // Print functionality
            document.getElementById('openPrint')?.addEventListener('click', function() {
                window.print();
            });
        });
    </script>
@endpush

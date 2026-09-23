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

      #print-area td:last-child,
      #print-area th:last-child {
            display: none !important;
      }

      #printButton {
            display: none !important;
      }

      .flex #perPage,
      .flex .text-gray-700,
      .flex .justify-end nav {
            display: none !important;
      }
}
</style>
@endpush

@section('content')
<div class="mx-auto">
      <x-page-header title="Transaction Reports" />
      <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <div class="flex justify-between items-center mb-6">

                  <form method="GET" class="flex gap-4 items-end flex-wrap" id="billingReportsSearch">
                        <div>
                              <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From
                                    Date</label>
                              <input type="date" name="from_date" id="from_date" value="{{Date('Y-m-d')}}"
                                    class="px-3 py-2 border border-gray-300 rounded w-44 focus:ring-primary focus:outline-none">
                        </div>

                        <div>
                              <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                              <input type="date" name="to_date" id="to_date" value="{{Date('Y-m-d')}}"
                                    class="px-3 py-2 border border-gray-300 rounded w-44 focus:ring-primary focus:outline-none">
                        </div>

                 <!-- User Filter -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Shop</label>
                        <select name="shopId" id="shopId"
                                class="w-52 px-3 py-2 border rounded @error('shop_id') border-red-500 @enderror">
                            <option value="">All</option>
                            @foreach($shop as $user)
                                <option value="{{ $user->id }}" {{ request('shop_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->ShopName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                        <div>
                              <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                              <select name="payment_type" id="payment_type"
                                    class="w-52 px-3 py-2 border rounded @error('payment_type') border-red-500 @enderror">
                                    <option value="">All</option>
                                    <option value="1">Recharge</option>
                                    <option value="2">Sale</option>
                                    <option value="3">Refund</option>
                                   

                                   <!--  @foreach (App\Const\Str::PAYMENT_TYPE as $key => $type)
                                    <option value="{{ $type }}"
                                          {{ request('payment_type') == $type ? 'selected' : '' }}>
                                          {{ $key }}
                                    </option>
                                    @endforeach -->
                              </select>
                              @error('payment_type')
                              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                              @enderror
                        </div>

                        <div>
                              <button type="submit"
                                    class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark mt-1"><i
                                          class="fas fa-search mr-2"></i> Find</button>
                              <button type="button" id="resetBtn"
                                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mt-1 ml-2">
                                    <i class="fas fa-undo mr-2"></i> Reset
                              </button>
                        </div>
                  </form>

                  @if (isset($query) && $query->count() > 0)
                  <button id="openPrint" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                        Print
                  </button>
                  @endif

            </div>
            <div class="mt-4" id="print-area">
                  <div id="printTitle" class="print-title hidden-print">
                        <h1 class="text-2xl font-bold">Billing Report</h1>
                        <p class="text-md font-bold">Total: @if(isset($query)) ({{ $query->count() }}) @endif</p>
                        <p class="text-md font-bold">From: {{ request('from_date') }}</p>
                        <p class="text-md font-bold">To: {{ request('to_date') }}</p>
                        <p class="text-md font-bold">Payment Type: {{ request('payment_type') ?? 'All' }}</p>
                  </div>

                  <div id="parent-bill-head-list" class="bill-head-list overflow-y-hidden overflow-x-auto ">
                        <table class="w-full border border-gray-200 text-sm">
                              <thead class="bg-gray-100">
                                    <tr>
                                          <th class="border px-4 py-2 text-left">SL</th>
                                          <th class="border px-4 py-2 text-left">Date</th>
                                          <th class="border px-4 py-2 text-left">Tag</th>
                                           <th class="border px-4 py-2 text-left">CustomerID</th>
                                           <th class="border px-4 py-2 text-left">Amount</th>

                                            <th class="border px-4 py-2 text-left">Balance</th>
                                          <th class="border px-4 py-2 text-left">Shop</th>
                                           <th class="border px-4 py-2 text-left">Device</th>
                                           <th class="border px-4 py-2 text-left">Action</th>

                                              <th class="border px-4 py-2 text-left">Operator</th>
                                           <th class="border px-4 py-2 text-left">EventTime</th>


                                    </tr>
                              </thead>
                              <tbody id="datatable">
                                    @if (!isset($query))
                                    <tr
                                          class="border hover:bg-gray-50 transform transition-all duration-500 ease-out row-animation h-80">
                                          <td colspan="12" class="border px-4 py-4 text-center">
                                                <span class="text-green-700 text-2xl"><i
                                                            class="fa fa-search"></i></span>
                                                <span class="text-gray-700 text-2xl ml-2">Find Transaction Report</span>
                                          </td>
                                    </tr>
                                    @else
                                    @forelse ($query as $key => $data)
                                    <tr
                                          class="border hover:bg-gray-50 transform  transition-all duration-500 ease-out row-animation">
                                          <td class="border px-4 py-2">{{ ++$key }}</td>
                                          <td class="border px-4 py-2 text-left">{{ $data->Date ?? '' }}</td>
                                          <td class="border px-4 py-2 text-left">{{ $data->tagsl ?? '' }}</td>
                                          <td class="border px-4 py-2 text-left">{{ $data->customerId ?? '' }}</td>
                                           <td class="border px-4 py-2 text-right">{{ $data->amount ?? '' }}</td>

                                           <td class="border px-4 py-2 text-right">{{ $data->rem_bal ?? '' }}</td>
                                        <td class="border px-4 py-2 text-left">{{ $data->shop?->ShopName ?? '' }}</td>
                                       <td class="border px-4 py-2 text-left">{{ $data->devID ?? '' }}</td>
                                        <td class="border px-4 py-2 text-left">
                                                @if ($data->add_del == 1)
                                                <span class="badge badge-success text-red-700">Recharge</span>
                                                @elseif ($data->add_del == 2)
                                                <span class="badge badge-danger text-green-700">Sale</span>
                                                @elseif ($data->add_del == 3)
                                                <span class="badge badge-danger text-blue-700">Refund</span>
                                                @endif
                                          </td>
                                       
                                        <td class="border px-4 py-2 text-left">{{ $data->operator?->emp_name ?? '' }}</td>
                                        <td class="border px-4 py-2 text-left">{{ $data->created_at ?? '' }}</td>
                              
                                      
                                    </tr>
                                    @empty
                                    <tr
                                          class="border hover:bg-gray-50 transform transition-all duration-500 ease-out row-animation">
                                          <td class="border px-4 py-1" colspan="12">
                                                <p class="py-2 text-center">No data found</p>
                                          </td>
                                    </tr>
                                    @endforelse
                                    @endif

                              </tbody>
                           @if(isset($query) && $query->count() > 0)
                            <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="border px-4 py-2 text-right font-bold">Total:</td>
                                <td class="border px-4 py-2 text-right font-bold">
                                    {{ number_format($query->sum('amount'), 2) }}
                                </td>
                                <td class="border px-4 py-2 text-right font-bold">
                                    {{ number_format($query->sum('rem_bal'), 2) }}
                                </td>
                                <td colspan="3" class="border px-4 py-2"></td>
                            </tr>
                            </tfoot>
                              @endif
                        </table>
                  </div>
                  <x-table-pagination :paginator="!isset($query)
                    ? new \Illuminate\Pagination\LengthAwarePaginator([], 1, 1, 1)
                    : $query->appends(request()->query())" />

                  {{-- <x-table-pagination :paginator="!isset($query) ? new \Illuminate\Pagination\LengthAwarePaginator([], 1, 1, 1) : $query" /> --}}
            </div>
      </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/scripts/billing_reports.js'])
@vite(['resources/js/scripts/index_script.js'])
@endpush

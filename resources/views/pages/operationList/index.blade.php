@extends('layouts.app')

@push('style')
    {{-- DataTables CSS is bundled via Vite in device.js --}}
@endpush

@section('content')
    <div class="mx-auto">
        <x-page-header title="OperationList Management" />

        {{-- Alerts --}}
<!--         @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
                @if(session('sync_counts'))
                    @php $counts = session('sync_counts'); @endphp

                    <div class="flex gap-5 mt-2 font-semibold">
                        <span>Inserted: {{ $counts['inserted'] }}</span>
                        <span>Updated: {{ $counts['updated'] }}</span>
                        <span>Dropped: {{ $counts['dropped'] }}</span>
                    </div>
                @endif
            </div>
        @endif -->
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-3">
                {{ session('success') }}

                @if(session('sync_counts'))
                    @php $counts = session('sync_counts'); @endphp
                    <div class="flex items-center gap-6 mt-2 font-semibold">
                        <span>Inserted: {{ $counts['inserted'] }}</span>
                        <span class="border-l pl-6">Updated: {{ $counts['updated'] }}</span>
                        <span class="border-l pl-6">Dropped: {{ $counts['dropped'] }}</span>
                    </div>
                @endif
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main card --}}
        <div class="bg-white shadow rounded p-3 mt-1 mx-auto">

            <div class="flex gap-2 w-full">

                {{-- LEFT: OrderCode buttons --}}
        <div id="Ordercode"
             class="flex-none w-[10ch] flex flex-col gap-1 h-[calc(100vh-160px)] overflow-y-auto overflow-x-hidden">

            @foreach($query as $order)

                <button type="button"
                        class="order-select-btn flex-none w-full whitespace-nowrap overflow-hidden text-ellipsis text-center px-1 py-2 rounded border border-purple-400 font-medium transition"
                        style="background-color: #9575CD; color: white;"
                        data-order-code="{{ $order->OrderCode }}"
                        data-order-no="{{ $order->OrderNo }}"
                        data-url="{{ route('operationList.load', $order->OrderCode) }}">

                    {{ $order->OrderCode }}

                </button>

            @endforeach

        </div>
                {{-- RIGHT: Remaining width --}}
                <div id="details" class="flex-1 min-w-0 bg-white rounded border border-gray-200 p-4">
                  
                    {{-- OperationList will load here --}}

                </div>

            </div>

        </div>
 
    </div>

    {{-- Add/Edit Operation List Modal --}}

{{-- Add/Edit Operation List Modal --}}
<div id="ShopModal"
     class="fixed inset-0 bg-gray-800/50 hidden z-[1100] items-center justify-center">

    <div class="bg-white p-6 rounded-lg w-[1200px] max-w-[98vw]
                max-h-[92vh] overflow-y-auto relative">

        <h2 class="text-lg font-bold mb-4" id="modalTitle">
            Add/Edit OperationList
        </h2>

        @include('pages.operationList.partials.create')

    </div>
</div>

  

    {{-- Edit Modal --}}
<!--     <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-lg font-bold mb-4">Edit Order</h2>
            @include('pages.operationList.partials.edit')
        </div>
    </div>
 -->

@endsection

@push('scripts')
    @vite(['resources/js/scripts/operationList.js'])
@endpush

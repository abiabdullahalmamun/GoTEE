@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Digitization Report" />
           @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
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
    
    {{-- Filter Form --}}
	<div class="bg-white shadow rounded p-2 mt-1 mx-auto">
		<div class="appDigitization">
			<form method="GET" action="{{ url('/digitization-report') }}" class="mb-2 flex gap-2">
			    @csrf
		        <!-- @method('POST') -->

<div class="flex flex-col gap-3">

    <!-- Row 1 -->
    <div class="flex gap-2">
        <div class="flex-1 flex items-center gap-2">
            <label class="w-20">From:</label>
            <input id="fromDate" type="date" name="from_date"
                value="{{ request('from_date') }}"
                class="flex-1 border p-2 rounded">
        </div>

        <div class="flex-1 flex items-center gap-2">
            <label class="w-20">To:</label>
            <input id="toDate" type="date" name="to_date"
                value="{{ request('to_date') }}"
                class="flex-1 border p-2 rounded">
        </div>

        <div class="flex items-center gap-2">
            <label class="w-20">Center:</label>
            <select name="center" id="center" class="w-40 border p-2 rounded">
                @if($role == 1 || $role == 5 || $role == 13)
                    <option value="">ALL</option>
                    @foreach($centers as $cnt)
                        <option value="{{ $cnt->id }}" {{ $cenId == $cnt->id ? 'selected' : '' }}>
                            {{ $cnt->center_name }}
                        </option>
                    @endforeach
                @else
                    @php
                        $userCenter = collect($centers)->firstWhere('id', $cenId);
                    @endphp
                    <option value="{{ $cenId }}" selected>
                        {{ $userCenter['center_name'] ?? 'N/A' }}
                    </option>
                @endif
            </select>
        </div>
    </div>

    <!-- Row 2 -->
    <div class="flex justify-between items-center">
        <div class="flex space-x-2">
            <button type="button"
                onclick="submitFormTo('{{ route('digitization-report.summary') }}', 'visa')"
                class="bg-purple-500 text-white px-2 py-1 rounded text-sm">
                 Summary
            </button>

        </div>

        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center">
            Search
        </button>
    </div>

</div>


			</form>
		</div>
		<hr class="my-2">
		
		{{-- Export Buttons --}}
	<div class="flex gap-2 items-center">
    <a href="{{ route('digitization-report.excel', request()->all()) }}" 
       class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>

  <!--   <a href="{{ route('undelivered-pass.pdf', request()->all()) }}" 
       class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a>
 -->
    <label class="ml-auto pr-6"><b>Total:{{$total}}</b></label>
</div>
		
	</div>

	
    {{-- DataTable --}}
    <div class="AppDataTable bg-white shadow rounded p-2 mt-1 mx-auto">
		<div class="overflow-x-auto">
			<table id="reportTable" class="table-auto w-full">
				<thead class="bg-gray-200">
					<tr>
						<th>#</th>
						<th>Center</th>
						<th>Deposite Date</th>
						<!-- <th>HCI/AHCI</th> -->
						
						
						<th>Webfile</th>
						<th>Received From HCI/AHCI</th>
						<th>Date of Digitization</th>
						<th>HCI Processing (Days)</th>
						<th>ICON Processing(Days)</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $index => $row)
					<tr>
					<td class="text-center">{{ $data->firstItem() + $index }}</td>
					
				<!-- <td class="text-center">{{ $row->region?->region_name }} </td> -->
	 				<td class="text-center">{{ $row->webReference?->center?->center_name }}</td>
	 				<td class="text-center">{{ $row['sent2hci_date'] ?? '' }}</td>
	 				<td class="text-center">{{ $row['webfile'] ?? '' }}</td>
	 				<td class="text-left">{{ $row['return_date'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['dvd_date'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['HCIProcessing'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['ICONProcessing'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['remarks'] ?? '' }}</td> 
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr class="font-bold">
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center">Average</td>
						<td class="text-center">{{ $averages->AvgHCI ?? 0 }}</td>
						<td class="text-center">{{ $averages->AvgICON ?? 0 }}</td>
						<td class="text-center"></td>
					</tr>
				</tfoot>
			</table>

			{{-- Pagination --}}
			<div class="mt-4">
				{{ $data->links() }}
			</div>
		</div>
	</div>



</div>
@endsection


@push('scripts')
    @vite(['resources/js/scripts/digitization.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Motor Status Report" />
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

<div class="appReceive">
    <form method="GET" action="{{ route('motor-status.index') }}" class="mb-2 space-y-4 mx-[150px]">
    	 @csrf
      
        <div class="flex gap-2">
            <div class="flex-1 flex items-center gap-2">
                <label class="w-20">From:</label>
                <input id="fromDate" type="date" name="from_date" value="{{ request('from_date') }}" class="flex-1 border p-2 rounded">
            </div>

            <div class="flex-1 flex items-center gap-2">
                <label class="w-20">To:</label>
                <input id="toDate" type="date" name="to_date" value="{{ request('to_date') }}" class="flex-1 border p-2 rounded">
            </div>
          <div class="flex-1 flex items-center gap-2">
                <label class="w-20">Device:</label>
                <select name="device" id="device" class="flex-1 border p-2 rounded">
                    <option value="">ALL</option>
                    @foreach($dev as $item)
                        <option value="{{ $item->devID }}">{{ $item->devID }}</option>
                    @endforeach
                </select>
            </div>
      
           
        </div>

 
<div class="flex justify-between items-center w-full">
 
    <button type="submit" 
       class="bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center">
        Search
    </button>
</div>

    </form>
</div>



		<hr class="my-2">
		
		{{-- Export Buttons --}}
		<div class=" flex gap-2">
			<a href="{{ route('motor-status.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
	 
			
			<!--  <button id="openPrint" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Print</button>-->	
			<!-- <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded">Print</button> -->
		</div>
		
	</div>
	
    {{-- DataTable --}}
    <div class="AppDataTable bg-white shadow rounded p-2 mt-1 mx-auto">
		<div class="overflow-x-auto">
			<table id="reportTable" class="table-auto w-full">
				<thead class="bg-gray-200">
					<tr>
						<th>#</th>
						<th class="text-center">Date</th>
						<th class="text-center">Device</th>
						
						<th class="text-center">Start</th>
						<th class="text-center">Stop</th>
						<th class="text-center">Run</th>
						<th class="text-center">rms</th>
						<th class="text-center">operator</th>
						<th class="text-center">created_at</th>
			 		</tr>
				</thead>
				<tbody>
					@foreach($data as $index => $row)
					<tr>
						<td class="text-center">{{ $data->firstItem() + $index }}</td>
						<td class="text-center">{{ $row->Date ?? '' }}</td>
						<td class="text-center">{{ $row->devId ?? '' }}</td>
						<td class="text-center">{{ mb_substr($row->mt_start, 0, 16)}}</td>
						<td class="text-center">{{ mb_substr($row->mt_stop, 0, 16)}}</td>
						<td class="text-left">{{ $row->mt_run ?? '' }}</td>
						<td class="text-left">{{ $row->ac_rms ?? '' }}</td>
						<td class="text-left">{{ $row->employee->emp_name ?? '' }}</td>
					 	<td class="text-center">{{ mb_substr($row->created_at, 0, 16)}}</td>
						 
					</tr>
					@endforeach
				</tbody>
				<tr>
				    <td colspan="100%" style="height: 15px;"></td>
				</tr>
 
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
<script type="module">
import 'datatables.net-dt/css/jquery.dataTables.css';
import $ from 'jquery';
import 'datatables.net';

$(document).ready(function() {
    $('#reportTable').DataTable({
        paging: false, // Laravel pagination is used
        searching: true,
        ordering: true
    });
});
</script>
@endpush

@push('scripts')
    @vite(['resources/js/scripts/motorReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

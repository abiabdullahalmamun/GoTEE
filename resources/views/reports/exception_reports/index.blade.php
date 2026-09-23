@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Exception Report" />
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
    <form method="GET" action="{{ route('exception-Report.index') }}" class="mb-2 space-y-4 mx-[150px]">
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
                    <label class="w-20">Center:</label>
                    <select  name="center" id="center" class="flex-1 border p-2 rounded">
                      @if($role == 1 || $role == 5 || $role == 13)
					        <option value="">ALL</option>
					        @foreach($centers as $cnt)
					            <option value="{{ $cnt->id }}" {{ $cenId == $cnt->id ? 'selected' : '' }}>
					                {{ $cnt['center_name'] }}
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

        {{-- Row 2 --}}
        <div class="flex gap-2">
            <div class="flex-1 flex items-center gap-2">
                <label class="w-20">Module:</label>
                <select name="module" class="flex-1 border p-2 rounded">
                    <option value="">ALL</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod->module }}">{{ $mod->module }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 flex items-center gap-2">
                <label class="w-20">User:</label>
                <select name="user" id="user" class="flex-1 border p-2 rounded">
                    <option value="">ALL</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
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
			<a href="{{ route('exception-Report.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
		<!-- 	<a href="{{ route('Receive-Report.pdf', request()->all()) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a> -->
			
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
						<th class="text-center">Center</th>
						<th class="text-center">Date</th>
						<th class="text-center">Interface</th>
						<th class="text-center">Action</th>
						<th class="text-center">Details</th>
						<th class="text-center">User</th>
						<th class="text-center">CreatedAt</th>
						<th class="text-center">IP</th>
			 		</tr>
				</thead>
				<tbody>
					@foreach($data as $index => $row)
					<tr>
						<td class="text-center">{{ $data->firstItem() + $index }}</td>
						<td class="text-center">{{ $row->center->center_name ?? '' }}</td>
						<td class="text-center">{{ $row->Date ?? '' }}</td>
						<td class="text-left">{{ $row->module ?? '' }}</td>
						<td class="text-left">{{ $row->action ?? '' }}</td>
						<td class="text-left">{{ $row->remarks ?? '' }}</td>
						<td class="text-center">{{ $row->user->name ?? ''}}</td>
						<td class="text-center">{{ mb_substr($row->created_at, 0, 16)}}</td>
						<td class="text-center">{{ $row->ip_address ?? '' }}</td>
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
    @vite(['resources/js/scripts/exceptReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

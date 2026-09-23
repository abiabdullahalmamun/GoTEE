@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Appointment Override Report" />
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

  
	<div class="bg-white shadow rounded p-2 mt-1 mx-auto">
		<div class="appReceive">
			<form method="GET" action="{{ route('override-Report.index') }}" class="mb-2 flex gap-2">
				From:
				<input type="date" name="from_date" value="{{request('from_date')}}" class="border p-2 rounded">
				To:
				<input type="date" name="to_date" value="{{request('to_date')}}" class="border p-2 rounded">

				@if($role == 1 || $role == 5) 
				Center:
				<select name="center" class="border p-2 rounded">
					<option value="">ALL</option>
					@foreach($centers as $cnt)
					<option value="{{ $cnt->id }}" > {{ $cnt['center_name'] }} </option>
					@endforeach
				</select>
				@endif
				User:
				<select name="user" class="border p-2 rounded">
					<option value="">ALL</option>
					@foreach($users as $user)
					<option value="{{ $user->id }}" > {{ $user->name }}  </option>
					@endforeach
				</select>
				Type:
				<select name="type" class="border p-2 rounded">
					<option value="">ALL</option>
					<option value="0">PENDING</option>
					<option value="1">APPROVED</option>
				</select>
				<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
			</form>
		</div>
		<hr class="my-2">
		
		{{-- Export Buttons --}}
		<div class=" flex gap-2">
			<a href="{{ route('override-Report.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
		<!-- 	<a href="{{ route('override-Report.pdf', request()->all()) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a> -->
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
						<th class="text-center">#</th>
						<th class="text-center">Center</th>
						<th class="text-center">Date</th>
						<th class="text-center">Webfile</th>
						<th class="text-center">VisaType</th>
						<th class="text-center">Remarks</th>
						<th class="text-center">CreatedBy</th>  
						<th class="text-center">CreatedAt</th>  
						<th class="text-center">Type</th>      
						<th class="text-center">ApprovedBy</th>  
						<th class="text-center">ApprovedAt</th>    
					</tr>
				</thead>
				<tbody>
					@php
					    $svcMap = [
					        1 => 'Foreign Passport',
					        2 => 'WAIVE',
					        3 => 'Others',
					    ];
					@endphp
					@foreach($data as $index => $row)
					<tr>
					<td class="text-center">{{ $data->firstItem() + $index }}</td>
					<td class="text-center">{{ $row->center->center_name ?? '' }}</td>
					<td class="text-center">{{ $row->Date ?? '' }}</td>
					<td class="text-center">{{ $row->WebFile_no ?? '' }}</td>
					<td class="text-center">{{ $row->visa->visa_type ?? '' }}</td>
					<td class="text-center">{{ $row->remarks ?? '' }}</td>
					<td class="text-center">{{ $row->user->name ?? ''  }}</td>
					<td class="text-center">{{ mb_substr($row->created_at, 0, 19)}}</td>
					<td class="text-center">  {{ $svcMap[$row->svcId] ?? '' }}</td>
					<td class="text-center">{{ $row->userApp->name ?? '' }}</td>
					<td class="text-center">{{ mb_substr($row->approvedAt, 0, 19)}}</td>
					</tr>
					@endforeach
				</tbody>
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
    @vite(['resources/js/scripts/overrideReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Application Return Report" />
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
			<form method="GET" action="{{ route('rejectReport.index') }}" class="mb-2 flex gap-2">
				From:
				<input type="date" name="from_date" value="{{request('from_date')}}" class="border p-2 rounded">
				To:
				<input type="date" name="to_date" value="{{request('to_date')}}" class="border p-2 rounded">

<div class="flex-1 flex items-center gap-2">
    <label class="w-20">Center:</label>
    <select name="center" id="center" class="flex-1 border p-2 rounded">


@if($role == 1 || $role == 5 || $role == 13)
    <option value="">ALL</option>
    @foreach($centers as $cnt)
        <option value="{{ $cnt->id }}" {{ $cenId == $cnt->id ? 'selected' : '' }}>
            {{ $cnt->center_name }}
        </option>
    @endforeach
@else
    @php
        $center = \App\Models\Center::find($cenId);
        $centerName = $center ? $center->center_name : 'N/A';
    @endphp
    <option value="{{ $cenId }}" selected>{{ $centerName }}</option>
@endif


    </select>
</div>

				User:
				<select name="user" class="border p-2 rounded">
					<option value="">ALL</option>
					@foreach($users as $user)
					<option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }} > {{ $user->name }}  </option>
					@endforeach
				</select>

				<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
			</form>
		</div>
		<hr class="my-2">
		
		{{-- Export Buttons --}}
		<div class=" flex gap-2">
			<a href="{{ route('rejectReport.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
			<!-- <a href="{{ route('readyCenter-report.pdf', request()->all()) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a> -->
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
						<th>Center</th>
						<th>Date</th>
						<th>Webfile</th>
						
						<th>Name</th>
						<th>Passport</th>
						<th>Contact</th>
						
						<th>VisaType</th>
						<th>Reasons</th> 
						<th>CreatedBy</th>  
						<th>CreatedAt</th>    
					</tr>
				</thead>
				<tbody>
					@foreach($data as $index => $row)
					<tr>
					<td class="text-center">{{ $data->firstItem() + $index }}</td>
					<td class="text-center">{{ $row->center->center_name ?? '' }}</td>
					<td class="text-center">{{ $row->Date ?? '' }}</td>
					
					<td class="text-left">{{ $row->Webfile ?? '' }}</td>
					<td class="text-center">{{ $row->ApplicantName ?? '' }}</td>
					<td class="text-center">{{ $row->passport ?? '' }}</td>
					<td class="text-center">{{ $row->contact  ?? '' }}</td>
					<td class="text-center">{{ $row->visa->visa_type  ?? '' }}</td>
				 	<td class="text-center">
					    {{ $row->rejectReasons->pluck('reason_name')->implode(', ') }}
					</td>
					<td>{{ $row->user->name ?? '' }}</td>
					<td class="text-center">{{ mb_substr($row->created_at, 0, 19)}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>

			{{-- Pagination --}}
			@if(count($data)>0)
			<div class="mt-4">
				{{ $data->links() }}
			</div>
			@endif
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
    @vite(['resources/js/scripts/rejectReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

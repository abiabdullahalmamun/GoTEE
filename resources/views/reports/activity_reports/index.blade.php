@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Activity Summary Report" />
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
    <form method="GET" action="{{ route('daily-activity.index') }}" class="mb-2 space-y-4 mx-[150px]">

        {{-- Row 1 --}}
        <div class="flex gap-2">
            <div class="flex-1 flex items-center gap-2">
                <label class="w-20">From:</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="flex-1 border p-2 rounded">
            </div>

            <div class="flex-1 flex items-center gap-2">
                <label class="w-20">To:</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="flex-1 border p-2 rounded">
            </div>

        </div>

        {{-- Row 2 --}}
        <div class="flex gap-2">
          
                <div class="flex-1 flex items-center gap-2">
                    <label class="w-20">Center:</label>
                    <select name="center" id="center" class="flex-1 border p-2 rounded">
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

        {{-- Row 3 (Button aligned right) --}}
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Search
            </button>
        </div>
    </form>
</div>


		<hr class="my-2">
		
		{{-- Export Buttons --}}
		<div class=" flex gap-2">
			<a href="{{ route('daily-activity.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
			<a href="{{ route('daily-activity.pdf', request()->all()) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a>
			
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
						<th class="p-2 border text-center">Center</th>
						<th class="p-2 border text-center">UserID</th>
						<th class="p-2 border text-center">User Name</th>
						<th class="p-2 border text-center">Total Visa Form Fill-Up</th>
						<th class="p-2 border text-center">Total Application Received</th>
						<th class="p-2 border text-center">Total Ready At Center</th>
						<th class="p-2 border text-center">Total Passport Delivered</th>
						<th class="p-2 border text-center">Total Biometric Captured</th>
						<th class="p-2 border text-center">Total</th>
					</tr>
				</thead>
				<tbody>
				@php
				  if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
				      $offset = ($data->currentPage() - 1) * $data->perPage();
				  } else {
				      $offset = 0;
				  }
				@endphp

				@foreach($data as $index => $row)
				<tr>
				  <td class="p-2 border text-center">{{ $offset + $index + 1 }}</td>
				  <td class="p-2 border text-center">{{ $row->user->center->center_name ?? '' }}</td>
				  <td class="p-2 border text-center">{{ $row->user->name ?? '' }}</td>
				  <td class="p-2 border text-center">{{ $row->user->FullName ?? '' }}</td>
				   <td class="p-2 border text-center">{{ $row->total_forms ?? '' }}</td>
				  <td class="p-2 border text-center">{{ $row->total_rec ?? '' }}</td>
				  <td class="p-2 border text-center">{{ $row->total_ready ?? '' }}</td>
				  <td class="p-2 border text-center">{{ $row->total_del ?? '' }}</td>
				  <td class="p-2 border text-center">{{ $row->total_bio ?? '' }}</td>
				  <td class="p-2 border text-center">{{ $row->total_all ?? '' }}</td>
				</tr>
				@endforeach
				</tbody>
				 <tfoot class="bg-gray-100 font-semibold">
				    <tr>
				      <td colspan="4" class="p-2 border text-right">Grand Total</td>
				      <td class="p-2 border text-center">{{ $net_forms }}</td>
				      <td class="p-2 border text-center">{{ $net_rec }}</td>
				      <td class="p-2 border text-center">{{ $net_ready }}</td>
				      <td class="p-2 border text-center">{{ $net_del }}</td>
				      <td class="p-2 border text-center">{{ $net_bio }}</td>
				      <td class="p-2 border text-center"></td>
				    </tr>
				  </tfoot>
			</table>

			{{-- Pagination --}}
			<div class="mt-4">
				
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
    @vite(['resources/js/scripts/actReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

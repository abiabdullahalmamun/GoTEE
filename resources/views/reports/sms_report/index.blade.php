@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="SMS Report" />
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
			<form method="GET" action="{{ route('sms-report.index') }}" class="mb-2 flex gap-2">
				From:
				<input type="date" name="from_date" value="{{request('from_date')}}" class="border p-2 rounded">
				To:
				<input type="date" name="to_date" value="{{request('to_date')}}" class="border p-2 rounded">

				                <div class="flex-1 flex items-center gap-2">
                    <label class="w-20">Center:</label>
                    <select name="center" id="center" class="flex-1 border p-2 rounded">
                      @if($role == 1 || $role == 5 ||  $role== 13)
					        <option value="">ALL</option>
					        @foreach($centers as $cnt)
					            <option value="{{ $cnt->id }}" {{ $cenId == $cnt->id ? 'selected' : '' }}>
					                {{ $cnt['center_name'] }}
					            </option>
					        @endforeach
					    @else
					        @php
					            $userCenter = $centers->firstWhere('id', $cenId);
					        @endphp
					        <option value="{{ $cenId }}" selected>
					            {{ $userCenter ? $userCenter->center_name : 'N/A' }}
					        </option>
					    @endif
                    </select>
                </div>

				SMS Type:
				<select name="type" class="border p-2 rounded">
					<option value="">ALL</option>
					<option value="0">OTP</option>
					<option value="1">Receive At Center</option>
					<option value="2">Sent to HCI</option>
					<option value="4">Ready At Center</option>
					<option value="41">Pending Alert</option>
				</select>

				<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
			</form>
		</div>
		<hr class="my-2">
		
		{{-- Export Buttons --}}
		<div class=" flex gap-2">
			<a href="{{ route('sms-report.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
			<a href="{{ route('sms-report.pdf', request()->all()) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a>
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
						<th>Type</th>

						<th>Webfile</th>
						<th>Contact</th>
						<th>Text</th>  
						<th>CreatedAt</th>
						<th>CreatedBy</th>        
						<th>TXN</th>  
						<th>Status</th>   
						<th>DelivertAt</th>    
					</tr>
				</thead>
				<tbody>
					@foreach($data as $index => $row)
					<tr>
					<td class="text-center">{{ $data->firstItem() + $index }}</td>
					<td class="text-center">{{ $row->center->center_name ?? '' }}</td>
					<td class="text-center">{{ $row->Date ?? '' }}</td>
					@php
					$type = $row->type ?? -1;

					$badges = [
					    0 => ['label' => 'OTP', 'class' => 'badge-success text-red-700'],
					    1 => ['label' => 'Receive', 'class' => 'badge-danger text-black-700'],
					    2 => ['label' => 'Sent2HCI', 'class' => 'badge-danger text-blue-700'],
					    3 => ['label' => 'RecFrmHCI', 'class' => 'badge-danger text-green-700'],
					    4 => ['label' => 'ReadyCenter', 'class' => 'badge-danger text-green-700'],
					    41 => ['label' => 'PendingAlert', 'class' => 'badge-danger text-red-700'],
					    5 => ['label' => 'Delivery', 'class' => 'badge-danger text-blue-700'],
					];

					$badge = $badges[$type] ?? ['label' => 'Unknown', 'class' => 'badge-secondary'];
					@endphp

					<td class="text-center">
					    <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
					</td>
					
					<td class="text-center">{{ $row->web->Webfile ?? '' }}</td>
					<td class="text-center">{{ $row->contact ?? '' }}</td>
					<td class="text-left">{{ $row->text ?? '' }}</td>

					<td class="text-center">{{ mb_substr($row->created_at, 0, 19)}}</td>
					<td class="text-center">{{ $row->user->name ?? '' }}</td>				
					<td class="text-center">{{ $row->txn ?? '' }}</td>
					<td class="text-center">{{ $row->Delivery ?? '' }}</td>
					<td class="text-center">{{ mb_substr($row->Delivery_time, 0, 19)}}</td>
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
    <!-- @vite(['resources/js/scripts/region.js']) -->
    @vite(['resources/js/scripts/smsReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

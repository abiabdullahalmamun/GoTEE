@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Undelivered Passport Report" />
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
		<div class="appUndelivery">
			<form method="GET" action="{{ url('/undelivered-pass') }}" class="mb-2 flex gap-2">
			    @csrf
		        <!-- @method('POST') -->
	        <div class="flex gap-2">
	             <div class="flex-1 flex items-center gap-2">
	                <label class="w-20">From:</label>
	                <input id="fromDate" type="date" name="from_date" value="{{ request('from_date') }}" class="flex-1 border p-2 rounded">
	            </div>

	            <div class="flex-1 flex items-center gap-2">
	                <label class="w-20">To:</label>
	                <input id="toDate" type="date" name="to_date" value="{{ request('to_date') }}" class="flex-1 border p-2 rounded">
	            </div>
	          </div>
                <!-- <div class="flex-1 flex items-center gap-2"> -->
                    <label class="w-20">Center:</label>
                    <select name="center" id="center" class="w-40 border p-2 rounded">

                    <!-- <select name="center" id="center" class="flex-1 border p-2 rounded"> -->
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
                <!-- </div> -->
                 	@if($role == 1)
	                <label class="w-20">Pending Days:</label>
					<select name="days" id="days" class="w-40 border p-2 rounded">
						<option value="0">ALL</option>
						<option value="20">30</option>
						<option value="60">60</option>
						<option value="90">90</option>
						<option value="120">120</option>
						<option value="150">150</option>
						<option value="180">180</option>
					</select>
					 @endif
				<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
			
			
			</form>
		</div>
		<hr class="my-2">
		
		{{-- Export Buttons --}}
	<div class="flex gap-2 items-center">
    <a href="{{ route('undelivered-pass.excel', request()->all()) }}" 
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
					@if($type==1)
					<tr>
						<th>#</th>
						<th>Date</th>
						<th>Center</th>
						<th>Passport</th>
						<th>Webfile</th>
						<th>Name</th>
						<th>Contact</th>
						<th>stcType</th>
						<th>StickerNo</th>
						<th>Visatype</th>
						<th>Source</th>
					</tr>
					@else
					<tr>
						<th>#</th>
						<th>ReceiveDate</th>
						<th>Center</th>
						<th>Passport</th>
						<th>Webfile</th>
						<th>Name</th>
						<th>Contact</th>
						<th>ReadyCenterDate</th>
						<th>PendingDays</th>
					</tr>
					@endif
				</thead>
				<tbody>
					@if($type==1)
					@foreach($data as $index => $row)
					<tr>
					<td class="text-center">{{ $data->firstItem() + $index }}</td>
					<td class="text-center">{{ $row['date'] ?? '' }}</td>
					<td class="text-center">{{ $row['center'] ?? '' }}</td>
	 				<td class="text-center">{{ $row['passport'] ?? '' }}</td>
	 				<td class="text-center">{{ $row['Webfile'] ?? '' }}</td>
	 				
	 				<td class="text-left">{{ $row['Name'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['Contact'] ?? '' }}</td>
				 	<td class="text-center">{{ $row['sticker']  ?? '' }}</td>
				 	<td class="text-center">{{ $row['stickerNo']  ?? '' }}</td>
					<td class="text-center">{{ $row['visa']  ?? '' }}</td>
					<td class="text-left">{{ $row['source'] ?? '' }}</td>
					</tr>
					@endforeach
					@else
					@foreach($data as $index => $row)
					<tr>
					<td class="text-center">{{ $data->firstItem() + $index }}</td>
					<td class="text-center">{{ $row->Date  ?? '' }}</td>
					<td class="text-center">{{ $row->centerId  ?? '' }}</td>
					<td class="text-center">{{ $row->passport  ?? '' }}</td>
					<td class="text-center">{{ $row->Webfile  ?? '' }}</td>
					<td class="text-center">{{ $row->ApplicantName  ?? '' }}</td>
					<td class="text-center">{{ $row->contact  ?? '' }}</td>
					<td class="text-center">{{ $row->last_date  ?? '' }}</td>
					<td class="text-center">{{ $row->days_diff  ?? '' }}</td>	
					</tr>
					@endforeach

					@endif
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
    @vite(['resources/js/scripts/undelReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

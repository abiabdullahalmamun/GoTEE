@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Token Report" />
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
    <form method="GET" action="{{ route('token-Report.index') }}" class="mb-2 space-y-4 mx-[150px]">
    	 @csrf

<div class="flex w-full justify-between text-sm">
    <div class="flex items-center whitespace-nowrap">
        <span class="text-gray-600">App Server:</span>
        <b class="ml-2">{{$apptime}}</b>
    </div>

    <div class="flex items-center whitespace-nowrap">
        <span class="text-gray-600">DB Server:</span>
        <b class="ml-2">{{$dbtime}}</b>
    </div>
</div>

<!-- 		<div class="flex gap-2">
		    <div class="flex-1 flex items-center gap-2">
		         <label class="whitespace-nowrap flex items-center gap-8">
				    <span>App Server Time: <b>{{$apptime}}</b></span>
				  
				    <span>DB Server Time: <b>{{$dbtime}}</b></span>
				</label>
		    </div>
		</div> -->
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
                <label class="w-20">Service:</label>
                <select name="service" class="flex-1 border p-2 rounded">
                    <option value="">ALL</option>
                    @foreach($svcType as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->service_name }}</option>
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
<!-- 	<div class="flex space-x-2">
	    <button type="button" 
	        onclick="submitFormTo('{{ route('Receive-Report.summary') }}', 'visa')" 
	        class="bg-purple-500 text-white px-2 py-1 rounded text-sm">
	        VisaType Summary
	    </button>

	    <button type="button" 
	        onclick="submitFormTo('{{ route('Receive-Report.summary') }}', 'sticker')" 
	        class="bg-gray-500 text-white px-2 py-1 rounded text-sm">
	        Sticker Summary
	    </button>
	</div>
 -->

    <button type="submit" 
       class="bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center">
        Search
    </button>
</div>

    </form>
</div>



		<!-- <hr class="my-2"> -->
		
		{{-- Export Buttons --}}
		<div class=" flex gap-2">
			<a href="{{ route('token-Report.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
		<!-- 	<a href="{{ route('Receive-Report.pdf', request()->all()) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm">PDF</a> -->
			
			  <!-- <button id="openPrint" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Print</button>	 -->
			<!-- <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded">Print</button> -->
		</div>
		
	</div>
	
    {{-- DataTable --}}
    <div class="AppDataTable bg-white shadow rounded p-2 mt-1 mx-auto">
		<div class="overflow-x-auto">
			<table id="reportTable" class="table-auto w-full">
				<thead class="bg-gray-200">
					<tr>
						<th class="text-center" >#</th>
						<th class="text-center">Center</th>
						<th class="text-center">Date</th>
						<th class="text-center">Token</th>
						
						<th class="text-center">Service</th>
						<th class="text-center">TokenIssueTime</th>
						<th class="text-center">CounterCallTime</th>
						<th class="text-center">Wait1 Detail</th>

						<th class="text-center">Svc Start</th>
						<th class="text-center">Wait2 Detail</th>

						<th class="text-center">Webfile </th>
						<th class="text-center"> Detail</th>
						<th class="text-center">Difference</th>

						<th class="text-center">ServiceStopTime</th>
						<th class="text-center">Wait</th>
						<th class="text-center">Add. Wait</th>
						<th class="text-center">Service</th>
						
						<th class="text-center">A-Service</th>
						<th class="text-center">Counter</th>
						<th class="text-center">Executive</th>
						 
					</tr>
				</thead>
 
 <tbody>
@foreach($data as $index => $row)
@php
    $webs = collect($row->web_svc_ranges);
    $webCount = max($webs->count(), 1);
@endphp
<!-- <tr> -->
<tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100">
    <td    class="text-center">{{$data->firstItem() + $index }}</td>
    <td   class="text-center">{{$row->center->center_name ??''}}</td>
    <td   class="text-center"> {{ $row->Date ?? '' }}  </td>
    <td   class="text-center">  {{ $row->tokenno ?? '' }}  </td>

    <td  class="text-center">{{ $row->serviceName->service_name ?? 'N/A' }} </td>
	<td  class="text-center">{{ mb_substr($row->tissuetime, 0, 19) ?? '' }}</td>
	<td  class="text-center">{{ mb_substr($row->ststart, 0, 19) ?? '' }}</td>

	<td   class="text-center">   {{ $row->wait1_range ?? ''}}    </td>

	<td  class="text-center">{{ mb_substr($row->scantime, 0, 19) ?? '' }}</td>
 	<td  class="text-center">{{ $row->wait2_range ?? ''}} </td>
	

    @if($webs->count() > 0)
        <td class="text-center">
            {{ $webs[0]['webfile'] }}
        </td>
        <td class="text-center">
            {{ $webs[0]['range'] }}
        </td>
        <td class="text-center">
            {{ $webs[0]['difference']
                ? gmdate('H:i:s', $webs[0]['difference'])
                : '' }}
        </td>
     @else
     <td></td>
     <td></td>
     <td></td>
     @endif 






	<td  class="text-center">{{ mb_substr($row->ststop, 0, 19) ?? '' }}</td>
	<td class="text-center"> {{ $row->waiting !== null ? gmdate('H:i:s', $row->waiting) : '' }}</td>
	<td class="text-center"> {{ $row->total_wait !== null ? gmdate('H:i:s', $row->total_wait) : '' }}</td>
	<td  class="text-center"> {{ $row->service !== null ? gmdate('H:i:s', $row->service) : '' }}</td>
	
	<td  class="text-center"> {{ $row->scan !== null ? gmdate('H:i:s', $row->scan) : '' }}</td>
	<td  class="text-center">{{ $row->cno ?? '' }}</td>
	<td  class="text-center">{{ $row->user->name ?? '' }}</td>

   
  


  </tr>
 
 @foreach($webs->slice(1) as $svc)
<tr>
	<td class="text-center">
    <span class="hidden">
        {{ $data->firstItem() + $index }}
    </span>
</td>
	
	<td></td>
	<td></td>
	<td></td>

	<td></td>
	<td></td>
	<td></td>
	<td></td>

	<td></td>
	<td></td>

	<td class="text-center">{{ $svc['webfile'] }}</td>
    <td class="text-center">{{ $svc['range'] }}</td>
    <td class="text-center">
        {{ $svc['difference']
            ? gmdate('H:i:s', $svc['difference'])
            : '' }}
    </td>

    <td></td>
	<td></td>

	<td></td>
	<td></td>
	<td></td>
	<td></td>

	<td></td>

    
</tr>
@endforeach
 

@endforeach
</tbody>
 

				<tfoot>
					<tr class="font-bold">
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						
							 	<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
<td class="text-center"></td>
						<td class="px-2 py-1 text-left font-medium">Max</td>
						<td class="px-2 py-1 text-center">{{ $stats->max_waiting !== null ? gmdate('H:i:s', $stats->max_waiting) : '-' }}</td>
						<td class="text-center"></td>
						<td class="px-2 py-1 text-center">{{ $stats->max_service !== null ? gmdate('H:i:s', $stats->max_service) : '-' }}</td>
						
						<td class="px-2 py-1 text-center">{{ $stats->max_scan !== null ? gmdate('H:i:s', $stats->max_scan) : '-' }}</td>
						
						<td class="text-center"></td>
						<td class="text-center"></td>
					 	
				

					</tr>
					<tr class="font-bold">
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						
							 <td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
	<td class="text-center"></td>
						 <td class="px-2 py-1 text-left font-medium">Min</td>
						 <td class="px-2 py-1 text-center">{{ $stats->min_waiting !== null ? gmdate('H:i:s', $stats->min_waiting) : '-' }}</td>
						 <td class="text-center"></td>
						 <td class="px-2 py-1 text-center">{{ $stats->min_service !== null ? gmdate('H:i:s', $stats->min_service) : '-' }}</td>
						 
						 <td class="px-2 py-1 text-center">{{ $stats->min_scan !== null ? gmdate('H:i:s', $stats->min_scan) : '-' }}</td>
						 <td class="text-center"></td>
						<td class="text-center"></td>
					
						 
					
						  
					</tr>
					<tr class="font-bold">
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
	<td class="text-center"></td>
						<td class="px-2 py-1 text-left font-medium">Avg</td>
					 	<td class="px-2 py-1 text-center">{{ $stats->avg_waiting !== null ? gmdate('H:i:s', (int)$stats->avg_waiting) : '-' }}</td>
					 	<td class="text-center"></td>
						<td class="px-2 py-1 text-center">{{ $stats->avg_service !== null ? gmdate('H:i:s', (int)$stats->avg_service) : '-' }}</td>
						
						<td class="px-2 py-1 text-center">{{ $stats->avg_scan !== null ? gmdate('H:i:s', (int)$stats->avg_scan) : '-' }}</td>
						<td class="text-center"></td>
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
    @vite(['resources/js/scripts/tokenReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Foreign Passport Received Report" />
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

<div class="fpPassReceive">
    <form method="GET" action="{{ route('frpReceiveReport.index') }}" class="mb-2 space-y-4 mx-[150px]">
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

           
        </div>

        {{-- Row 2 --}}
        <div class="flex gap-2">
 
          
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
<div class="flex flex-wrap gap-3">
	<!-- <input type="hidden" name="summary_type" value=""> -->
	<button type="button"
        onclick="submitFormTo('{{ route('frpReceiveReport.summary') }}', 'visafax')"
        class="bg-purple-500 text-white px-4 py-2 rounded text-sm whitespace-nowrap">
        Visa & Telex/Fax App.
    </button>

    <button type="button"
        onclick="submitFormTo('{{ route('frpReceiveReport.summary') }}', 'icwf')"
        class="bg-purple-500 text-white px-4 py-2 rounded text-sm whitespace-nowrap">
        ICWF App.
    </button>

    <button type="button"
        onclick="submitFormTo('{{ route('frpReceiveReport.summary') }}', 'sendingstatus')"
        class="bg-purple-500 text-white px-4 py-2 rounded text-sm whitespace-nowrap">
        Sending Status Print.
    </button>

    <button type="button"
        onclick="submitFormTo('{{ route('frpReceiveReport.summary') }}', 'statement')"
        class="bg-purple-500 text-white px-4 py-2 rounded text-sm whitespace-nowrap">
        Statement.
    </button>
</div>



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
			<a href="{{ route('frpReceiveReport.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
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
						<th>Sticker <br> Type</th>
						<th>StickerNo</th>
						<th>Contact</th>
						<th>Visatype</th>
						<th>Gratis</th>
						<th>BookNo<br>ReceiptNo</th>
						<th>Nationality</th>

						<th>Visa<br>fee</th>  
						<th>fax<br>txn</th>
						<th>ICWF</th>
						<th>Visa<br>App</th>  
						<th>Total<br>BDT</th>
						<th>Remarks</th>
						<th>Received<br>By</th>  
						<th>ReceivedAt</th>    
					</tr>
				</thead>
				<tbody>
					@foreach($data as $index => $row)
					<tr>
						<td>{{ $data->firstItem() + $index }}</td>
						<td>{{ $row->center->center_name ?? '' }}</td>
						<td>{{ $row->Date ?? '' }}</td>
						<td>{{ $row->webref->Webfile ?? '' }}</td>
						<td>{{ $row->webref->ApplicantName ?? '' }}</td>
						<td>{{ $row->webref->passport ?? '' }}</td>
						<td>{{ $row->webref->sticker->sticker ?? '' }}</td>
						<td>{{ $row->webref->stickerNo ?? '' }}</td>
						<td>{{ $row->webref->contact ?? '' }}</td>
						<td>{{ $row->webref->visa->visa_type ?? '' }}</td>
						<td> 
						  @if($row->gratis == 1)
						        YES
						    @else
						        NO
						    @endif
						</td>
		 				<td>{{ $row->BookNo ?? '' }} - {{ $row->ReceiptNo ?? '' }}</td>
						<td>{{ $row->nationality ?? '' }}</td>
		
						<td>{{ $row->visa_fee ?? '' }}</td>
						<td>{{ $row->fax_trans_charge ?? '' }}</td>
						<td>{{ $row->icwf ?? '' }}</td>
						<td>{{ $row->visa_app_charge ?? '' }}</td>
						<td>{{ $row->total_amount ?? '' }}</td>
						<td>{{ $row->v_duration?->name ?? '-' }}/{{ $row->entry?->name ?? '-' }}/{{ $row->webref->visa->visa_type ?? '-'}}</td>

						<td>{{ $row->user->name ?? ''}}</td>
						<td>{{ mb_substr($row->created_at, 0, 16)}}</td>
					</tr>
					@endforeach
				</tbody>
				<tr>
				    <td colspan="100%" style="height: 15px;"></td>
				</tr>
				<tfoot>
		 
			        <tr class="font-bold">
			        	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	
			            	<td></td>
			            	<td></td>
			            	<td></td>
			        	<td></td>
			            	<td></td>
			            	
			           
			            <td class="border px-2 py-1 text-right"> </td>
			            <td></td>
			               	<td></td>
			              	<td></td>
			            	
			            	<td></td>
			            	<td></td>
			            	 <td class="border px-2 py-1" colspan="2">Grand Total:</td>
			           	 <td class="border px-2 py-1 text-right">{{$sumTotalAmount}} </td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
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
    @vite(['resources/js/scripts/frpRecReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

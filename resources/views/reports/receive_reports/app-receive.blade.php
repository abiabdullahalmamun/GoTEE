@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
    <x-page-header title="Application Received Report" />
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
    <form method="GET" action="{{ route('Receive-Report.index') }}" class="mb-2 space-y-4 mx-[150px]">
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
                      @if($role == 1 || $role == 5 ||  $role== 13)
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
                <label class="w-20">Visa:</label>
                <select name="visa" class="flex-1 border p-2 rounded">
                    <option value="">ALL</option>
                    @foreach($VisaType as $visa)
                        <option value="{{ $visa->id }}">{{ $visa->visa_type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 flex items-center gap-2">
                <label class="w-20">Sticker:</label>
                <select name="sticker" class="flex-1 border p-2 rounded">
                    <option value="">ALL</option>
                    @foreach($stickerTypeList as $sticker)
                        <option value="{{ $sticker->id }}">{{ $sticker->sticker }}</option>
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
	<div class="flex space-x-2">
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
			<a href="{{ route('Receive-Report.excel', request()->all()) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm">Excel</a>
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
						<th>Pay</th>
						<th>Transaction ID</th>
						<th>PaymentInfo<br>/Remarks</th>
						<th>Correction<br>fee</th>  
						<th>Pass<br>Qty</th>
						<th>Counter<br>No</th>
						<th>Token<br> No</th>  
						<th>Received<br>By</th>  
						<th>Received<br> time Stamp</th>    
					</tr>
				</thead>
				<tbody>
					@foreach($data as $index => $row)
					<tr>
						<td>{{ $data->firstItem() + $index }}</td>
						<td>{{ $row->center->center_name ?? '' }}</td>
						<td>{{ $row->Date ?? '' }}</td>
						<td>{{ $row->Webfile ?? '' }}</td>
						<td>{{ $row->ApplicantName ?? '' }}</td>
						<td>{{ $row->passport ?? '' }}</td>
						<td>{{ $row->sticker->sticker ?? '' }}</td>
						<td>{{ $row->stickerNo ?? '' }}</td>
						<td>{{ $row->contact ?? '' }}</td>
						<td>{{ $row->visa->visa_type ?? '' }}</td>
						
						@php
						    $badges = [
						        1 => ['label' => 'Online', 'class' => 'badge-success text-red-700'],
						        2 => ['label' => 'Cash', 'class' => 'badge-danger text-green-700'],
						        3 => ['label' => 'Waive', 'class' => 'badge-danger text-blue-700'],
						    ];
						    $badge = $badges[$row->pmethod] ?? ['label' => 'Unknown', 'class' => 'badge-secondary'];
						@endphp
						
						<td> 
							 <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
						</td>
						<td>{{ $row->txn ?? '' }}</td>
						<td>{{ $row->remarks ?? '' }}</td>
						<td>{{ $row->corrFee ?? '' }}</td>
						<td>{{ $row->psQty ?? '' }}</td>
						<td>{{ $row->cntNo ?? '' }}</td>
						<td>{{ $row->tknNo ?? '' }}</td>
						<td>{{ $row->user->name ?? ''}}</td>
						<td>{{ mb_substr($row->created_at, 0, 16)}}</td>
					</tr>
					@endforeach
				</tbody>
				<tr>
				    <td colspan="100%" style="height: 15px;"></td>
				</tr>
				<tfoot>
					 @foreach($paymentSummary as $type => $count)
					    <tr>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>

			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td class="border px-2 py-1">
			                	@if($type == 1)
							        Total Online
							    @elseif($type == 2)
							        Total Cash
							    @elseif($type == 3)
							        Total Waive
							    @else
							        N/A
							    @endif
			                </td>
			                <td class="border px-2 py-1 text-right">{{ $count }}</td>
			                
			            	<td></td>
			            	<td></td>
			            	
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            	<td></td>
			            </tr>
			        @endforeach
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
			            	
			            <td class="border px-2 py-1">Grand Total</td>
			            <td class="border px-2 py-1 text-right">{{$paymentSummary->sum()}}</td>
			            <td></td>
			             <td class="border px-2 py-1">Total Correction</td>
			                <td class="border px-2 py-1 text-right">{{ $corrTotal }} BDT</td>
			              	<td></td>
			            	
			            	<td></td>
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
    @vite(['resources/js/scripts/recReport.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

@extends('layouts.app')
@push('style')
@endpush
@section('content')
<div class="mx-auto">
	<x-page-header title="Search Result" />
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
	<div class="flex items-center justify-start space-x-2 px-4 py-2">
    <form action="{{ url('/searchWebfile') }}" method="POST" class="flex items-center space-x-2">
        @csrf
        @method('POST')
        <input type="text" name="searchTx1" id="searchTx1"
            class="px-4 py-2 border border-gray-300 rounded w-52 !text-black bg-white"
            placeholder="Search...">
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Search
        </button>
    </form>
</div>
  @if(isset($formdata) && count($formdata) > 0)
<div class="bg-gray-100 shadow rounded p-3 mt-1 mx-auto">
	<div><b>Forms Fill-in</b></div>
		<div class="trkTable">
			<table class="w-full text-sm">
				@if($formdata)
				<tr>
					<th>SL</th>
					<th>Date</th>
					<th>Webfile</th>
					<th>Passport</th>

					<th>Name</th>
					<th>contact</th>
					<th>day_sl</th>
					<th>Fee</th>
					
					<th>Created By</th>
					<th>Created At</th>
					<th>Action</th>
				</tr>

				@foreach ($formdata as $key => $data)
				<tr>
					<td>{{ ++$key }}</td>
					<td>{{ $data->Date ?? '' }}</td>
					<td>{{ $data->webfile  ?? '' }}</td>
					<td>{{ $data->passport  ?? '' }}</td>

					<td>{{ $data->Name  ?? '' }}</td>
					<td>{{ $data->contact ?? '' }}</td>
					<td>{{ $data->day_sl ?? '' }}</td>
					<td>{{ $data->fee ?? '' }}</td>

					<td>{{ $data->user->name ?? '' }}</td>
					<td>{{ $data->created_at ?? '' }}</td>
					<td class="px-4 py-2 text-center">
						<div class="flex space-x-2">
						@if ($role!=10)
						<form action="{{ route('form-fill.edit', $data->id) }}" method="GET">
							@csrf
							@method('GET')
							<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded text-sm">
								<i class="fas fa-edit"></i> 
							</button>
						</form>
						<form action="{{ route('form-fill.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
							@csrf
							@method('DELETE')
							<button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded text-sm">
								<i class="fas fa-trash-alt"></i> 
							</button>
						</form>
						@endif
						</div>
					</td>
				</tr>
				
				<tr>
					<td colspan="6">
						@endforeach
						@else
						<p>No Tracking data found for this Webfile.</p>
						@endif
					</td>
				</tr>
			</table>
		
		</div>
</div>
@endif
	
@if($pgreply)
<div class="bg-red-500 shadow rounded p-3 mt-1 mx-auto">
    <div><b>PG API</b></div>
    <pre>{{ print_r($pgreply, true) }}</pre>
</div>
@endif
@if($reject)
<div class="bg-yellow-200 shadow rounded p-3 mt-1 mx-auto">
    <div><b>Rejected Info</b></div>

    <table class="w-full text-sm">
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
            <th></th>
        </tr>

        <tr>
            <td class="text-center">1</td>
            <td class="text-center">{{ $reject->center->center_name ?? '' }}</td>
            <td class="text-center">{{ $reject->Date ?? '' }}</td>
            <td>{{ $reject->Webfile }}</td>
            <td>{{ $reject->ApplicantName }}</td>
            <td>{{ $reject->passport }}</td>
            <td>{{ $reject->contact }}</td>
            <td>{{ $reject->visa->visa_type ?? '' }}</td>
            <td>
                {{ $reject->rejectReasons->pluck('reason_name')->implode(', ') }}
            </td>
            <td>{{ $reject->user->name ?? '' }}</td>
            <td class="text-center">
            	@if ($role!=10)
	            	<form action="{{ route('rejectReport.destroy', $reject->id) }}" method="POST"
						  onsubmit="return confirm('Are you sure?')">
						@csrf
						@method('DELETE')
						<button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded text-sm">
							<i class="fas fa-trash-alt"></i> Delete
						</button>
					</form>
				@endif
			</td>
        </tr>
    </table>
</div>
@endif

<div class="bg-white shadow rounded p-3 mt-1 mx-auto">
		<table class="w-full text-sm">
		 			 
			@if($query)
			<tr>
				@if ($role!=10)
				<td width="70px;" class="text-left"> <!-- Align content to the left -->
					<form action="{{ route('app-receive.edit', $query->id) }}" method="GET"
						   onsubmit="return confirm('Are you sure?')">
						@csrf
						@method('GET')
						<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded text-sm">
							<i class="fas fa-edit"></i> Edit
						</button>
					</form>
				 
				</td>
				@endif
				<td class="text-left">
					<?php if($query->remarks==='FOREIGN'){
						?>
						<form action="{{ route('frpReceive.print', $query->id) }}" method="GET" >
						@csrf
							<button type="submit" class="bg-green-500 hover:bg-red-700 text-white px-2 py-1 rounded text-sm">
							<i class="fas fa-print"></i> Print
							</button>
						</form>
					<?php
					}
					else{
						?>
						<form action="{{ route('app-receive.print', $query->id) }}" method="GET" >
						@csrf
							<button type="submit" class="bg-green-500 hover:bg-red-700 text-white px-2 py-1 rounded text-sm">
							<i class="fas fa-print"></i> Print
							</button>
						</form>
						<?php
					} ?>
				
				</td>

				<td class="text-right"> <!-- Align content to the right -->
					@if ($role!=10)
						@if (in_array($query->stepId, [1, 2]))
						<form action="{{ route('app-receive.destroy', $query->id) }}" method="POST"
							  onsubmit="return confirm('Are you sure?')">
							@csrf
							@method('DELETE')
							<button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded text-sm">
								<i class="fas fa-trash-alt"></i> Delete
							</button>
						</form>
						@endif
					@endif
				</td>
			</tr>
		</table>
		
		<!-- <hr class="my-1"> -->
	 


		<div><b>Info</b></div>
		
		<div class="infoTable">
			<table class="w-full text-sm">
				<tr>
					<td>Record ID:</td>
					<td>{{$query->id }}</td>
					
					<td>Sticker Type: </td>
					<td>{{ $query->sticker->sticker ?? 'N/A' }}</td>
						
						@php
						$bioTypes = [1 => '<12', 2 => '12-70', 3 => '70+',4 => 'Updated'];
						@endphp
					<td>Biometric Type:</td>
					<td>{{ $bioTypes[$query->bioType] ?? 'Unknown' }}</td>
				</tr>
				
				<tr>
					<td>Mission Name:</td>
					<td>{{ $query->region->region_name ?? 'N/A' }}</td>
					
					<td>Sticker Number: </td>
					<td>  {{ $query->stickerNo }}</td>
					
					<td>Correction Fee:</td>
					<td>{{ $query->corrFee }}</td>
				</tr>
				
				<tr>
					<td>Center Name:</td>
					<td>{{ $query->center->center_name ?? 'N/A' }}</td>
					
					<td>Visa Type:</td>
					<td>{{ $query->visa->visa_type ?? 'N/A'}}</td>
					
					@php
					$stps = [1 => 'Received At Center', 2 => 'Sent to HCI', 3 => 'Received From HCI', 4 => 'Ready At Center', 5 => 'Delivered'];
					@endphp
					<td>Tracking Step:</td>
					<td> {{ $stps[$query->stepId] ?? 'Unknown' }}
					</td>
				</tr>
				
				<tr>
					<td>Webfile</td>
					<td>{{ $query->Webfile }}</td>
					
						@php
						$pmths = [1 => 'Online', 2 => 'Cash', 3 => 'Waive'];
						@endphp
					<td>Payment Method:</td>
					<td>{{ $pmths[$query->pmethod] ?? 'Unknown' }}</td>
					
					<td>Counter Number:</td>
					<td>{{ $query->cntNo ?? 'N/A'  }}</td>
				</tr>
				
				<tr>
					<td>Applicant Name:</td>
					<td>{{ $query->ApplicantName }}</td>
					
					<td>Transaction ID:</td>
					<td>{{ $query->txn }}</td>
					
					<td>Received By:</td>
					<td>{{ $query->user->name ?? 'N/A'  }}</td>
				</tr>
				
				<tr>
					<td>Passport No.</td>
					<td>{{ $query->passport }}</td>
					
					<td>Remarks:</td>
					<td>{{ $query->remarks }}</td>
					
					<td>Received Time Stamp:</td>
					<td>{{ $query->created_at }}</td>
				</tr>
				
				<tr>
					<td>Contact No.</td>
					<td>{{ $query->contact }}</td>
					
					<td>Passport Qty:</td>
					<td>{{ $query->psQty }}</td>
					
					<td>Token Number:</td>
					<td>{{ $query->tknNo ?? 'N/A' }}</td>
				</tr>

	

@if($fpdata)
<tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
    <td>Nationality:</td>
    <td>{{ $fpdata->nationality }}</td>

    <td>Date of Checking:</td>
    <td>{{ \Carbon\Carbon::parse($fpdata->Date)->format('Y-m-d') }}</td>

    <td>Remarks:</td>
    <td> 
    	{{ $fpdata->v_duration?->name ?? '-' }} /
		{{ $fpdata->entry?->name ?? '-' }} /
     
		{{ $query->visa->visa_type ?? '-'}}
    </td>
</tr>

<tr>
    <td>Visa fee:</td>
    <td>{{ $fpdata->visa_fee }} Tk</td>

    <td>Fax trans. charge:</td>
    <td>{{ $fpdata->fax_trans_charge }} Tk</td>

    <td>ICWF:</td>
    <td>{{ $fpdata->icwf }} Tk</td>
</tr>

<tr>
    <td>Visa app. charge:</td>
    <td>{{ $fpdata->visa_app_charge }} Tk</td>
	
	<td>Rupee Rate</td>
    <td>{{ $fpdata->rupee_rate }} Tk</td>
    
    <td>Total Amount:</td>
    <td>{{ $fpdata->total_amount }} Tk</td>

    
</tr>
@endif



				<tr>
					<td colspan="6">
						@else
						<p>No Recived data found for this Webfile.</p>
						@endif
					</td>
				</tr>
			</table>
			<br>
		</div>
		
		
		<div><b>Appointment Data</b></div>
		<div class="infoTable">
			<table class="w-full text-sm">
				@if($apt)
				<tr>
					<td>Webfile Number:</td>
					<td>{{$apt->WebFile_no}}</td>
					
					<td>Pay Status:</td>
					<td>{{ $apt->paystatus }}</td>
					<td>Center Name:</td>
					<td>{{ $apt->center }}</td>
				
					<!-- <td>Checked By:</td>
					<td>{{ $apt->checked_user }}</td> -->
				</tr>
				
				<tr>
					<td>Old Appointment:</td>
					<td>{{ $apt->prev_date }}</td>
					
					<td>Amount:</td>
					<td>{{ $apt->amount }}</td>
					<td>Visa Type:</td>
					<td>{{ $apt->visatype }}</td>
				
					<!-- <td>Checked At:</td>
					<td>{{ $apt->checked_on }}</td> -->
				</tr>
				
				<tr>
					<td>Current Appointment:</td>
					<td>{{ $apt->curr_date }}</td>
					
					<td>Transaction ID:</td>
					<td>{{ $apt->txnId }}</td>
							<td>Passport No:</td>
					<td>{{ $apt->passport }}</td>
					
				</tr>
				
				<tr>
					<td>Appointment Hour:</td>
					<td>{{ $apt->apt_hr }}</td>
					
					<td>Transaction Date:</td>
					<td>{{ $apt->txn_date }}</td>
			
							<td></td>
					<td></td>
					
				</tr>
				
				<tr>
					<td colspan="6">
						@else
						<p>No Appointment data found for this Webfile.</p>
						@endif
					</td>
				</tr>
			</table>
			<br>
		</div>
		
		
		<div><b>Tracking Information</b></div>
		<div class="trkTable">
			<table class="w-full text-sm">
				@if($query)
				<tr>
					<th>SL</th>
					<th>Date</th>
					<th>Webfile</th>
					<th>Passport</th>
					<th>Contact</th>
					<th>Action</th>
					<th>Remarks</th>
					<th>Created By</th>
					<th>Created At</th>
					<th>Update At</th>
				</tr>

				@foreach ($logs as $key => $data)
				<tr>
					<td>{{ ++$key }}</td>
					<td>{{ $data->Date ?? '' }}</td>
					<td>{{ $data->webref->Webfile ?? '' }}</td>
					<td>{{ $data->webref->passport ?? '' }}</td>
					<td>{{ $data->webref->contact ?? '' }}</td>
						@php
						$steps = [1 => 'Application Received', 2 => 'Sent to HCI/AHCI', 31 => 'Passport Sent to IVAC',32 => 'Receive from HCI',33 => 'DirectDelivery',4 => 'Passport Ready at Center',5 => 'Passport Delivered',11 => 'Biometric Captured',8 => 'Document Received',9 => 'DVD Created'];
						@endphp
					<td>{{ $steps[$data->stepId] ?? 'Unknown' }}</td>
					<td>{{ $data->remarks ?? '' }}</td>
					<td>{{ $data->user->name ?? '' }}</td>
					<td>{{ $data->created_at ?? '' }}</td>
					<td>{{ $data->updated_at ?? '' }}</td>
				</tr>
				
				<tr>
					<td colspan="6">
						@endforeach
						@else
						<p>No Tracking data found for this Webfile.</p>
						@endif
					</td>
				</tr>
			</table>
			<br>
		</div>
		
		
		<div><b>SMS Information</b></div>
		<div class="smsTable">
			<table class="w-full text-sm">
				@if($smsD)
				<tr>
					<th>SL</th>
					<th>Date</th>
					<th>Step</th>
					<th>Contact</th>
					<th width="40%">Text</th>
					<th>Created By</th>
					<th>Reference</th>
					<th>DeliveryTime</th>
				</tr>
				
				@foreach ($smsD as $key => $data)
				<tr>
					<td>{{ ++$key }}</td>
					<td>{{ $data->Date ?? '' }}</td>
						@php
						$steps = [0 => 'OTP',1 => 'Receive', 2 => 'Sent To HCI', 3 => 'Receive from HCI',4 => 'Ready at center', 41 => 'PendingAlert' ,5 => 'Delivered'];
						@endphp
					<td>{{ $steps[$data->type] ?? 'Unknown' }}</td>
					<td>{{ $data->contact ?? '' }}</td>
					<td>{{ $data->text  ?? '' }}</td>
					<td>{{ $data->created_at ?? '' }}</td>
					<td>{{ $data->txn ?? '' }}</td>
					<td>{{ $data->Delivery_time ?? '' }}</td>
				</tr>
				
				<tr>
					<td colspan="8">
						@endforeach
						@else
						<p>No SMS data found for this Webfile.</p>
						@endif
					</td>
				</tr>
			</table>
			<br>
		</div>   
		<div><b>NIC Data</b></div>
		<div class="smsTable">
			<table class="w-full text-sm">
				@if($nicData)
				<tr>
					<th>SL</th>
					<th>Webfile</th>
					<th>Passport</th>
					<th>Name</th>
					<th>Date of Birth</th>
					<th>Contact</th>
				</tr>
				
				@foreach ($nicData as $key => $data)
				<tr>
					<td>{{ ++$key }}</td>
					<td>{{ $data->webfile ?? '' }}</td>
					<td>{{ $data->passport }}</td>
					<td>{{ $data->Name ?? '' }}</td>
					<td>{{ $data->dob  ?? '' }}</td>
					<td>{{ $data->contact ?? '' }}</td>
				</tr>
				
				<tr>
					<td colspan="8">
						@endforeach
						@else
						<p>No NIC data found for this Webfile.</p>
						@endif
					</td>
				</tr>
			</table>
		</div>       
    </div>


    </div> 
@endsection

@push('scripts')
    <!-- @vite(['resources/js/scripts/region.js']) -->
    @vite(['resources/js/scripts/index_script.js'])
@endpush

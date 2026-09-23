@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Application Receive" />
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
		<div class="status bg-white shadow rounded mt-2 max-w-full mx-auto">
			
		<form method="POST" action="{{ route('app-receive.store') }}"  >
			@csrf
			@method('POST')
					
            <table width=100%>
			  <tr>
				<td width="22%">
					<label class="label" for="sticker_type">Service</label><br>
					<select class="form-control w-full px-3 py-2 border" name="svctypeId" required="required" id="svctypeId">
					   <option value=""></option>
					   @foreach($svcType as $item)
						<option value="{{ $item['id'] }}" {{$lastSvc == $item['id'] ? 'selected' : '' }}  >{{ $item['service_name'] }}</option>
						@endforeach
					</select>
				</td>
				
				<td width="22%">
					<div class="label">Counter</div>
					<div class="number">
						<b><input type="text" id="counterNo" name="counterNo" readonly class="number px-2 py-1" readonly  value="{{$counter}}"></b>
					</select>
						<!-- <b><input type="text" id="counterNo" name="counterNo" readonly class="number px-2 py-1" readonly  value="2"></b> -->
					</div>
				</td>
				
				<td width="15%">
					<div class="label">Current Token</div>
					<div class="number">
						<b><input type="text" id="TokenNo" name="TokenNo" readonly class="number px-2 py-1"  value="{{ $lastTkn }}"></b>
					</div>
				</td>
				<td width=50px;>
					<div class="label">File Qty</div>
					<div class="number">
						<b><input type="text" id="tokenQty" name="tokenQty" readonly class="number px-2 py-1" value="{{ session('tokenQty', '') }}"></b>
					</div>
				</td>
				<td width=30px;>
					<button id="defer" type="button" class="callbtn bg-blue-500 text-white px-3 py-1 max-w-full"><i class="fas fa-clock fa-lg"></i></button>
				</td>
				
				<td>
					<div class="label">Received</div>
					<div class="number"><b>{{$total}}</b></div>
				</td>
				<td>
					<div class="label"> 
						<button type="button" id="tdd" class="callbtn bg-green-500 text-white px-3 py-1 max-w-full"><b>TDD</b></button>	
					</div>
					 
				</td>
			  </tr>
			</table>
        </div>
		
		<!-- Queue & Form Area -->

		<div class="queue bg-white shadow mt-2 max-w-full mx-auto">
            <table border="0">
			  <tr>
				<td width=70px;>
					<div class="currentq">
						<button type="button" id="loadTokenNo" class="callbtn bg-blue-500 text-white px-3 py-1 max-w-full"><b>Call</b></button>	
						<div class="label">Q List</div>
						
						<ol id="currQList" class="QList">
							@foreach ($currQ as $item)
								<li>{{ $item->token_number }}</li>
							@endforeach
						</ol>
					</div>
				</td>
				<td width=70px;>
					<div class="waitingq">
				<!-- 		<button id="Wait" type="button" class="QList waitq">
							No<br>
							tokens<br>
							found<br>
						</button> -->
						<div class="label">Waiting Q</div>

						<ol id="defQList" class="QList">
							@foreach ($waitQ as $item)
								<li>{{ $item->token_number }}</li>
							@endforeach
						</ol>
					</div>
				</td>
<td>
<div class="form-container">

	<div class="form-row">
		<label for="form_date">Barcode</label>
		<input class="barcode" type="text" value="" id="code" name="code" maxlength="13">
	</div>
	
	<div id="security_code" style="display: none">
		
		<div class="form-row">
			<label>Op Type: </label>
			<select name="op_type" id="op_type"  >
				<option value="manual"{{$lastoptype == 'manual' ? 'selected' : '' }} >Manual</option>
				<option value="auto"{{$lastoptype == 'auto' ? 'selected' : '' }} >Auto</option>
			</select>
		</div>
		
		<div class="form-row">
            <label>Webfile: </label>
			<input type="text" id="wf_no" name="wf_no" autocomplete="off" maxlength="12">
		</div>

	  <div id="web_check" style="display: none"> 

		<div class="form-row">
			<label>P Method: </label>
			<select class="pMethod" name='payment_method' id="payment_method" >
			<!-- Options will be added by JS -->
			</select>
			<!-- <button type="button" id="Sendotp" class="btnotp bg-green-500 text-white px-3 py-1">Send OTP</button> -->
		</div>
              
		<div class="form-row">
			<label  >Name:</label> 
			<input type="text" id="name" name="name" maxlength="30"  class="hideDiv">
		</div>

		<div class="form-row">
			<label>Passport:</label>
			
			<input class="pass1" type='text' id='passport' name='passport1'  maxlength="10" placeholder="Passport Number" title="Passport Number">
			<input class="pass2" type='text' id='passport2' name='passport2'  maxlength="10"   readonly > 
			<input class="passqty" type="number" name="OldPass" min="1" max="50" id="OldPass" value="1" title="Old Passport Qty"  required>
		</div>

		<div class="form-row">
			<label>Contact:&nbsp;</label>
			<input class="contact" type="text" id="contact" name="contact" maxlength="11">
		<!-- 	<input class="typeotp" type="text" id="otp" name="otp" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="4" class="hideDiv" title="OTP type here" placeholder="OTP"> -->
		</div>
		
		<div class="form-row">
			<label></label>
			<input class="sslVisaType" type="text" id="v_apt" name="v_apt" autocomplete="off" readonly>
		</div>

		<div class="form-row">
			<label style="display: inline-block;">Visa Type:&nbsp;</label>
			<select name='visaType' id="visaType"  aria-required="true">
				@foreach($VisaType as $item)
					<option value="{{$item['id']}}" {{$lastvisa == $item['id'] ? 'selected' : '' }} >{{ $item['visa_type'] }}</option>
				@endforeach
            </select>
		</div>
		
		<div class="form-row">
			<label>Sticker: </label>
			<select class="stType" name="st_color" id="st_color" >
				@foreach($stickerList as $item)
				<option value="{{$item['id']}}"{{$laststicker == $item['id'] ? 'selected' : '' }} >{{ $item['sticker'] }}</option>
				@endforeach
			</select>
			<input class="stNumber" type="text" id="st_no" name="sticker_no" onfocus="this.value=''" min="1" autocomplete="off" placeholder="Sticker Number">
		</div>
                	
		<div class="form-row">
			<label> Correction </label>
		    <input class="corr" type="checkbox" id="toggleCorfee">
			
		    <div  id="corfeeRow" style="display: none; margin-left: 10px;">
			    <input  class="corrFee" type="number" id="corfee" name="corfee" value="0" >
			</div>
     	</div>
	 
	<div id="fee_check" style="display: none">
	
		<div class="form-row">
			<label>Fee: </label>
			<input class="pFee" type="number" id="profee" maxlength="5" name="profee" title="Proc Fee" placeholder="Proc Fee" value="0"> 
			<input class="sFee" type="number" name="spfee" maxlength="5" id="spfee" title="SP Fee" placeholder="Sp Fee" value="0">
		</div>
		
	</div>


		<div class="form-row2">
			<label>BIO: </label>
			<input type="radio" name="bio_st" id="bio_st" <?php if (isset($bio_st) && $bio_st=="1") echo "checked";?> value="1" >&nbsp;&lt;12 &nbsp;&nbsp;&nbsp;
			<input type="radio" name="bio_st" id="bio_st"<?php if (isset($bio_st) && $bio_st=="2") echo "checked";?> value="2" >&nbsp;12-70 &nbsp;&nbsp;&nbsp;
			<input type="radio" name="bio_st" id="bio_st"<?php if (isset($bio_st) && $bio_st=="3") echo "checked";?> value="3" >&nbsp;70+ &nbsp;&nbsp;&nbsp;
			<input type="radio" name="bio_st" id="bio_st"<?php if (isset($bio_st) && $bio_st=="4") echo "checked";?> value="4" >&nbsp;Updated 
        </div>
		
		<div class="form-row">
			<label>Transaction ID:</label>
			<input type="text" id="fmember" name="fmember"   autocomplete="off" >
		</div>
		
		<div class="form-row">
			<label  >Remarks:</label>
			<input type="text"  id="remarks" name="remarks" required >
        </div>

                <!-- <div id="app_accept" style="display: none"> -->

		<button id="bt3" type="button" class="reject text-white px-4 py-2 rounded">REJECT</button>
		
		<button id="bt2" name="accept" class="accept text-white px-4 py-2 rounded"  type="submit">ACCEPT</button> 


		</div>
	</div>
</div>
</td>
			  </tr>
			</table>
        </div>
		

    </div>


    <div id="rejectModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4" id="modalTitle">Reject Application</h2>
             @include('pages.AppReceive.partials.create')

        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/scripts/app_receive.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

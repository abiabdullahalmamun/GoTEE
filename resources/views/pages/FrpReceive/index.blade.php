@extends('layouts.app')
@push('style')
@endpush
@section('content')
    <div class="mx-auto">
        <x-page-header title="Foreign Passport Receive" />
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
		<form method="POST" action="{{ route('frpReceive.store') }}"  >
		<div class="status bg-white shadow rounded mt-2 max-w-full mx-auto">
			
			@csrf
			@method('POST')
					
            <table width=100%>
			  <tr>
                <td width="15%">
                    <label class="label" for="sticker_type">Service</label><br>
                    <select class="form-control w-full px-3 py-2 border" name="svctypeId" required="required" id="svctypeId">
                       <option value=""></option>
                       @foreach($svcType as $item)
                        <option value="{{ $item['id'] }}" {{$lastSvc == $item['id'] ? 'selected' : '' }}  >{{ $item['service_name'] }}</option>
                        @endforeach
                    </select>
                </td>
				
				<td width="15%">
					<div class="label">Counter</div>
					<div class="number">
						<b><input type="text" id="counterNo" name="counterNo" readonly class="number px-2 py-1" readonly  value="{{$counter}}"></b>
					<!-- </select> -->
						<!-- <b><input type="text" id="counterNo" name="counterNo" readonly class="number px-2 py-1" readonly  value="2"></b> -->
					</div>
				</td>
                <td width="10%">
                    <div class="label">Current Token</div>
                    <div class="number">
                        <b><input type="text" id="TokenNo" name="TokenNo" readonly class="number px-2 py-1"  value="{{ $lastTkn }}"></b>
                    </div>
                </td>
				<td width="50%">
                   
				</td>
 				<td width="10%">
					<div class="label">Received</div>
					<div class="number"><b>{{$total}}</b></div>
				</td>
 
			  </tr>
			</table>
        </div>
		
		<!-- Queue & Form Area -->

		<div class="queue bg-white shadow mt-2 max-w-full mx-auto">
            <table border="0">
			  <tr>
 
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
	 
 
		
<div class="form-row2">
    <label>BIO: </label>
    <input type="radio" name="bio_st" id="bio_st_1" value="1" @checked(isset($bio_st) && $bio_st=="1")>&nbsp;&lt;12 &nbsp;&nbsp;&nbsp;
    <input type="radio" name="bio_st" id="bio_st_2" value="2" @checked(isset($bio_st) && $bio_st=="2")>&nbsp;12-70 &nbsp;&nbsp;&nbsp;
    <input type="radio" name="bio_st" id="bio_st_3" value="3" @checked(isset($bio_st) && $bio_st=="3")>&nbsp;70+ &nbsp;&nbsp;&nbsp;
    <input type="radio" name="bio_st" id="bio_st_4" value="4" @checked(isset($bio_st) && $bio_st=="4")>&nbsp;Updated
</div>

	
		</div>
	</div>
</div>
</td>
<td></td>
<td></td>
<td>
	<div class="form-container"> 
	 <div id="web_check2" style="display: none;"> 
		 
         @if($currencyRate)
            <div class="form-row text-red-600 font-semibold">
                {{ $currencyRate->currency_name }}
                Rate({{ $currencyRate->currency_rate }})
                Updated on {{ $currencyRate->created_at->format('Y-m-d') }}
            </div>
        @endif
		<div class="form-row">
			<label style="display: inline-block;">Nationality:&nbsp;</label>
			<input type="text" id="nationality" name="nationality"  list="nationalityList" maxlength="30" class="hideDiv"  placeholder="Select or type" >
<datalist id="nationalityList">
    <option value="Afghan">
    <option value="Albanian">
    <option value="Algerian">
    <option value="American">
    <option value="Andorran">
    <option value="Angolan">
    <option value="Argentine">
    <option value="Armenian">
    <option value="Australian">
    <option value="Austrian">
    <option value="Azerbaijani">

    <option value="Bahamian">
    <option value="Bahraini">
    <option value="Bangladeshi">
    <option value="Barbadian">
    <option value="Belarusian">
    <option value="Belgian">
    <option value="Belizean">
    <option value="Beninese">
    <option value="Bhutanese">
    <option value="Bolivian">
    <option value="Bosnian">
    <option value="Botswanan">
    <option value="Brazilian">
    <option value="British">
    <option value="Bruneian">
    <option value="Bulgarian">
    <option value="Burkinabe">
    <option value="Burmese">
    <option value="Burundian">

    <option value="Cambodian">
    <option value="Cameroonian">
    <option value="Canadian">
    <option value="Cape Verdean">
    <option value="Central African">
    <option value="Chadian">
    <option value="Chilean">
    <option value="Chinese">
    <option value="Colombian">
    <option value="Comorian">
    <option value="Congolese">
    <option value="Costa Rican">
    <option value="Croatian">
    <option value="Cuban">
    <option value="Cypriot">
    <option value="Czech">

    <option value="Danish">
    <option value="Djiboutian">
    <option value="Dominican">
    <option value="Dutch">

    <option value="Ecuadorean">
    <option value="Egyptian">
    <option value="Emirati">
    <option value="Equatorial Guinean">
    <option value="Eritrean">
    <option value="Estonian">
    <option value="Ethiopian">

    <option value="Fijian">
    <option value="Finnish">
    <option value="French">

    <option value="Gabonese">
    <option value="Gambian">
    <option value="Georgian">
    <option value="German">
    <option value="Ghanaian">
    <option value="Greek">
    <option value="Grenadian">
    <option value="Guatemalan">
    <option value="Guinean">
    <option value="Guyanese">

    <option value="Haitian">
    <option value="Honduran">
    <option value="Hungarian">

    <option value="Icelandic">
    <option value="Indian">
    <option value="Indonesian">
    <option value="Iranian">
    <option value="Iraqi">
    <option value="Irish">
    <option value="Israeli">
    <option value="Italian">
    <option value="Ivorian">

    <option value="Jamaican">
    <option value="Japanese">
    <option value="Jordanian">

    <option value="Kazakh">
    <option value="Kenyan">
    <option value="Kuwaiti">
    <option value="Kyrgyz">

    <option value="Laotian">
    <option value="Latvian">
    <option value="Lebanese">
    <option value="Liberian">
    <option value="Libyan">
    <option value="Liechtensteiner">
    <option value="Lithuanian">
    <option value="Luxembourgish">

    <option value="Macedonian">
    <option value="Malagasy">
    <option value="Malawian">
    <option value="Malaysian">
    <option value="Maldivian">
    <option value="Malian">
    <option value="Maltese">
    <option value="Mauritanian">
    <option value="Mauritian">
    <option value="Mexican">
    <option value="Moldovan">
    <option value="Monacan">
    <option value="Mongolian">
    <option value="Montenegrin">
    <option value="Moroccan">
    <option value="Mozambican">

    <option value="Namibian">
    <option value="Nepalese">
    <option value="New Zealander">
    <option value="Nicaraguan">
    <option value="Nigerian">
    <option value="Norwegian">

    <option value="Omani">

    <option value="Pakistani">
    <option value="Panamanian">
    <option value="Paraguayan">
    <option value="Peruvian">
    <option value="Philippine">
    <option value="Polish">
    <option value="Portuguese">

    <option value="Qatari">

    <option value="Romanian">
    <option value="Russian">
    <option value="Rwandan">

    <option value="Saudi Arabian">
    <option value="Scottish">
    <option value="Senegalese">
    <option value="Serbian">
    <option value="Seychellois">
    <option value="Singaporean">
    <option value="Slovak">
    <option value="Slovenian">
    <option value="Somali">
    <option value="South African">
    <option value="South Korean">
    <option value="Spanish">
    <option value="Sri Lankan">
    <option value="Sudanese">
    <option value="Swedish">
    <option value="Swiss">
    <option value="Syrian">

    <option value="Taiwanese">
    <option value="Tajik">
    <option value="Tanzanian">
    <option value="Thai">
    <option value="Togolese">
    <option value="Tunisian">
    <option value="Turkish">
    <option value="Turkmen">

    <option value="Ugandan">
    <option value="Ukrainian">
    <option value="Uruguayan">
    <option value="Uzbek">

    <option value="Venezuelan">
    <option value="Vietnamese">

    <option value="Welsh">
    <option value="Yemeni">
    <option value="Zambian">
    <option value="Zimbabwean">
</datalist>

		</div>	
		<!-- <div class="flex items-center gap-2"> -->
	<div class="form-row">
    <label class="whitespace-nowrap">Duration:</label>

    <select name="duration" id="duration"  class="h-8 px-2 text-sm leading-tight border border-gray-300 rounded flex-1 min-w-0" aria-required="true">
        <option value="">Select</option>
        @foreach($visaDuration as $item)
            <option value="{{$item['id']}}" {{ $lastvisa == $item['id'] ? 'selected' : '' }}>
                {{ $item['name'] }}
            </option>
        @endforeach
    </select>

    <label class="whitespace-nowrap">EntryType:</label>
    <select
        name="entryType"  id="entryType" class="h-8 px-2 text-sm leading-tight border border-gray-300 rounded flex-1 min-w-0" aria-required="true">
        <option value="">Select</option>
        @foreach($entrytype as $item)
            <option value="{{$item['id']}}" {{ $lastvisa == $item['id'] ? 'selected' : '' }}>
                {{ $item['name'] }}
            </option>
        @endforeach
    </select>
</div>

        <div class="form-row2">
            <label>GRATIS: </label>

            <input type="radio" name="gratis" id="gratis_1" value="1"
                {{ (isset($gratis) && $gratis == "1") ? 'checked' : '' }}>
            &nbsp;YES &nbsp;&nbsp;&nbsp;

            <input type="radio" name="gratis" id="gratis_2" value="0"
                {{ (!isset($gratis) || $gratis == "0") ? 'checked' : '' }}>
            &nbsp;NO &nbsp;&nbsp;&nbsp;
        </div>
		<div class="form-row">
			<label>BookNo: </label>
			<input class="pFee" type="text" id="BookNo" maxlength="5" name="BookNo" title="Proc Fee" placeholder="BookNo" value="{{$lastBook}}" readonly > 
			<label>ReceipNot: </label>
			<input class="sFee" type="text" name="RecptNo" maxlength="10" id="RecptNo" title="SP Fee" placeholder="ReceiptNo" value="">
            <input type="number" name="rupee_rate" id="rupee_rate" value="{{$currencyRate->currency_rate}}" hidden>
		</div>	


		<div class="form-row">
		    <label class="whitespace-nowrap">Visa Fee:</label>
		    <input class="pFee" type="number" id="visafee" maxlength="5" name="visafee" title="Proc Fee" placeholder="Visa Fee" value="0"> 
		 
		    <label class="whitespace-nowrap">ICWF:</label>
			<input class="pFee" type="number" id="icwf" maxlength="5" name="icwf" title="Proc Fee" placeholder="Proc Fee" value="0"> 
		</div>	
		 
		<div class="form-row">
			<label>FaxTransChg: </label>
			<input class="pFee" type="number" id="faxcharge" maxlength="5" name="faxcharge" title="Proc Fee" placeholder="Proc Fee" value="0"> 
     
          <label>VisaAppChg: </label>
            <input class="pFee" type="number" id="visaApp" maxlength="5" name="visaApp" title="Proc Fee" placeholder="Proc Fee" value="0"> 
           
    
		</div>
        <div class="form-row">
            <!-- <div id="visaApp_check" style="display: none"> -->
             
            <label>Total: </label>
            <input class="pFee" type="number" id="totalfee" maxlength="5" name="totalfee" title="Proc Fee" placeholder="Proc Fee" value="0"> 
             <div class="form-row">
                <label>P Method: </label>
                <select class="pMethod" name='payment_method' id="payment_method" >  </select>
            </div>
        </div>
        <div class="form-row">
             <div id="txn_check" style="display: none">
                <label>Transaction ID:</label>
                <input class="pFee" type="text" id="txnId" name="txnId"   autocomplete="off" >
            </div>
        </div>
		<!-- <div class="form-row">
			
       </div> -->

		 	<div class="form-row">
				<label  >Remarks:</label>
				<input type="text"  id="remarks" name="remarks"  >
	        </div>
		 	<button id="bt3" type="button" class="reject text-white px-4 py-2 rounded" >REJECT</button>
		
			<button id="bt2" name="accept" class="accept text-white px-4 py-2 rounded"  type="submit">ACCEPT</button> 

		 </div>
	</div>
</td>
			  </tr>
			</table>
        </div>
		</form>

    </div>


    <div id="rejectModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[1100]">
        <div class="bg-white p-6 rounded w-96 relative">
            <h2 class="text-lg font-bold mb-4" id="modalTitle">Reject Foreign Application</h2>
             @include('pages.FrpReceive.partials.create')

        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/scripts/frp_receive.js'])
    @vite(['resources/js/scripts/index_script.js'])
@endpush

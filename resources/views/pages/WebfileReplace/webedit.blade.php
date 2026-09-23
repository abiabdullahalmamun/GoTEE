@extends('layouts.app')

@section('content')
    <div class="mx-auto ">
        <div class="max-w-full mx-auto">
            <x-page-header title="Replace Webfile"/>
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
        </div>

        <div class="bg-yellow-200 shadow rounded p-6 mt-1 max-w-full mx-auto">
            <form action="{{ route('web-replace.update', $data->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Name Field -->
                    <div class="w-full">
                        <label for="name" class="block text-gray-700 font-semibold mb-1">Webfile</label>
                        <input type="text" id="recId" name="recId" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->id }}" readonly required hidden>
                        <input type="text" id="Webfile" name="Webfile" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->Webfile }}" required>

                        @error('')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field (Readonly) -->
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Name</label>
                         <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->ApplicantName }}"   required>
                        
                    </div>
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Passport</label>
                         <input type="text" id="passport" name="passport" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->passport }}"   required>
                        
                    </div>
                   <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">Contact</label>
                         <input type="text" id="contact" name="contact" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->contact }}"   required>
                        
                    </div>
                     <div class="w-full">
                        <label for="role" class="block text-gray-700 font-semibold mb-1">Sticker Type</label>
                    
                    <select name="stickertype" id="stickertype"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                    <option value="">All</option>
                    @foreach($stickers as $itm)
                        <option value="{{ $itm->id }}" {{ (int)$itm->id === (int)$data->stickertype ? 'selected' : '' }}  > {{ $itm->sticker }}
                        </option>
                    @endforeach
                </select>

                </div>

                     <div class="w-full">
                        <label for="email" class="block text-gray-700 font-semibold mb-1">stickerNo</label>
                         <input type="text" id="stickerNo" name="stickerNo" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->stickerNo }}"   required>
                        
                    </div>
       

                    <!-- Confirm Password Field -->
                    
                    
                    <div class="w-full">
                        <label for="role" class="block text-gray-700 font-semibold mb-1">VisaType</label>
                    
                    <select name="visatype" id="visatype"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                    <option value="">All</option>
                    @foreach($visaType as $itm)
                        <option value="{{ $itm->id }}" {{ (int)$itm->id === (int)$data->visatype ? 'selected' : '' }}  > {{ $itm->visa_type }}
                        </option>
                    @endforeach
                </select>

                </div>
                <div class="w-full">
                    <label for="role" class="block text-gray-700 font-semibold mb-1">Correction Fee</label>
                    
                  <input type="number" id="corrfee" name="corrfee" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $data->corrFee ?? 0 }}" required>

                  <input type="text" id="remarks" name="remarks"  value="{{ $data->remarks }}" required hidden>
       
                </div>
 

                @if($fpdata)
          <div class="w-full">
            </div>

      <div class="w-full">
        <hr class="w-full border-t-2 border-red-500 my-4">
    </div>
         <div class="w-full">
        <hr class="w-full border-t-2 border-red-500 my-4">
    </div>
         <div class="w-full">
        <hr class="w-full border-t-2 border-red-500 my-4">
    </div>
         <div class="w-full">
          <label>GRATIS: </label>
          <br>
            <input   type="radio" name="gratis" id="gratis_1" value="1"
                {{ (isset($gratis) && $gratis == "1") ? 'checked' : '' }}>
            &nbsp;YES &nbsp;&nbsp;&nbsp;

            <input type="radio" name="gratis" id="gratis_2" value="0"
                {{ (!isset($gratis) || $gratis == "0") ? 'checked' : '' }}>
            &nbsp;NO &nbsp;&nbsp;&nbsp;

        </div>           
                 <div class="w-full">
                    <label for="role" class="block text-gray-700 font-semibold mb-1">Nationality</label>
                   
            <input type="text" id="nationality" name="nationality"  list="nationalityList" maxlength="30" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" value="{{ $fpdata->nationality ?? 0 }}" required>
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
        <div class="w-full">
            <label for="role" class="block text-gray-700 font-semibold mb-1">Visa Duration</label>
           <select name="duration" id="duration"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                <option value="">Select</option>
                @foreach($visaDuration as $item)
                    <option value="{{ $item->id }}" {{ (int)$item->id === (int)$fpdata->duration ? 'selected' : '' }}  > {{ $item->name }}  </option>

                @endforeach
            </select>
        </div>
        <div class="w-full">
            <label for="role" class="block text-gray-700 font-semibold mb-1">Entry Type</label>
           <select name="entryType" id="entryType"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" required>
                <option value="">Select</option>
                @foreach($entrytype as $item)
                    <option value="{{ $item->id }}" {{ (int)$item->id === (int)$fpdata->entryType ? 'selected' : '' }}  > {{ $item->name }}  </option>

                @endforeach
            </select>
        </div>

       <div class="w-full">
        <label>BookNo: </label>
            <input  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" type="text" id="BookNo" maxlength="5" name="BookNo" placeholder="BookNo" value="{{$lastBook}}" readonly > 
        </div>
         <div class="w-full">
            <label>ReceipNot: </label>
            <input  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" type="text" name="RecptNo" maxlength="10" id="RecptNo"  placeholder="" value="{{ $fpdata->ReceiptNo }}" >
            <input type="number" name="rupee_rate" id="rupee_rate" value="{{$currencyRate->currency_rate}}" hidden>
         </div>
         <div class="w-full">
        <label>Visa Fee: </label>
            <input  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" type="text" id="visafee" name="visafee" placeholder="BookNo" value="{{ $fpdata->visa_fee}}" required > 
        </div>
                <div class="w-full">
        <label>ICWF: </label>
            <input  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" type="text" id="icwf" name="icwf" placeholder="BookNo" value="{{ $fpdata->icwf}}" required > 
        </div>
                <div class="w-full">
        <label>FaxTransChg:: </label>
            <input  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" type="text" id="faxcharge" name="faxcharge" placeholder="BookNo" value="{{ $fpdata->fax_trans_charge}}" required > 
        </div>
                <div class="w-full">
        <label>VisaAppChg: </label>
            <input  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" type="text" id="visaApp" name="visaApp" placeholder="BookNo" value="{{ $fpdata->visa_app_charge}}" required > 
        </div>
                    <div class="w-full">
        <label>Total Fee: </label>
            <input  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring focus:ring-blue-300 focus:border-blue-500" type="text" id="totalfee" name="totalfee" placeholder="BookNo" value="{{ $fpdata->total_amount}}" required > 
        </div>
                @endif
                <!-- Submit Button -->
              
              <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition">
                    Update File
                </button>
              </div>  
            </form>
        </div>
    </div>
@endsection

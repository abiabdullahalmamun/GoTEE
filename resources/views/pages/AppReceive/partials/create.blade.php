<form method="POST" id="createRejectForm" action="{{ route('applications.reject') }}">
    @csrf
    @method('POST')
  
     <div class="flex flex-col md:flex-row gap-4 w-full">
        <div class="flex-1">
            <label   for="name">Webfile</label><br>
             <input  class="w-full border px-3 py-2 rounded" type="text" id="webNo" name="webNo" autocomplete="off" maxlength="12"   required readonly>
      
        </div>
        <div class="flex-1">
             <label   for="name">Passport</label><br>
             <input  class="w-full border px-3 py-2 rounded" type="text" id="a_pass" name="a_pass" autocomplete="off" required>
        </div>
    </div>
    <div class="flex flex-col md:flex-row gap-4 w-full">
        <div class="flex-1">
             <label   for="name">Name</label><br>
             <input  class="w-full border px-3 py-2 rounded" type="text" id="a_name" name="a_name" autocomplete="off" required >
        </div>
    </div>
     <div class="flex flex-col md:flex-row gap-4 w-full">
          <div class="flex-1">
             <label   for="name">Contact</label><br>
             <input  class="w-full border px-3 py-2 rounded" type="text" id="a_contact" name="a_contact" autocomplete="off" required>
        </div>
        <div class="flex-1">
             <label for="name">VisaType</label><br>
             <select name='a_visaType' id="a_visaType"  class="w-full border px-3 py-2 rounded"  >
                @foreach($VisaType as $item)
                    <option value="{{$item['id']}}" {{$lastvisa == $item['id'] ? 'selected' : '' }} >{{ $item['visa_type'] }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="row">
        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
                <label for="Contact">Initial</label><br>
               @foreach($rejectionReasons as $reason)
                <div>
                    <input type="checkbox" name="rejection_reasons[]" value="{{ $reason->id }}" id="reason_{{ $reason->id }}" >
                    <label for="reason_{{ $reason->id }}">{{ $reason->reason_name }}</label>
                </div>
                @endforeach
                <input type="hidden" id="rejectRequired">
            </div>
        </div>


        <div class="mb-3 text-right">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="cancelReject">Cancel</button>
        </div>
    </div>

    <!-- <input type="hidden" name="selected_rejection_reasons" id="selected_rejection_reasons"> -->

</form>

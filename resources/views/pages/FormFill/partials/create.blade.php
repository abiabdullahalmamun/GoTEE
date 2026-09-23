<form method="POST" id="createRejectForm" action="{{ route('applications.reject') }}">
    @csrf
    @method('POST')
    <div class="row">
        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
                <label for="name">WEBFILE</label><br>
             <input type="text" id="webNo" name="webNo" autocomplete="off" maxlength="12"   >
            </div>
        </div>
        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
                <label for="Contact">Initial</label><br>
               @foreach($rejectionReasons as $reason)
                <div>
                    <input type="checkbox" name="rejection_reasons[]" value="{{ $reason->id }}" id="reason_{{ $reason->id }}">
                    <label for="reason_{{ $reason->id }}">{{ $reason->reason_name }}</label>
                </div>
            @endforeach

            </div>
        </div>


        <div class="mb-3 text-right">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeModal">Cancel</button>
        </div>
    </div>

    <!-- <input type="hidden" name="selected_rejection_reasons" id="selected_rejection_reasons"> -->

</form>

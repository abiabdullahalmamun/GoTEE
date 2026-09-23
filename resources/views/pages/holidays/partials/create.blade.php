<form method="POST" id="createShopForm" action="{{ route('holidays.store') }}">
    @csrf
    @method('POST')
    <div class="row">
        <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
            <div class="form-group flex-1">
               <label for="Contact">From Date</label><br>
              <input type="date" name="from_date" class="form-control @error('symbol') is-invalid @enderror w-full px-3 py-2 border rounded" value="{{ old('symbol') }}" placeholder="type" required>
                @error('mac')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group flex-1">
                <label for="endHr">To Date</label><br>
                <input type="date" name="to_date" class="form-control @error('days') is-invalid @enderror w-full px-3 py-2 border rounded" value="{{ old('days') }}" placeholder="" required>
                @error('days')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3 col-12 col-md-4 col-lg-6">
             <div class="form-group flex-1">
               <label for="Contact">Description</label><br>
              <input type="text" name="description" class="form-control @error('description') is-invalid @enderror w-full px-3 py-2 border rounded" value="{{ old('description') }}" placeholder="type" required>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
   
        </div>


        @php
            $today = \Carbon\Carbon::today()->format('l'); // e.g. "Thursday"
        @endphp
  <div class="mb-3 col-12 col-md-4 col-lg-6">
    
      <div class="form-group flex-1">
         <label for="Contact">Day</label><br>
        <select name="weekday" class="form-control @error('description') is-invalid @enderror w-full px-3 py-2 border rounded">
             <option value="" > Select </option>
            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                <option value="{{ $day }}" > {{ $day }}
                </option>
            @endforeach
        </select>
    </div>
</div>
        <div class="mb-4 w-full md:w-1/2 lg:w-1/3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="flex items-center space-x-3">
                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="1"
                        class="form-radio text-green-600 focus:ring-green-500 @error('status') border-red-500 @enderror"
                        checked>
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="0"
                        class="form-radio text-red-600 focus:ring-red-500 @error('status') border-red-500 @enderror">
                    <span class="ml-2 text-sm text-gray-700">Inactive</span>
                </label>
            </div>

            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


<div class="mb-3 col-12 col-md-4 col-lg-6">
    
      <div class="form-group flex-1">
        <label for="Contact" class="text-red-600 italic">*Please Select Same Date for a single holiday</label><br>
       <label for="Contact" class="text-red-600 italic">*Please Select Different Date, Day of week for multiple holiday</label><br>
       
    </div>


        <div class="mb-3 text-right">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeModal">Cancel</button>
        </div>
    </div>
</form>

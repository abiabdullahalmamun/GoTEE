<form method="POST" id="createShopForm" action="{{ route('devices.store') }}">
    @csrf
    @method('POST')
    <div class="row">
 
<div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
       <label for="name">DeviceID</label><br>
       <input type="text" name="devID"
                       class="form-control @error('devID') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('devID') }}"  required>
         @error('devID')
                    <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
       <label for="name">Device Type</label><br>
       <select class="form-control w-full px-3 py-2 border" name="devType" required="required" id="devType">
            <option value=""></option>
            <option value="1">SEWING</option>
            <option value="2">QC</option>
            <option value="3">INPUT</option>
        </select>     
        @error('devType')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Service dropdown, hidden by default --}}
<div class="mb-3 col-12 col-md-4 col-lg-6" id="serviceBox" style="display: none;">
    <div class="form-group flex-1">
       <label for="name">Service</label><br>
       <select class="form-control w-full px-3 py-2 border" name="serviceId[]" id="serviceId" multiple>
            @foreach($serviceList as $item)
            <option value="{{ $item['id'] }}">{{ $item['service_name'] }}</option>
            @endforeach
        </select>     
        @error('serviceId')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>




        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group flex-1">
                <label for="startHr">MAC</label><br>
                <input type="text" name="mac"
                       class="form-control @error('mac') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('mac') }}"  required>
                @error('mac')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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

        <div class="mb-3 text-right">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeModal">Cancel</button>
        </div>
    </div>
</form>

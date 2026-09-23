<form method="POST" id="createShopForm" action="{{ route('visaDuration.store') }}">
    @csrf
    @method('POST')
    <div class="row">
 

        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group flex-1">
                <label for="startHr">Visa Duration</label><br>
                <input type="text" name="nameD"
                       class="form-control @error('nameD') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('nameD') }}"  required>
                @error('nameD')
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

<form method="POST" id="editDeviceForm">
    @csrf
    @method('PUT')
    <div class="row">
 
 
 
<div class="mb-3 col-12 col-md-6">
    <div class="form-group">
        <label for="edit_name">Operation Name</label>
        <input type="text" name="recId" id="recId" hidden>
        <input type="text"
            name="name"
            id="edit_name"
            class="form-control w-full px-3 py-2 border rounded"
            required>
    </div>
</div>
 
        <div class="mb-4 w-full md:w-1/2 lg:w-1/3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="flex items-center space-x-3">
                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="1" id="edit_status_active"
                        class="form-radio text-green-600 focus:ring-green-500 @error('status') border-red-500 @enderror"
                        checked>
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="radio" name="status" value="0" id="edit_status_inactive"
                        class="form-radio text-red-600 focus:ring-red-500 @error('status') border-red-500 @enderror">
                    <span class="ml-2 text-sm text-gray-700">Inactive</span>
                </label>
            </div>

            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3 text-right"> <button type="submit"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded"
                id="closeEditModal">Cancel</button>
        </div>
    </div>
</form>

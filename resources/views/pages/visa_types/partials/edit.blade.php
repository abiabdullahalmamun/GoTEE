<form method="POST" id="editVisaTypeForm">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
                <input type="text" name="visatypeId" id="edit_visatypeid"  required hidden>
                <label for="name">Visa Type</label><br><input type="text" name="visa_type" id="edit_visatype"
                    class="form-control @error('visa_type') is-invalid @enderror w-full px-3 py-2 border rounded"
                    value="{{ old('visa_type') }}" placeholder="Enter Type..." required>
                @error('visa_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
      <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
            <div class="form-group flex-1">
               <label for="Contact">Symbol</label><br>
              <input type="text" name="symbol" id="edit_symbol" class="form-control @error('symbol') is-invalid @enderror w-full px-3 py-2 border rounded" value="{{ old('symbol') }}" placeholder="type" required>
                @error('mac')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group flex-1">
                <label for="endHr">Leadtime(Days)</label><br>
                <input type="number" name="days" id="edit_days" class="form-control @error('days') is-invalid @enderror w-full px-3 py-2 border rounded" value="{{ old('days') }}" placeholder="" required>
                @error('days')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div class="mb-4 w-full md:w-1/2 lg:w-1/3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="flex items-center space-x-3">
                <label class="inline-flex items-center">
                    <input type="radio" id="edit_status_active" name="status" value="1">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="radio" id="edit_status_inactive" name="status" value="0">
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

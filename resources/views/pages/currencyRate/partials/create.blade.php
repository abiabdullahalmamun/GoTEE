<form method="POST" id="createShopForm" action="{{ route('currencyRate.store') }}">
    @csrf
    @method('POST')
    <div class="row">
 
                <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group flex-1">
                <label for="startHr">Name</label><br>
                <input type="text" name="nameD"
                       class="form-control @error('nameD') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="RUPEE"  required readonly>
                @error('nameD')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group flex-1">
                <label for="startHr">Rate</label><br>
                <input type="number" name="rate"
                       class="form-control @error('rate') is-invalid @enderror w-full px-3 py-2 border rounded" step="any"
                       value="{{ old('rate') }}"  required>
                @error('rate')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
 
 
 
        <div class="mb-3 text-right">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeModal">Cancel</button>
        </div>
    </div>
</form>

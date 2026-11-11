<form method="POST" id="createShopForm" action="{{ route('sewingMachine.store') }}">
    @csrf
    @method('POST')
    <div class="row">
 
<div class="flex flex-row gap-3 mb-3 w-full">
    {{-- Designation --}}
    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="designation">Machine Type</label>
            <select class="form-control w-full px-3 py-2 border"
                    name="machineType" required id="machineType">
                @foreach($MachineType as $item)
                    <option value="{{ $item['id'] }}">
                        {{ $item['typeName'] }}
                    </option>
                @endforeach
            </select>
            @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Card ID --}}
    <div class="flex-1 min-w-0">
        <div class="form-group">
            <label for="designation">Machine Brand</label>
            <select class="form-control w-full px-3 py-2 border"
                    name="machineBrand" required id="machineBrand">
                @foreach($MachineBrand as $item)
                    <option value="{{ $item['id'] }}">
                        {{ $item['brandname'] }}
                    </option>
                @endforeach
            </select>
            @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

</div>


<div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
       <label for="name">Machine ID</label><br>
       <input type="text" name="machineId"
                       class="form-control @error('machine_id') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('machine_id') }}"  required>
         @error('machine_id')
                    <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
       <label for="name">Machine Model</label><br>
       <input type="text" name="machineModel"
                       class="form-control @error('machine_model') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('machine_model') }}"  required>
         @error('machine_model')
                    <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
       <label for="name">Description</label><br>
       <input type="text" name="description"
                       class="form-control @error('Description') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('Description') }}"  >
         @error('Description')
                    <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
       <label for="designation">Rented</label>
            <select class="form-control w-full px-3 py-2 border"
                    name="rent" required id="rent">
                <option value="0"> OWN</option>
                <option value="1"> RENTED</option>
                
            </select>
            @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
       <label for="name">Rent Reference</label><br>
       <input type="text" name="rentref"
                       class="form-control @error('Description') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('Description') }}"   >
         @error('Description')
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

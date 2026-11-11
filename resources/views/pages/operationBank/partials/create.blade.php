<form method="POST" id="createShopForm" action="{{ route('operationBank.store') }}">
    @csrf
    @method('POST')
    <div class="row">
 
<div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
       <label for="name">Operation Area</label><br>
       <select class="form-control w-full px-3 py-2 border" name="catA" required="required" id="catA">
            @foreach($cat as $item)
            <option value="{{ $item['Category_id'] }}">{{ $item['Category'] }}</option>
            @endforeach
        </select>     
        @error('devType')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Service dropdown, hidden by default --}}
<div class="flex flex-col w-full gap-1">

    <label for="edit_codeA" class="font-medium">
        Operation Code
    </label>

    <div class="flex gap-3 w-full">

        <div class="w-20 shrink-0">
            <input type="text"
                name="codeA"
                id="edit_codeA"
                class="form-control w-full px-3 py-2 border rounded text-center"
                maxlength="2"
                pattern="[0-9]{2}"
                inputmode="numeric"
                required
                readonly>
        </div>

        <div class="flex-1 min-w-0">
            <input type="text"
                name="codeB"
                
                class="form-control w-full px-3 py-2 border rounded"
                maxlength="3"
                pattern="[0-9]{3}"
                value="{{ old('codeB') }}"
                required>
        </div>

    </div>
</div>
 
<div class="mb-3 col-12 col-md-4 col-lg-6"  >
    <div class="form-group flex-1">
       <label for="name">Operation Name</label><br>
         <input type="text" name="name"   class="form-control @error('mac') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('mac') }}"  required>
        @error('mac')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-4 w-full md:w-1/2 lg:w-1/3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2"> Operation</label>
            <div class="flex items-center space-x-3">
                <label class="inline-flex items-center">
                    <input type="radio" name="defopn" value="1"
                        class="form-radio text-green-600 focus:ring-green-500 @error('status') border-red-500 @enderror"
                        checked>
                    <span class="ml-2 text-sm text-gray-700">Default</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="radio" name="defopn" value="0"
                        class="form-radio text-red-600 focus:ring-red-500 @error('status') border-red-500 @enderror">
                    <span class="ml-2 text-sm text-gray-700">Select</span>
                </label>
            </div>

            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
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

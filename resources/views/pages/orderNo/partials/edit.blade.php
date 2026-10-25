<form method="POST" id="editDeviceForm">
    @csrf
    @method('PUT')
    <div class="row">
       <div class="mb-3 col-12 col-md-4 col-lg-6">
        <div class="form-group flex-1">
            <label for="OrderCode" class="form-label">Order Ref</label><br>
        
            <input type="text"
                 name="OrderNo"
                   id="edit_OrderNo"
                   class="form-control @error('OrderNo') is-invalid @enderror w-full px-3 py-2 border rounded"
                   value="{{ old('OrderNo') }}"
                   required >
            @error('OrderNo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
      <label for="OrderNo" class="form-label">Order Code</label><br>
            <input type="text"
             name="OrderCode"
                   id="edit_OrderCode"

                   class="form-control @error('OrderCode') is-invalid @enderror w-full px-3 py-2 border rounded"
                   value="{{ old('OrderCode') }}"
                   required readonly>
            @error('OrderCode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
    </div>

    <div class="form-group flex-1">
                 
                <label for="attr" class="form-label">Attribute</label>
                <input type="text"
                       name="attr"
                       id="edit_attr"
                       class="form-control @error('attr') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('attr') }}">
                @error('attr')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
    </div>
</div>
 <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="Style" class="form-label">Style</label>
        <input type="text"
               name="Style"
               id="edit_Style"
               class="form-control @error('Style') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('Style') }}">
        @error('Style')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
   
    <div class="form-group flex-1">
        <label for="buyer" class="form-label">Buyer</label>
         <select class="form-control w-full px-3 py-2 border" name="buyer" required="required" id="edit_buyer">
        <option value=""></option>
        @foreach($buyer as $item)
        <option value="{{ $item['id'] }}">{{ $item['buyer'] }}</option>
        @endforeach
        </select>     
        @error('buyer')
            <div class="invalid-feedback">{{ $message }}</div>
         @enderror
        </div>
        
</div>
<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="totalsmv" class="form-label">Total SMV</label>
        <input type="number"
               step="0.01"
               name="totalsmv"
               id="edit_totalsmv"
               class="form-control @error('totalsmv') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('totalsmv') }}">
        @error('totalsmv')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group flex-1">
        <label for="item" class="form-label">Item</label>
         <select class="form-control w-full px-3 py-2 border" name="item" required="required" id="edit_item">
        <option value=""></option>
        @foreach($itemList as $item)
        <option value="{{ $item['id'] }}">{{ $item['itemName'] }}</option>
        @endforeach
        </select>     
 
        @error('item')
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

<form method="POST" id="createShopForm" action="{{ route('orders.store') }}">
    @csrf
 @method('POST')
 
    <div class="row">

      <div class="mb-3 col-12 col-md-4 col-lg-6">
        <div class="form-group flex-1">
            <label for="OrderCode" class="form-label">Order Ref</label><br>
        
            <input type="text"
                 name="OrderNo"
                   id="OrderNo"
                   class="form-control @error('OrderNo') is-invalid @enderror w-full px-3 py-2 border rounded"
                   value="{{ old('OrderNo') }}"
                   required>
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
                   id="OrderCode"

                   class="form-control @error('OrderCode') is-invalid @enderror w-full px-3 py-2 border rounded"
                   value="{{ old('OrderCode') }}"
                   required>
            @error('OrderCode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
    </div>

    <div class="form-group flex-1">
                 
                <label for="attr" class="form-label">Attribute</label>
                <input type="text"
                       name="attr"
                       id="attr"
                       class="form-control @error('attr') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('attr') }}">
                @error('attr')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
    </div>
</div>
 

        {{-- Style --}}
<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="Style" class="form-label">Style</label>
        <input type="text"
               name="Style"
               id="Style"
               class="form-control @error('Style') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('Style') }}">
        @error('Style')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
   
    <div class="form-group flex-1">
        <label for="buyer" class="form-label">Buyer</label>
         <select class="form-control w-full px-3 py-2 border" name="buyer" required="required" id="buyer">
        <option value=""></option>
        @foreach($buyer as $item)
        <option value="{{ $item['id'] }}">{{ $item['buyer'] }}</option>
        @endforeach
        </select>     
        @error('buyer')
            <div class="invalid-feedback">{{ $message }}</div>
         @enderror
        </div>
        {{-- Buyer --}}
  

</div>
        {{-- Total SMV --}}
<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="totalsmv" class="form-label">Total SMV</label>
        <input type="number"
               step="0.01"
               name="totalsmv"
               id="totalsmv"
               class="form-control @error('totalsmv') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('totalsmv') }}">
        @error('totalsmv')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group flex-1">
        <label for="item" class="form-label">Item</label>
         <select class="form-control w-full px-3 py-2 border" name="item" required="required" id="item">
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

        {{-- Status --}}
      <div class="mb-3 col-12 col-md-4 col-lg-6">
    <div class="form-group flex-1">
            <label class="form-label d-block">Status</label>

            <div class="d-flex align-items-center gap-3">
                <label class="form-check-label">
                    <input type="radio"
                           name="status"
                           value="1"
                           class="form-check-input"
                           {{ old('status', '1') == '1' ? 'checked' : '' }}>
                    Active
                </label>

                <label class="form-check-label">
                    <input type="radio"
                           name="status"
                           value="0"
                           class="form-check-input"
                           {{ old('status') === '0' ? 'checked' : '' }}>
                    Inactive
                </label>
            </div>

            @error('status')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

        {{-- Buttons --}}
         <div class="mb-3 text-right">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded" id="closeModal">Cancel</button>
        </div>
 

    </div>
</form>

<form method="POST" id="editCenterForm">
    @csrf
    @method('PUT')
    <div class="row">

        <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
      <label for="name">Center</label><br>
       <select class="form-control w-full px-3 py-2 border" name="cenId" required="required" id="edit_cenId">
        <option value=""></option>
        @foreach($centerList as $item)
        <option value="{{ $item['id'] }}">{{ $item['center_name'] }}</option>
        @endforeach
        </select>     
        @error('center_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group flex-1">
        <input type="text" name="counterId" id="edit_counterId"  required hidden>
        <label for="startHr">Counter No</label><br>
        <input type="number" name="counterNo" id="edit_counterNo"
               class="form-control @error('counterNo') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('counterNo') }}" readonly required>
        @error('counterNo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="startHr">CounterName</label><br>
        <input type="text" name="counterName" id="edit_counterName"
               class="form-control @error('counterName') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('counterName') }}" placeholder="Enter counterName..." required>
        @error('counterName')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
 <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
       <label for="Contact">MAC</label><br>
      <input type="text" name="mac" id="edit_mac"
            class="form-control @error('mac') is-invalid @enderror w-full px-3 py-2 border rounded"
            value="{{ old('mac') }}" placeholder="aa:bb:cc:dd:ee:ff" required>
        @error('mac')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group flex-1">
        <label for="endHr">IP</label><br>
        <input type="text" name="ip" id="edit_ip"
               class="form-control @error('ip') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('ip') }}" placeholder="192.168.1.11" >
        @error('ip')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="startHr">Hostname</label><br>
        <input type="text" name="hostname" id="edit_host"
               class="form-control @error('hostname') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('hostname') }}" placeholder="Enter hostname..." >
        @error('hostname')
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
                class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Update</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded"
                id="closeEditModal">Cancel</button>
        </div>
    </div>
</form>

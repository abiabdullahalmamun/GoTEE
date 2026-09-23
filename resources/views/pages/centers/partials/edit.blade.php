<form method="POST" id="editCenterForm">
    @csrf
    @method('PUT')
    <div class="row">

        <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
      <label for="name">Region</label><br>
       <select class="form-control w-full px-3 py-2 border" name="regionId" required="required" id="edit_regionId">
        <option value=""></option>
        @foreach($regionList as $item)
        <option value="{{ $item['id'] }}">{{ $item['region_name'] }}</option>
        @endforeach
        </select>     
        @error('region_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group flex-1">
        <input type="text" name="cenId" id="edit_cenId"  required hidden>
       <label for="Contact">Center Name</label><br>
      <input type="text" name="centerName" id="edit_centername"
            class="form-control @error('centerName') is-invalid @enderror w-full px-3 py-2 border rounded"
            value="{{ old('centerName') }}" placeholder="Enter Name..." required>
        @error('centerName')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="startHr">Start Operation</label><br>
        <input type="text" name="startHr" id="edit_startHr"
               class="form-control @error('startHr') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('startHr') }}" placeholder="HH:mm" required>
        @error('startHr')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group flex-1">
        <label for="endHr">End Operation</label><br>
        <input type="text" name="endHr" id="edit_endHr"
               class="form-control @error('endHr') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('endHr') }}" placeholder="HH:mm" required>
        @error('endHr')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="startHr">Before Tolerance</label><br>
        <input type="text" name="apt_tol" id="edit_apt_tol"
               class="form-control @error('apt_tol') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('apt_tol') }}" placeholder="Enter Minute..." required>
        @error('apt_tol')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group flex-1">
        <label for="endHr">After Tolerance</label><br>
        <input type="text" name="end_tol" id="edit_end_tol"
               class="form-control @error('del_time') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('end_tol') }}" placeholder="Enter Minute..." required>
        @error('end_tol')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
            <label for="Contact">Gateway Name</label><br>
              <input type="text" name="gtw_name" id="edit_gtw_name"
                    class="form-control @error('gtw_name') is-invalid @enderror w-full px-3 py-2 border rounded"
                    value="{{ old('gtw_name') }}" placeholder="Name As in Payment Gateway..." required>
                @error('gtw_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
        <!-- <div class="mb-3 col-12 col-md-4 col-lg-6"> -->

            <div class="form-group flex-1">
        <label for="endHr">Delivery Time</label><br>
        <input type="text" name="del_time" id="edit_del_time"
               class="form-control @error('del_time') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('del_time') }}" placeholder="HH:mm - HH:mm" required>
        @error('del_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
            <div class="form-group flex-1">
            <label for="Contact">Hotline</label><br>
              <input type="text" name="hotline" id="edit_hotline"
                    class="form-control @error('hotline') is-invalid @enderror w-full px-3 py-2 border rounded"
                    value="{{ old('hotline') }}" placeholder="" >
                @error('hotline')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
            <label for="Contact">Receipt Info</label><br>
              <input type="text" name="info" id="edit_info"
                    class="form-control @error('info') is-invalid @enderror w-full px-3 py-2 border rounded"
                    value="{{ old('info') }}" placeholder="" >
                @error('info')
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

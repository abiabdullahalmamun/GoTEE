<form method="POST" id="editDeviceForm">
    @csrf
    @method('PUT')
    <div class="row">
         <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group flex-1">
               <label for="name">Center</label><br>
                <input type="text" name="recId" id="edit_recId" hidden>
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
        </div>
          <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
        <!-- <div class="mb-3 col-12 col-md-4 col-lg-6"> -->
            <div class="form-group flex-1">
               <label for="name">Device Type</label><br>
               <select class="form-control w-full px-3 py-2 border" name="devType" required="required" id="edit_devType">
                    <option value=""></option>
                    <option value="1">Linker</option>
                    <option value="2">Mender</option>
                </select>     
                @error('devType')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
              <div class="form-group flex-1">
               <label for="name">Device Type</label><br>
               <select class="form-control w-full px-3 py-2 border" name="led" required="required" id="edit_led">
                    <option value=""></option>
                    <option value="1">Green</option>
                    <option value="2">Red</option>
                    <option value="3">Blue</option>
                    <option value="4">Red+Blue</option>
                    <option value="5">Green+Red</option>
                    <option value="6">Green+Blue</option>
                    <option value="7">Off</option>
                    <option value="8">R+B+G</option>
                </select>     
                @error('opstate')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
       <div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
   
     <!-- <div class="mb-3 col-12 col-md-4 col-lg-6"> -->
            <div class="form-group flex-1">
                <label for="startHr">MAC</label><br>
                <input type="text" name="mac" id="edit_mac"
                       class="form-control @error('mac') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('mac') }}"  required>
                @error('mac')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
<!--         </div>
       <div class="mb-3 col-12 col-md-4 col-lg-6"> -->
            <div class="form-group flex-1">
                <label for="startHr">IP</label><br>
                <input type="text" name="ip" id="edit_ip"
                       class="form-control @error('ip') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('ip') }}"  >
                @error('ip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group flex-1">
                <label for="startHr">Location</label><br>
                <input type="text" name="location" id="edit_location"
                       class="form-control @error('location') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('location') }}"  >
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
         <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group flex-1">
                <label for="startHr">Message</label><br>
                <input type="text" name="message" id="edit_message"
                       class="form-control @error('location') is-invalid @enderror w-full px-3 py-2 border rounded"
                       value="{{ old('message') }}"  >
                @error('message')
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

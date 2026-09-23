<form method="POST" id="createRejectForm" action="{{ route('ports.store') }}">
    @csrf
    @method('POST')
    <div class="row">
        

<div class="mb-3 col-12 col-md-4 col-lg-6 flex space-x-4">
    <div class="form-group flex-1">
        <label for="startHr">Port Name</label><br>
        <input type="text" name="portName"
               class="form-control @error('portName') is-invalid @enderror w-full px-3 py-2 border rounded"
               value="{{ old('portName') }}" placeholder="Enter port name..." required>
        @error('portName')
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

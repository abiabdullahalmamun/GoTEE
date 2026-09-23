<form method="POST" id="editStickerTypeForm">
    @csrf
    @method('PUT')
    <div class="row">
                <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
            <label for="StickerInfo" class="form-label d-block mb-1">Sticker Info</label>

            <!-- <label class="form-label">Sticker Info</label> -->
            <input type="text"
                   name="StickerInfo"
                     id="edit_StickerInfo"
                   class="form-control @error('StickerInfo') is-invalid @enderror"
                   value="{{ old('StickerInfo') }}"
                   placeholder="Enter Sticker Info"
                   required>

            @error('StickerInfo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

            <!-- Sticker -->
       <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
            <label class="form-label">Sticker</label>
            <input type="text"
                   name="sticker"
                   id="edit_sticker"
                   class="form-control @error('sticker') is-invalid @enderror"
                   value="{{ old('sticker') }}"
                   placeholder="Enter Sticker"
                   required>
            <input type="number" name="stcId" id="edit_stcId" hidden required>
            @error('sticker')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

        <!-- Center -->
      <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
            <label class="form-label">Center</label>
            <select name="center_id"
                    id="edit_center"
                    class="form-control @error('center_id') is-invalid @enderror"
                    required>
                <option value="">Select Center</option>

                @foreach($centers as $center)
                    <option value="{{ $center->id }}"
                        {{ old('center_id') == $center->id ? 'selected' : '' }}>
                        {{ $center->center_name }}
                    </option>
                @endforeach
            </select>

            @error('center_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
       <div class="mb-3 col-12 col-md-4 col-lg-6">
            <div class="form-group">
            <label class="form-label">Remarks</label>
            <input name="remarks"
                    id="edit_remarks"
                      class="form-control @error('remarks') is-invalid @enderror"
                      placeholder="Remarks">

            @error('remarks')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

 

        <div class="mb-3 text-right"> <button type="submit"
                class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded"
                id="closeEditModal">Cancel</button>
        </div>
    </div>
</form>
